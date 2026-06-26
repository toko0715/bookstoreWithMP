<x-layout title="Mis compras">
    <h1 class="mb-6 font-serif text-3xl font-semibold text-stone-900">Mis compras</h1>

    @if ($orders->isEmpty())
        <x-empty-state title="Aún no tienes compras" message="Cuando completes un pago aprobado, tus pedidos aparecerán aquí.">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Ir al catálogo</a>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="card block p-5 transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <div class="hidden h-12 w-12 place-items-center rounded-xl bg-stone-100 text-stone-500 sm:grid">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" /></svg>
                            </div>
                            <div>
                                <p class="font-medium text-stone-900">Pedido #{{ $order->id }}</p>
                                <p class="text-sm text-stone-500">{{ $order->paid_at?->translatedFormat('d \d\e F \d\e Y') }} · {{ $order->totalQuantity() }} artículo(s)</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="badge {{ $order->statusClasses() }}">{{ $order->statusLabel() }}</span>
                            <x-price :value="$order->total" class="text-lg font-bold text-stone-900" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2 border-t border-stone-100 pt-4">
                        @foreach ($order->items->take(5) as $item)
                            <span class="chip">{{ $item->quantity }}× {{ str($item->title)->limit(28) }}</span>
                        @endforeach
                        @if ($order->items->count() > 5)
                            <span class="chip">+{{ $order->items->count() - 5 }} más</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</x-layout>
