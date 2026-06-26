<x-layout title="¡Compra exitosa!">
    <div class="mx-auto max-w-2xl">
        <div class="text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            </div>
            <h1 class="mt-5 font-serif text-3xl font-semibold text-stone-900">¡Gracias por tu compra!</h1>
            <p class="mt-2 text-stone-500">Tu pago fue aprobado y registramos tu pedido.</p>
        </div>

        <div class="card mt-8">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 p-6">
                <div>
                    <p class="text-xs text-stone-400">Pedido</p>
                    <p class="font-medium text-stone-900">#{{ $order->id }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400">Fecha</p>
                    <p class="font-medium text-stone-900">{{ $order->paid_at?->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <span class="badge {{ $order->statusClasses() }}">{{ $order->statusLabel() }}</span>
            </div>

            <div class="divide-y divide-stone-100">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 p-4 sm:px-6">
                        <x-book-cover :title="$item->title" :author="$item->author" :cover="$item->cover_url" class="h-16 w-12 rounded-md" />
                        <div class="flex-1">
                            <p class="text-sm font-medium text-stone-900">{{ $item->title }}</p>
                            <p class="text-xs text-stone-500">{{ $item->quantity }} × {{ \App\Support\Money::format($item->unit_price) }}</p>
                        </div>
                        <x-price :value="$item->line_total" class="text-sm font-semibold text-stone-900" />
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between border-t border-stone-200 p-6">
                <span class="font-semibold text-stone-900">Total pagado</span>
                <x-price :value="$order->total" class="text-xl font-bold text-stone-900" />
            </div>
        </div>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-primary">Ver mis compras</a>
            <a href="{{ route('catalog.index') }}" class="btn btn-ghost">Seguir comprando</a>
        </div>
    </div>
</x-layout>
