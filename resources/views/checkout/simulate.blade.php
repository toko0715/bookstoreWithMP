<x-layout title="Pago de prueba">
    <div class="mx-auto max-w-md">
        <div class="card overflow-hidden">
            <div class="bg-sky-500 px-6 py-5 text-white">
                <div class="flex items-center justify-between">
                    <span class="font-semibold">Pago de prueba</span>
                    <span class="chip bg-white/15 text-white ring-0">SANDBOX</span>
                </div>
                <p class="mt-1 text-sm text-sky-50">Simulación de Mercado Pago (sin credenciales)</p>
            </div>

            <div class="p-6">
                <p class="text-sm text-stone-500">Estás a punto de pagar</p>
                <p class="mt-1 font-serif text-3xl font-bold text-stone-900">{{ \App\Support\Money::format($payload['total']) }}</p>

                <ul class="mt-5 space-y-2 border-t border-stone-100 pt-4 text-sm">
                    @foreach ($payload['items'] as $item)
                        <li class="flex justify-between gap-3 text-stone-600">
                            <span class="truncate">{{ $item['quantity'] }} × {{ $item['title'] }}</span>
                            <span class="shrink-0 font-medium">{{ \App\Support\Money::format($item['line_total']) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6 space-y-2">
                    <form action="{{ route('checkout.simulate.process', $reference) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="approve">
                        <button class="btn btn-accent w-full">Aprobar pago ✓</button>
                    </form>
                    <form action="{{ route('checkout.simulate.process', $reference) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="reject">
                        <button class="btn btn-ghost w-full !text-rose-600">Rechazar pago</button>
                    </form>
                </div>

                <p class="mt-5 text-center text-xs text-stone-400">
                    Define <code class="rounded bg-stone-100 px-1 py-0.5">MP_ACCESS_TOKEN</code> en tu <code class="rounded bg-stone-100 px-1 py-0.5">.env</code>
                    para usar el checkout real de Mercado Pago.
                </p>
            </div>
        </div>
    </div>
</x-layout>
