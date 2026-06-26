<x-layout title="Carrito">
    <h1 class="mb-6 font-serif text-3xl font-semibold text-stone-900">Tu carrito</h1>

    @if ($items->isEmpty())
        <x-empty-state title="Tu carrito está vacío" message="Agrega algunos libros del catálogo para continuar.">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Explorar catálogo</a>
        </x-empty-state>
    @else
        <div class="grid gap-8 lg:grid-cols-[1fr_340px] lg:items-start">
            <div class="space-y-4">
                @foreach ($items as $item)
                    <div class="card flex gap-4 p-4">
                        <a href="{{ route('catalog.show', $item->slug) }}" class="shrink-0">
                            <x-book-cover :title="$item->title" :author="$item->author" :cover="$item->cover_url"
                                          class="h-28 w-20 rounded-lg" />
                        </a>
                        <div class="flex flex-1 flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('catalog.show', $item->slug) }}" class="font-medium text-stone-900 hover:underline">{{ $item->title }}</a>
                                    <p class="text-sm text-stone-500">{{ $item->author }}</p>
                                </div>
                                <form action="{{ route('cart.destroy', $item->slug) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg p-1.5 text-stone-400 transition hover:bg-stone-100 hover:text-rose-600" title="Eliminar">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </form>
                            </div>
                            <div class="mt-auto flex items-end justify-between pt-4">
                                <form action="{{ route('cart.update', $item->slug) }}" method="POST" class="flex items-center rounded-lg bg-white ring-1 ring-inset ring-stone-200">
                                    @csrf
                                    @method('PATCH')
                                    <button name="quantity" value="{{ $item->quantity - 1 }}" class="px-3 py-1.5 text-lg leading-none text-stone-500 transition hover:text-stone-900" aria-label="Disminuir">−</button>
                                    <span class="w-8 text-center text-sm font-semibold text-stone-900">{{ $item->quantity }}</span>
                                    <button name="quantity" value="{{ $item->quantity + 1 }}" class="px-3 py-1.5 text-lg leading-none text-stone-500 transition hover:text-stone-900" aria-label="Aumentar">+</button>
                                </form>
                                <div class="text-right">
                                    <x-price :value="$item->line_total" class="block font-semibold text-stone-900" />
                                    <p class="text-xs text-stone-400">{{ \App\Support\Money::format($item->unit_price) }} c/u</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('cart.clear') }}" method="POST" class="pt-1">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm font-medium text-stone-500 transition hover:text-rose-600">Vaciar carrito</button>
                </form>
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
                <a href="{{ route('checkout.index') }}" class="btn btn-primary mt-6 w-full">Continuar al pago</a>
                <a href="{{ route('catalog.index') }}" class="btn btn-ghost mt-2 w-full">Seguir comprando</a>
            </aside>
        </div>
    @endif
</x-layout>
