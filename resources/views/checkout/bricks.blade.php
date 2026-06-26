<x-layout title="Pagar con Mercado Pago">
    <a href="{{ route('checkout.index') }}" class="text-sm text-stone-500 hover:text-stone-700">← Volver al resumen</a>
    <h1 class="mb-2 mt-3 font-serif text-3xl font-semibold text-stone-900">Completar pago</h1>
    <p class="mb-6 max-w-2xl text-sm text-stone-500">
        El pago se realiza dentro de esta misma página con Mercado Pago Bricks. No sales de la tienda y, si se aprueba,
        la compra queda registrada al instante.
    </p>

    <div class="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-start">
        <section class="space-y-6">
            <div class="card border border-amber-200 bg-amber-50/70 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-serif text-lg font-semibold text-stone-900">Tarjeta de prueba</h2>
                        <p class="mt-1 text-sm text-stone-600">
                            Usa estos datos de sandbox para completar el pago desde el Brick.
                        </p>
                    </div>
                    <span class="badge bg-white text-amber-700 ring-amber-200">Sandbox</span>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-inset ring-amber-200">
                        <p class="text-xs uppercase tracking-wide text-stone-400">Número</p>
                        <p class="mt-1 font-mono text-sm text-stone-900">{{ $testCard['number'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-inset ring-amber-200">
                        <p class="text-xs uppercase tracking-wide text-stone-400">CVV</p>
                        <p class="mt-1 font-mono text-sm text-stone-900">{{ $testCard['cvv'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-inset ring-amber-200">
                        <p class="text-xs uppercase tracking-wide text-stone-400">Vencimiento</p>
                        <p class="mt-1 font-mono text-sm text-stone-900">{{ $testCard['expiry'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-inset ring-amber-200">
                        <p class="text-xs uppercase tracking-wide text-stone-400">Titular</p>
                        <p class="mt-1 font-mono text-sm text-stone-900">{{ $testCard['name'] }}</p>
                    </div>
                </div>

                <p class="mt-4 text-xs leading-5 text-stone-500">
                    El número y CVV no se pueden autocompletar dentro del iframe seguro de Mercado Pago, pero sí quedan visibles aquí para copiar y pegar.
                </p>
            </div>

            <div class="card p-6">
                <h2 class="font-serif text-lg font-semibold text-stone-900">Datos del comprador</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-stone-400">Nombre</p>
                        <p class="mt-1 text-sm text-stone-700">{{ $buyerName ?: 'No especificado' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-stone-400">Correo</p>
                        <p class="mt-1 text-sm text-stone-700">{{ $buyerEmail ?: 'No especificado' }}</p>
                    </div>
                </div>
            </div>

            <div id="paymentPanel" class="card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-serif text-lg font-semibold text-stone-900">Tarjeta de pago</h2>
                        <p class="mt-1 text-sm text-stone-500">Completa los datos y paga sin redirecciones externas.</p>
                    </div>
                    <span class="badge bg-sky-50 text-sky-700 ring-sky-200">Payment Brick</span>
                </div>

                <div id="paymentBrick_container" class="mt-6 rounded-2xl bg-stone-50 p-4 ring-1 ring-inset ring-stone-200"></div>

                <div id="mpBrickError" class="mt-4 hidden rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-inset ring-rose-200"></div>
            </div>

            <div id="successPanel" class="card hidden p-6">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <div class="mt-5 text-center">
                    <h2 class="font-serif text-2xl font-semibold text-stone-900">¡Pago aprobado!</h2>
                    <p id="successMessage" class="mt-2 text-sm text-stone-500">Tu compra se registró correctamente.</p>
                </div>

                <div class="mt-6 rounded-2xl bg-stone-50 p-4 ring-1 ring-inset ring-stone-200">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-stone-500">Pedido</span>
                        <span id="successOrderId" class="font-medium text-stone-900">-</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-4">
                        <span class="text-sm text-stone-500">Total</span>
                        <span class="font-medium text-stone-900">{{ \App\Support\Money::format($total) }}</span>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary">Ver mis compras</a>
                    <a href="{{ route('catalog.index') }}" class="btn btn-ghost">Seguir comprando</a>
                </div>
            </div>
        </section>

        <aside class="card sticky top-28 p-6">
            <h2 class="font-serif text-lg font-semibold text-stone-900">Resumen</h2>
            <div class="mt-4 divide-y divide-stone-100">
                @foreach ($items as $item)
                    <div class="flex items-center gap-3 py-3 first:pt-0">
                        <x-book-cover :title="$item->title" :author="$item->author" :cover="$item->cover_url" class="h-12 w-9 rounded-md" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-stone-900">{{ $item->title }}</p>
                            <p class="text-xs text-stone-500">{{ $item->quantity }} × {{ \App\Support\Money::format($item->unit_price) }}</p>
                        </div>
                        <x-price :value="$item->line_total" class="text-sm font-semibold text-stone-900" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4 space-y-2 border-t border-stone-200 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-stone-500">Subtotal</span>
                    <span class="font-medium text-stone-800">{{ \App\Support\Money::format($subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-stone-500">Envío</span>
                    <span class="font-medium text-emerald-600">Gratis</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="font-semibold text-stone-900">Total</span>
                    <x-price :value="$total" class="text-xl font-bold text-stone-900" />
                </div>
            </div>
        </aside>
    </div>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        (async function () {
            const publicKey = @json($publicKey);
            const locale = @json($mpLocale);
            const total = @json($total);
            const buyerEmail = @json($buyerEmail);
            const description = 'Compra en Librería';
            const errorBox = document.getElementById('mpBrickError');
            const paymentPanel = document.getElementById('paymentPanel');
            const successPanel = document.getElementById('successPanel');
            const successOrderId = document.getElementById('successOrderId');
            const successMessage = document.getElementById('successMessage');

            if (!publicKey) {
                errorBox.textContent = 'No pudimos preparar el checkout de Mercado Pago.';
                errorBox.classList.remove('hidden');
                return;
            }

            try {
                const mp = new MercadoPago(publicKey, { locale });
                const bricksBuilder = mp.bricks();

                await bricksBuilder.create('payment', 'paymentBrick_container', {
                    initialization: {
                        amount: total,
                        payer: {
                            email: buyerEmail,
                        },
                    },
                    customization: {
                        paymentMethods: {
                            creditCard: 'all',
                            debitCard: 'all',
                            ticket: 'all',
                            bankTransfer: 'all',
                            maxInstallments: 1,
                        },
                    },
                    callbacks: {
                        onReady: () => {
                            console.log('Mercado Pago Payment Brick listo');
                        },
                        onSubmit: async (formData) => {
                            try {
                                console.log('Mercado Pago Payment Brick submit payload:', formData);
                                const normalizedForm = formData?.formData ?? formData?.data ?? formData ?? {};
                                const paymentMethodId =
                                    normalizedForm.payment_method_id
                                    ?? normalizedForm.paymentMethodId
                                    ?? normalizedForm.selectedPaymentMethod?.id
                                    ?? normalizedForm.selectedPaymentMethod?.payment_method_id
                                    ?? normalizedForm.selectedPaymentMethod?.paymentMethodId
                                    ?? null;
                                const token =
                                    normalizedForm.token
                                    ?? normalizedForm.cardToken
                                    ?? normalizedForm.card_token
                                    ?? normalizedForm.paymentMethodToken
                                    ?? null;
                                const installments = normalizedForm.installments ?? normalizedForm.quotas ?? 1;
                                const issuerId =
                                    normalizedForm.issuer_id
                                    ?? normalizedForm.issuerId
                                    ?? normalizedForm.selectedIssuer?.id
                                    ?? null;
                                const payerEmail =
                                    normalizedForm.payer?.email
                                    ?? normalizedForm.payer_email
                                    ?? buyerEmail
                                    ?? null;

                                const response = await fetch(@json(route('checkout.pay')), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': @json(csrf_token()),
                                    },
                                    body: JSON.stringify({
                                        ...normalizedForm,
                                        payment_method_id: paymentMethodId,
                                        token,
                                        installments,
                                        issuer_id: issuerId,
                                        transaction_amount: total,
                                        description,
                                        payer_email: payerEmail,
                                    }),
                                });

                                const result = await response.json();

                                if (!response.ok) {
                                    throw new Error(result.message || 'No se pudo procesar el pago.');
                                }

                                if (result.status === 'approved') {
                                    paymentPanel.classList.add('hidden');
                                    successPanel.classList.remove('hidden');
                                    successOrderId.textContent = `#${result.order.id}`;
                                    successMessage.textContent = 'Tu pago fue aprobado y registramos tu pedido en la tienda.';
                                } else {
                                    errorBox.textContent = result.message || 'El pago quedó pendiente.';
                                    errorBox.classList.remove('hidden');
                                }
                            } catch (error) {
                                console.error('Mercado Pago payment submit error:', error);
                                errorBox.textContent = error.message || 'No pudimos procesar el pago.';
                                errorBox.classList.remove('hidden');
                            }
                        },
                        onError: (error) => {
                            console.error('Mercado Pago Payment Brick error:', error);
                            errorBox.textContent = 'No pudimos cargar la tarjeta de pago. Intenta de nuevo.';
                            errorBox.classList.remove('hidden');
                        },
                    },
                });
            } catch (error) {
                console.error('Mercado Pago init error:', error);
                errorBox.textContent = 'No pudimos iniciar el checkout de Mercado Pago.';
                errorBox.classList.remove('hidden');
            }
        })();
    </script>
</x-layout>
