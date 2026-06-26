<?php

namespace App\Support;

use App\Models\Book;
use Illuminate\Support\Collection;

/**
 * Carrito de compras basado en sesión (no requiere autenticación).
 *
 * Cada línea se guarda indexada por el id del libro con un snapshot de los
 * datos relevantes, de modo que el precio mostrado no cambie si luego se
 * actualiza el catálogo.
 */
class Cart
{
    protected string $key = 'cart';

    /** @return array<int,array<string,mixed>> */
    public function raw(): array
    {
        return session($this->key, []);
    }

    protected function persist(array $items): void
    {
        session([$this->key => $items]);
    }

    public function add(Book $book, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $items = $this->raw();

        $existing = $items[$book->id]['quantity'] ?? 0;
        $newQuantity = $existing + $quantity;

        if ($book->stock > 0) {
            $newQuantity = min($newQuantity, $book->stock);
        }

        $items[$book->id] = [
            'book_id' => $book->id,
            'slug' => $book->slug,
            'title' => $book->title,
            'author' => $book->author,
            'unit_price' => (float) $book->price,
            'cover_url' => $book->cover_url,
            'stock' => (int) $book->stock,
            'quantity' => $newQuantity,
        ];

        $this->persist($items);
    }

    public function update(int $bookId, int $quantity): void
    {
        $items = $this->raw();

        if (! isset($items[$bookId])) {
            return;
        }

        if ($quantity <= 0) {
            unset($items[$bookId]);
            $this->persist($items);

            return;
        }

        $stock = (int) ($items[$bookId]['stock'] ?? 0);
        if ($stock > 0) {
            $quantity = min($quantity, $stock);
        }

        $items[$bookId]['quantity'] = $quantity;
        $this->persist($items);
    }

    public function remove(int $bookId): void
    {
        $items = $this->raw();
        unset($items[$bookId]);
        $this->persist($items);
    }

    public function clear(): void
    {
        session()->forget($this->key);
    }

    /** @return Collection<int,object> líneas con line_total calculado */
    public function items(): Collection
    {
        return collect($this->raw())
            ->map(function (array $item) {
                $item['line_total'] = round($item['unit_price'] * $item['quantity'], 2);

                return (object) $item;
            })
            ->values();
    }

    public function isEmpty(): bool
    {
        return count($this->raw()) === 0;
    }

    /** Cantidad total de unidades (para el badge del carrito). */
    public function count(): int
    {
        return (int) collect($this->raw())->sum('quantity');
    }

    public function subtotal(): float
    {
        return round(
            collect($this->raw())->sum(fn (array $i) => $i['unit_price'] * $i['quantity']),
            2
        );
    }

    public function total(): float
    {
        // Tienda de ejemplo: sin gastos de envío ni impuestos adicionales.
        return $this->subtotal();
    }

    /**
     * Snapshot inmutable usado para registrar la orden cuando el pago se aprueba.
     *
     * @return array<string,mixed>
     */
    public function snapshot(): array
    {
        return [
            'items' => $this->items()->map(fn (object $i) => [
                'book_id' => $i->book_id,
                'title' => $i->title,
                'author' => $i->author,
                'unit_price' => $i->unit_price,
                'quantity' => $i->quantity,
                'line_total' => $i->line_total,
                'cover_url' => $i->cover_url,
            ])->all(),
            'subtotal' => $this->subtotal(),
            'total' => $this->total(),
            'currency' => config('store.currency'),
        ];
    }
}
