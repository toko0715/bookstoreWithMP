<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_book_can_be_added_to_the_cart(): void
    {
        $book = Book::factory()->create(['price' => 50, 'stock' => 10]);

        $this->post(route('cart.store', $book), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee($book->title)
            ->assertSee('S/ 100.00'); // 50 x 2
    }

    public function test_quantities_can_be_updated(): void
    {
        $book = Book::factory()->create(['price' => 50, 'stock' => 10]);

        $this->post(route('cart.store', $book), ['quantity' => 1]);
        $this->patch(route('cart.update', $book), ['quantity' => 4]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('S/ 200.00'); // 50 x 4
    }

    public function test_an_item_can_be_removed(): void
    {
        $book = Book::factory()->create(['stock' => 10]);

        $this->post(route('cart.store', $book));
        $this->delete(route('cart.destroy', $book));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Tu carrito está vacío');
    }

    public function test_an_out_of_stock_book_cannot_be_added(): void
    {
        $book = Book::factory()->outOfStock()->create();

        $this->post(route('cart.store', $book))
            ->assertRedirect();

        $this->get(route('cart.index'))
            ->assertSee('Tu carrito está vacío');
    }
}
