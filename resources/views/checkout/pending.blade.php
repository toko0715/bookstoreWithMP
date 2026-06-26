<x-layout title="Pago pendiente">
    <div class="mx-auto max-w-lg text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-amber-100 text-amber-600">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <h1 class="mt-5 font-serif text-3xl font-semibold text-stone-900">Tu pago está pendiente</h1>
        <p class="mt-2 text-stone-500">Estamos esperando la confirmación de Mercado Pago. Registraremos tu compra apenas se apruebe el pago.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-primary">Ver mis compras</a>
            <a href="{{ route('catalog.index') }}" class="btn btn-ghost">Volver al inicio</a>
        </div>
    </div>
</x-layout>
