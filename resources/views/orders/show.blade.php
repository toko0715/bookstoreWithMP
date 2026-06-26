<x-layout :title="'Pedido #'.$order->id">
    <a href="{{ route('orders.index') }}" class="text-sm text-stone-500 hover:text-stone-700">← Mis compras</a>
    <div class="mb-6 mt-3 flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-serif text-3xl font-semibold text-stone-900">Pedido #{{ $order->id }}</h1>
        <span class="badge {{ $order->statusClasses() }}">{{ $order->statusLabel() }}</span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px] lg:items-start">
        <section class="card">
            <div class="divide-y divide-stone-100">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 p-4 sm:px-6">
                        @if ($item->book)
                            <a href="{{ route('catalog.show', $item->book->slug) }}" class="shrink-0">
                                <x-book-cover :title="$item->title" :author="$item->author" :cover="$item->cover_url" class="h-20 w-14 rounded-md" />
                            </a>
                        @else
                            <x-book-cover :title="$item->title" :author="$item->author" :cover="$item->cover_url" class="h-20 w-14 shrink-0 rounded-md" />
                        @endif
                        <div class="flex-1">
                            <p class="font-medium text-stone-900">{{ $item->title }}</p>
                            <p class="text-sm text-stone-500">{{ $item->author }}</p>
                            <p class="mt-1 text-xs text-stone-400">{{ $item->quantity }} × {{ \App\Support\Money::format($item->unit_price) }}</p>
                        </div>
                        <x-price :value="$item->line_total" class="font-semibold text-stone-900" />
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="card p-6">
            <h2 class="font-serif text-lg font-semibold text-stone-900">Detalle</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-stone-500">Fecha</dt><dd class="text-right text-stone-800">{{ $order->paid_at?->translatedFormat('d/m/Y H:i') }}</dd></div>
                <div class="flex items-center justify-between gap-4"><dt class="text-stone-500">Estado</dt><dd><span class="badge {{ $order->statusClasses() }}">{{ $order->statusLabel() }}</span></dd></div>
                @if ($order->buyer_name)<div class="flex justify-between gap-4"><dt class="text-stone-500">Comprador</dt><dd class="text-right text-stone-800">{{ $order->buyer_name }}</dd></div>@endif
                @if ($order->buyer_email)<div class="flex justify-between gap-4"><dt class="text-stone-500">Correo</dt><dd class="truncate text-right text-stone-800">{{ $order->buyer_email }}</dd></div>@endif
                @if ($order->payment_method)<div class="flex justify-between gap-4"><dt class="text-stone-500">Método</dt><dd class="text-right capitalize text-stone-800">{{ $order->payment_method }}</dd></div>@endif
                @if ($order->payment_id)<div class="flex justify-between gap-4"><dt class="text-stone-500">ID de pago</dt><dd class="truncate text-right text-stone-800">{{ $order->payment_id }}</dd></div>@endif
            </dl>
            <div class="mt-4 flex items-center justify-between border-t border-stone-200 pt-4">
                <span class="font-semibold text-stone-900">Total</span>
                <x-price :value="$order->total" class="text-xl font-bold text-stone-900" />
            </div>
        </aside>
    </div>
</x-layout>
