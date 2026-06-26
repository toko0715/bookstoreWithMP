<x-layout title="Pago no completado">
    <div class="mx-auto max-w-lg text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-rose-100 text-rose-600">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </div>
        <h1 class="mt-5 font-serif text-3xl font-semibold text-stone-900">El pago no se completó</h1>
        <p class="mt-2 text-stone-500">No se realizó ningún cargo y tu carrito sigue intacto. Puedes intentarlo nuevamente.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Reintentar pago</a>
            <a href="{{ route('cart.index') }}" class="btn btn-ghost">Volver al carrito</a>
        </div>
    </div>
</x-layout>
