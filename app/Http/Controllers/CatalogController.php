<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $books = Book::query()
            ->search($q)
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('catalog.index', [
            'books' => $books,
            'q' => $q,
        ]);
    }

    public function show(Book $book)
    {
        $related = Book::query()
            ->where('id', '!=', $book->id)
            ->when($book->category, fn ($query) => $query->where('category', $book->category))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = Book::query()
                ->where('id', '!=', $book->id)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return view('catalog.show', [
            'book' => $book,
            'related' => $related,
        ]);
    }
}
