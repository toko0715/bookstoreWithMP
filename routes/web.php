<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Catálogo
|--------------------------------------------------------------------------
*/
Route::get('/up', fn () => response()->noContent())->name('health');

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/libros/{book}', [CatalogController::class, 'show'])->name('catalog.show');

/*
|--------------------------------------------------------------------------
| Carrito
|--------------------------------------------------------------------------
*/
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/{book}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/carrito/{book}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{book}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/carrito', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout (Mercado Pago Sandbox + pago simulado de respaldo)
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/pago', [CheckoutController::class, 'pay'])->name('checkout.pay');

Route::get('/checkout/exito', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/error', [CheckoutController::class, 'failure'])->name('checkout.failure');
Route::get('/checkout/pendiente', [CheckoutController::class, 'pending'])->name('checkout.pending');

Route::get('/checkout/simular/{reference}', [CheckoutController::class, 'simulate'])->name('checkout.simulate');
Route::post('/checkout/simular/{reference}', [CheckoutController::class, 'simulateProcess'])->name('checkout.simulate.process');

Route::post('/webhooks/mercadopago', [CheckoutController::class, 'webhook'])->name('webhook.mercadopago');

/*
|--------------------------------------------------------------------------
| Mis compras
|--------------------------------------------------------------------------
*/
Route::get('/mis-compras', [OrderController::class, 'index'])->name('orders.index');
Route::get('/mis-compras/{order}', [OrderController::class, 'show'])->name('orders.show');
