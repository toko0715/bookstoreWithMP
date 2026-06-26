<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MercadoPagoService;
use App\Services\OrderService;
use App\Support\Cart;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly Cart $cart,
        private readonly MercadoPagoService $mercadoPago,
        private readonly OrderService $orders,
    ) {
    }

    /** Resumen del pedido antes de pagar. */
    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('catalog.index')->with('info', 'Tu carrito está vacío.');
        }

        return view('checkout.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'total' => $this->cart->total(),
            'mercadoPagoEnabled' => $this->mercadoPago->configured(),
        ]);
    }

    /** Crea la preferencia de pago y muestra el Brick de pago (o el flujo simulado). */
    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('catalog.index')->with('info', 'Tu carrito está vacío.');
        }

        $data = $request->validate([
            'buyer_name' => ['nullable', 'string', 'max:120'],
            'buyer_email' => ['nullable', 'email', 'max:160'],
        ]);

        $reference = (string) Str::uuid();

        $payload = $this->cart->snapshot();
        $payload['buyer_name'] = $data['buyer_name'] ?? null;
        $payload['buyer_email'] = $data['buyer_email'] ?? null;

        $this->orders->stash($reference, $payload);
        session(['checkout_ref' => $reference]);

        // Sin credenciales de Mercado Pago -> pago simulado.
        if (! $this->mercadoPago->configured()) {
            return redirect()->route('checkout.simulate', $reference);
        }

        try {
            $preference = $this->mercadoPago->createPreference(
                snapshot: $payload,
                externalReference: $reference,
                backUrls: [
                    'success' => route('checkout.success'),
                    'failure' => route('checkout.failure'),
                    'pending' => route('checkout.pending'),
                ],
                payerEmail: $data['buyer_email'] ?? null,
                notificationUrl: $this->notificationUrl(),
            );

            if (! $preference['id']) {
                throw new \RuntimeException('Mercado Pago no devolvió un preferenceId.');
            }

            return view('checkout.bricks', [
                'items' => $this->cart->items(),
                'subtotal' => $this->cart->subtotal(),
                'total' => $this->cart->total(),
                'preferenceId' => $preference['id'],
                'publicKey' => config('services.mercadopago.public_key'),
                'mpLocale' => str_replace('_', '-', config('store.locale', 'es_PE')),
                'buyerName' => $payload['buyer_name'] ?? null,
                'buyerEmail' => $payload['buyer_email'] ?? null,
                'testCard' => [
                    'number' => '5031 7557 3453 0604',
                    'cvv' => '123',
                    'expiry' => '11/30',
                    'name' => 'APRO',
                ],
            ]);
        } catch (MPApiException $e) {
            Log::error('Mercado Pago checkout failed', [
                'status' => $e->getStatusCode(),
                'error' => $e->getMessage(),
                'response' => $e->getApiResponse()->getContent(),
            ]);

            return back()->with('error', 'Mercado Pago rechazó la solicitud de pago. Revisa el detalle en el log para ver el motivo exacto.');
        } catch (\Throwable $e) {
            Log::error('Mercado Pago checkout failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo iniciar el pago con Mercado Pago. Revisa las credenciales o inténtalo de nuevo.');
        }
    }

    /** Procesa el pago embebido del Brick sin salir de la app. */
    public function pay(Request $request)
    {
        $reference = session('checkout_ref');

        if (! $reference) {
            return response()->json([
                'message' => 'La sesión de pago expiró. Inténtalo de nuevo.',
            ], 419);
        }

        $payload = $this->orders->pending($reference);

        if (! $payload) {
            return response()->json([
                'message' => 'La sesión de pago expiró. Inténtalo de nuevo.',
            ], 419);
        }

        $paymentMethodId = $request->input('payment_method_id', $request->input('paymentMethodId'));
        $selectedPaymentMethod = $request->input('selectedPaymentMethod', []);
        if (! is_string($paymentMethodId) || trim($paymentMethodId) === '') {
            $paymentMethodId = data_get($selectedPaymentMethod, 'id')
                ?: data_get($selectedPaymentMethod, 'payment_method_id')
                ?: data_get($selectedPaymentMethod, 'paymentMethodId')
                ?: null;
        }

        $token = $request->input('token', $request->input('cardToken'));
        if (! is_string($token) || trim($token) === '') {
            $token = data_get($request->input('formData', []), 'token')
                ?: data_get($request->input('selectedPaymentMethod', []), 'token')
                ?: data_get($request->input('selectedPaymentMethod', []), 'cardToken')
                ?: null;
        }
        $installments = (int) $request->input('installments', 1);
        $issuerId = $request->input('issuer_id', $request->input('issuerId'));
        if ($issuerId === null || $issuerId === '') {
            $issuerId = data_get($selectedPaymentMethod, 'issuer_id')
                ?: data_get($selectedPaymentMethod, 'issuerId')
                ?: data_get($request->input('formData', []), 'issuer_id')
                ?: null;
        }
        $payerEmail = $request->input('payer_email', data_get($request->input('payer'), 'email'));
        if (! is_string($payerEmail) || trim($payerEmail) === '') {
            $payerEmail = data_get($request->input('formData', []), 'payer.email')
                ?: data_get($request->input('payer', []), 'email')
                ?: null;
        }
        $description = $request->input('description', 'Compra en Librería');

        if (! is_string($paymentMethodId) || trim($paymentMethodId) === '') {
            return response()->json(['message' => 'Falta el método de pago.'], 422);
        }

        if (! is_string($token) || trim($token) === '') {
            return response()->json(['message' => 'Falta el token de la tarjeta.'], 422);
        }

        if ($installments !== 1) {
            return response()->json(['message' => 'Este checkout solo permite 1 cuota.'], 422);
        }

        if (! is_string($payerEmail) || ! filter_var($payerEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'El correo del comprador no es válido.'], 422);
        }

        $payment = $this->mercadoPago->createDirectPayment([
            'transaction_amount' => $payload['total'],
            'token' => $token,
            'description' => $description,
            'installments' => 1,
            'payment_method_id' => $paymentMethodId,
            'issuer_id' => $issuerId,
            'external_reference' => $reference,
            'payer_email' => $payerEmail,
        ]);

        if (! $payment) {
            Log::error('Mercado Pago direct payment failed', [
                'reference' => $reference,
                'payment_method_id' => $paymentMethodId,
            ]);

            return response()->json([
                'message' => 'No se pudo procesar el pago en Mercado Pago.',
            ], 422);
        }

        if ($payment['status'] === 'approved') {
            $order = $this->orders->createApprovedFromCache(
                $payment['external_reference'] ?: $reference,
                $payment,
            );

            if ($order) {
                $this->cart->clear();
                session()->forget('checkout_ref');

                return response()->json([
                    'status' => 'approved',
                    'message' => 'Pago aprobado.',
                    'order' => [
                        'id' => $order->id,
                        'paid_at' => optional($order->paid_at)?->toIso8601String(),
                        'total' => (float) $order->total,
                    ],
                ]);
            }
        }

        if (in_array($payment['status'], ['pending', 'in_process'], true)) {
            return response()->json([
                'status' => $payment['status'],
                'message' => 'El pago quedó pendiente de confirmación.',
            ]);
        }

        return response()->json([
            'status' => $payment['status'] ?? 'rejected',
            'message' => $payment['status_detail'] ?? 'El pago fue rechazado.',
        ], 422);
    }

    /** Mercado Pago (o el flujo simulado) regresa aquí cuando el pago es exitoso. */
    public function success(Request $request)
    {
        // Flujo simulado: la orden ya fue creada y llega por query string.
        if ($orderId = $request->integer('order')) {
            $order = Order::with('items')->find($orderId);

            return $order
                ? view('checkout.success', ['order' => $order])
                : redirect()->route('catalog.index');
        }

        // Flujo Mercado Pago: verificamos el pago contra la API antes de registrar.
        $paymentId = $request->query('payment_id') ?: $request->query('collection_id');
        $reference = $request->query('external_reference') ?: session('checkout_ref');

        if ($this->mercadoPago->configured() && $paymentId) {
            $payment = $this->mercadoPago->getPayment($paymentId);

            if ($payment && $payment['status'] === 'approved') {
                $order = $this->orders->createApprovedFromCache(
                    $payment['external_reference'] ?: $reference,
                    $payment,
                );

                if ($order) {
                    $this->cart->clear();
                    session()->forget('checkout_ref');

                    return view('checkout.success', ['order' => $order]);
                }
            }

            if ($payment && in_array($payment['status'], ['pending', 'in_process'], true)) {
                return redirect()->route('checkout.pending');
            }

            return redirect()->route('checkout.failure');
        }

        return redirect()->route('checkout.pending');
    }

    public function failure()
    {
        return view('checkout.failure');
    }

    public function pending()
    {
        return view('checkout.pending');
    }

    /** Página de pago SIMULADO (cuando no hay credenciales de Mercado Pago). */
    public function simulate(string $reference)
    {
        $payload = $this->orders->pending($reference);

        if (! $payload) {
            return redirect()->route('cart.index')->with('info', 'La sesión de pago expiró. Inténtalo de nuevo.');
        }

        return view('checkout.simulate', [
            'reference' => $reference,
            'payload' => $payload,
        ]);
    }

    public function simulateProcess(Request $request, string $reference)
    {
        if ($request->input('decision') === 'approve') {
            $order = $this->orders->createApprovedFromCache($reference, [
                'id' => 'SIMULADO-'.strtoupper(Str::random(10)),
                'status' => 'approved',
                'payment_method' => 'simulado',
            ]);

            if (! $order) {
                return redirect()->route('cart.index')->with('info', 'La sesión de pago expiró. Inténtalo de nuevo.');
            }

            $this->cart->clear();
            session()->forget('checkout_ref');

            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        $this->orders->forget($reference);

        return redirect()->route('checkout.failure');
    }

    /** Notificaciones server-to-server de Mercado Pago (IPN/Webhook). */
    public function webhook(Request $request)
    {
        $type = $request->input('type', $request->query('type', $request->input('topic')));
        $paymentId = $request->input('data.id', $request->query('id', data_get($request->input('data'), 'id')));

        if ($type === 'payment' && $paymentId && $this->mercadoPago->configured()) {
            $payment = $this->mercadoPago->getPayment($paymentId);

            if ($payment && $payment['status'] === 'approved') {
                $this->orders->createApprovedFromCache($payment['external_reference'], $payment);
            }
        }

        return response()->noContent();
    }

    /** Mercado Pago no puede notificar a un host local, así que lo omitimos. */
    private function notificationUrl(): ?string
    {
        $url = route('webhook.mercadopago');
        $host = parse_url($url, PHP_URL_HOST);

        if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0'], true)) {
            return null;
        }

        return $url;
    }
}
