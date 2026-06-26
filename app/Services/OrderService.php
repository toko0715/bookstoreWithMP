<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Orquesta el registro de una compra. Una orden SÓLO se persiste cuando el
 * pago está aprobado; mientras tanto el snapshot del carrito vive en caché
 * (accesible tanto desde el redirect del navegador como desde el webhook).
 */
class OrderService
{
    public function cacheKey(string $reference): string
    {
        return "checkout:{$reference}";
    }

    /** Guarda el snapshot del carrito a la espera de la confirmación del pago. */
    public function stash(string $reference, array $payload): void
    {
        Cache::put($this->cacheKey($reference), $payload, now()->addHours(2));
    }

    /** @return array<string,mixed>|null */
    public function pending(string $reference): ?array
    {
        return Cache::get($this->cacheKey($reference));
    }

    public function forget(string $reference): void
    {
        Cache::forget($this->cacheKey($reference));
    }

    /**
     * Crea (de forma idempotente) una orden aprobada a partir del snapshot en
     * caché. Devuelve null si no hay nada que registrar.
     *
     * @param  array<string,mixed>  $payment  datos del pago (Mercado Pago o simulado)
     */
    public function createApprovedFromCache(?string $reference, array $payment = []): ?Order
    {
        if (! $reference) {
            return null;
        }

        return DB::transaction(function () use ($reference, $payment) {
            // Idempotencia: el redirect y el webhook pueden llegar ambos.
            if ($existing = Order::where('external_reference', $reference)->first()) {
                return $existing->load('items');
            }

            $payload = $this->pending($reference);
            if (! $payload) {
                return null;
            }

            $order = Order::create([
                'external_reference' => $reference,
                'status' => 'approved',
                'total' => $payload['total'],
                'currency' => $payload['currency'] ?? config('store.currency'),
                'buyer_name' => $payload['buyer_name'] ?? null,
                'buyer_email' => $payload['buyer_email'] ?? ($payment['payer_email'] ?? null),
                'payment_id' => $payment['id'] ?? null,
                'payment_method' => $payment['payment_method'] ?? null,
                'paid_at' => now(),
            ]);

            foreach ($payload['items'] as $item) {
                $order->items()->create([
                    'book_id' => $item['book_id'] ?? null,
                    'title' => $item['title'],
                    'author' => $item['author'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'] ?? round($item['unit_price'] * $item['quantity'], 2),
                    'cover_url' => $item['cover_url'] ?? null,
                ]);

                if (! empty($item['book_id'])) {
                    Book::where('id', $item['book_id'])
                        ->where('stock', '>=', $item['quantity'])
                        ->decrement('stock', $item['quantity']);
                }
            }

            $this->forget($reference);

            return $order->load('items');
        });
    }
}
