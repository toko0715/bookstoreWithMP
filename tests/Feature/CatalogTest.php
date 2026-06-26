<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_books(): void
    {
        $book = Book::factory()->create(['title' => 'Cien años de soledad', 'slug' => 'cien-anos']);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Cien años de soledad')
            ->assertSee($book->author);
    }

    public function test_search_filters_by_title(): void
    {
        Book::factory()->create(['title' => 'El Aleph', 'slug' => 'el-aleph']);
        Book::factory()->create(['title' => 'Rayuela', 'slug' => 'rayuela']);

        $this->get(route('catalog.index', ['q' => 'aleph']))
            ->assertOk()
            ->assertSee('El Aleph')
            ->assertDontSee('Rayuela');
    }

    public function test_book_detail_page_renders(): void
    {
        $book = Book::factory()->create(['stock' => 5]);

        $this->get(route('catalog.show', $book))
            ->assertOk()
            ->assertSee($book->title)
            ->assertSee($book->author)
            ->assertSee('Agregar al carrito');
    }
}
