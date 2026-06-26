<x-layout title="Finalizar compra">
    <a href="{{ route('cart.index') }}" class="text-sm text-stone-500 hover:text-stone-700">← Volver al carrito</a>
    <h1 class="mb-6 mt-3 font-serif text-3xl font-semibold text-stone-900">Finalizar compra</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-start">
        @csrf
        <div class="space-y-6">
            <section class="card p-6">
                <h2 class="font-serif text-lg font-semibold text-stone-900">
                    Datos del comprador
                    <span class="font-sans text-sm font-normal text-stone-400">(opcional)</span>
                </h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="buyer_name" class="mb-1.5 block text-sm text-stone-600">Nombre</label>
                        <input id="buyer_name" name="buyer_name" value="{{ old('buyer_name') }}" class="field" placeholder="Tu nombre">
                        @error('buyer_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="buyer_email" class="mb-1.5 block text-sm text-stone-600">Correo electrónico</label>
                        <input id="buyer_email" name="buyer_email" type="email" value="{{ old('buyer_email') }}" class="field" placeholder="tucorreo@ejemplo.com">
                        @error('buyer_email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="card">
                <h2 class="p-6 pb-2 font-serif text-lg font-semibold text-stone-900">Tu pedido</h2>
                <div class="divide-y divide-stone-100">
                    @foreach ($items as $item)
                        <div class="flex items-center gap-4 px-6 py-4">
                            <x-book-cover :title="$item->title" :author="$item->author" :cover="$item->cover_url" class="h-16 w-12 rounded-md" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-stone-900">{{ $item->title }}</p>
                                <p class="text-xs text-stone-500">{{ $item->quantity }} × {{ \App\Support\Money::format($item->unit_price) }}</p>
                            </div>
                            <x-price :value="$item->line_total" class="text-sm font-semibold text-stone-900" />
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="card sticky top-28 p-6">
            <h2 class="font-serif text-lg font-semibold text-stone-900">Resumen</h2>
            <dl class="mt-4 space-y-2.5 text-sm">
                <div class="flex justify-between"><dt class="text-stone-500">Subtotal</dt><dd class="font-medium text-stone-800">{{ \App\Support\Money::format($subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Envío</dt><dd class="font-medium text-emerald-600">Gratis</dd></div>
            </dl>
            <div class="mt-4 flex items-center justify-between border-t border-stone-200 pt-4">
                <span class="font-semibold text-stone-900">Total</span>
                <x-price :value="$total" class="text-xl font-bold text-stone-900" />
            </div>

            <button class="btn btn-primary mt-6 w-full">
                {{ $mercadoPagoEnabled ? 'Continuar al pago con Mercado Pago' : 'Pagar (modo demo)' }}
            </button>

            <p class="mt-3 flex items-center justify-center gap-1.5 text-xs text-stone-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                Pago {{ $mercadoPagoEnabled ? 'embebido con Mercado Pago Bricks' : 'simulado para demostración' }}
            </p>
        </aside>
    </form>
</x-layout>
