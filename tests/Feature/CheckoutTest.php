<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /** Inicia el checkout y devuelve la referencia del pago simulado. */
    private function startCheckout(Book $book, int $quantity = 1): string
    {
        $this->post(route('cart.store', $book), ['quantity' => $quantity]);

        $response = $this->post(route('checkout.store'), [
            'buyer_name' => 'Ada Lovelace',
            'buyer_email' => 'ada@example.com',
        ]);

        $response->assertRedirectContains('/checkout/simular/');

        return Str::afterLast(parse_url($response->headers->get('Location'), PHP_URL_PATH), '/');
    }

    public function test_an_approved_payment_registers_the_order_and_clears_the_cart(): void
    {
        $book = Book::factory()->create(['price' => 50, 'stock' => 5]);

        $reference = $this->startCheckout($book, 2);

        $this->post(route('checkout.simulate.process', $reference), ['decision' => 'approve'])
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);

        $order = Order::with('items')->first();
        $this->assertSame('approved', $order->status);
        $this->assertEquals(100.00, (float) $order->total);
        $this->assertSame('Ada Lovelace', $order->buyer_name);
        $this->assertCount(1, $order->items);
        $this->assertSame(2, $order->items->first()->quantity);

        // El stock se descuenta y el carrito queda vacío.
        $this->assertSame(3, $book->fresh()->stock);
        $this->get(route('cart.index'))->assertSee('Tu carrito está vacío');
    }

    public function test_a_rejected_payment_does_not_register_any_order(): void
    {
        $book = Book::factory()->create(['price' => 50, 'stock' => 5]);

        $reference = $this->startCheckout($book, 1);

        $this->post(route('checkout.simulate.process', $reference), ['decision' => 'reject'])
            ->assertRedirect(route('checkout.failure'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(5, $book->fresh()->stock); // sin descuento de stock
    }

    public function test_the_order_appears_in_my_purchases_after_approval(): void
    {
        $book = Book::factory()->create(['title' => 'Pedro Páramo', 'slug' => 'pedro-paramo', 'price' => 40, 'stock' => 3]);

        $reference = $this->startCheckout($book, 1);
        $this->post(route('checkout.simulate.process', $reference), ['decision' => 'approve']);

        $order = Order::first();

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Pedido #'.$order->id)
            ->assertSee('Pedro Páramo');
    }
}
