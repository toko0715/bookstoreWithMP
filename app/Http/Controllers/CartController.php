<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly Cart $cart)
    {
    }

    public function index()
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'total' => $this->cart->total(),
        ]);
    }

    public function store(Request $request, Book $book)
    {
        if ($book->isOutOfStock()) {
            return back()->with('error', "«{$book->title}» está agotado.");
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        $this->cart->add($book, $quantity);

        return redirect()
            ->route('cart.index')
            ->with('status', "«{$book->title}» se agregó al carrito.");
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($book->id, $data['quantity']);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Carrito actualizado.');
    }

    public function destroy(Book $book)
    {
        $this->cart->remove($book->id);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        $this->cart->clear();

        return redirect()
            ->route('cart.index')
            ->with('status', 'Vaciaste el carrito.');
    }
}
