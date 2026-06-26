<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): Order
    {
        $order = Order::create([
            'external_reference' => (string) Str::uuid(),
            'status' => 'approved',
            'total' => 59.90,
            'currency' => 'PEN',
            'buyer_name' => 'Jorge Luis Borges',
            'paid_at' => now(),
        ]);

        $order->items()->create([
            'title' => 'Ficciones',
            'author' => 'Jorge Luis Borges',
            'unit_price' => 59.90,
            'quantity' => 1,
            'line_total' => 59.90,
        ]);

        return $order;
    }

    public function test_my_purchases_index_lists_orders(): void
    {
        $order = $this->makeOrder();

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Pedido #'.$order->id)
            ->assertSee('Ficciones');
    }

    public function test_an_order_detail_can_be_viewed(): void
    {
        $order = $this->makeOrder();

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Ficciones')
            ->assertSee('S/ 59.90');
    }

    public function test_my_purchases_is_empty_without_orders(): void
    {
        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Aún no tienes compras');
    }
}
