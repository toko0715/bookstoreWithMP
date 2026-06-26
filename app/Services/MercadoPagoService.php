<?php

namespace App\Services;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    /**
     * ¿Hay un access token configurado? Si no, el checkout usa el pago simulado.
     */
    public function configured(): bool
    {
        if (app()->environment('testing')) {
            return false;
        }

        return filled(config('services.mercadopago.token'))
            && filled(config('services.mercadopago.public_key'));
    }

    protected function boot(): void
    {
        MercadoPagoConfig::setAccessToken((string) config('services.mercadopago.token'));

        // En local, el SDK relaja la verificación SSL para facilitar pruebas.
        MercadoPagoConfig::setRuntimeEnviroment(
            app()->environment('local')
                ? MercadoPagoConfig::LOCAL
                : MercadoPagoConfig::SERVER
        );
    }

    /**
     * Normaliza la URL de portada para evitar enviar valores vacíos o inválidos
     * a Mercado Pago.
     */
    protected function normalizedPictureUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * Mercado Pago puede rechazar `auto_return` cuando la URL de éxito apunta
     * a localhost. En ese caso dejamos que el checkout vuelva por la URL de
     * retorno sin forzar el auto-redirect.
     */
    protected function shouldAutoReturn(array $backUrls): bool
    {
        $successUrl = $backUrls['success'] ?? null;

        if (! is_string($successUrl) || $successUrl === '') {
            return false;
        }

        $host = parse_url($successUrl, PHP_URL_HOST);

        return ! in_array($host, ['localhost', '127.0.0.1', '0.0.0.0'], true);
    }

    /**
     * Crea una preferencia de pago (Checkout Pro) y devuelve sus puntos de inicio.
     *
     * @param  array<string,mixed>  $snapshot  salida de Cart::snapshot()
     * @param  array{success:string,failure:string,pending:string}  $backUrls
     * @return array{id:string,init_point:?string,sandbox_init_point:?string}
     */
    public function createPreference(
        array $snapshot,
        string $externalReference,
        array $backUrls,
        ?string $payerEmail = null,
        ?string $notificationUrl = null,
    ): array {
        $this->boot();

        $items = array_map(fn (array $i) => array_filter([
            'id' => (string) $i['book_id'],
            'title' => $i['title'],
            'quantity' => (int) $i['quantity'],
            'unit_price' => (float) $i['unit_price'],
            'currency_id' => (string) config('services.mercadopago.currency'),
            'picture_url' => $this->normalizedPictureUrl($i['cover_url'] ?? null),
        ], fn ($value) => $value !== null), $snapshot['items']);

        $request = [
            'items' => $items,
            'external_reference' => $externalReference,
            'back_urls' => $backUrls,
            'statement_descriptor' => 'LIBRERIA',
        ];

        if ($this->shouldAutoReturn($backUrls)) {
            $request['auto_return'] = 'approved';
        }

        if ($payerEmail) {
            $request['payer'] = ['email' => $payerEmail];
        }

        if ($notificationUrl) {
            $request['notification_url'] = $notificationUrl;
        }

        $preference = (new PreferenceClient())->create($request);

        return [
            'id' => (string) $preference->id,
            'init_point' => $preference->init_point ?? null,
            'sandbox_init_point' => $preference->sandbox_init_point ?? null,
        ];
    }

    /**
     * Consulta el estado real de un pago. Devuelve null si no se puede obtener.
     *
     * @return array<string,mixed>|null
     */
    public function getPayment(int|string $paymentId): ?array
    {
        $this->boot();

        try {
            $payment = (new PaymentClient())->get((int) $paymentId);
        } catch (MPApiException) {
            return null;
        }

        return [
            'id' => (string) $payment->id,
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'external_reference' => $payment->external_reference,
            'payment_method' => $payment->payment_method_id ?? null,
            'transaction_amount' => $payment->transaction_amount ?? null,
            'payer_email' => $payment->payer->email ?? null,
        ];
    }

    /**
     * Crea un cobro directo desde un formulario de tarjeta de Bricks.
     *
     * @param  array<string,mixed>  $paymentData
     * @return array<string,mixed>|null
     */
    public function createDirectPayment(array $paymentData): ?array
    {
        $this->boot();

        $request = array_filter([
            'transaction_amount' => (float) ($paymentData['transaction_amount'] ?? 0),
            'token' => (string) ($paymentData['token'] ?? ''),
            'description' => (string) ($paymentData['description'] ?? 'Compra en Librería'),
            'installments' => (int) ($paymentData['installments'] ?? 1),
            'payment_method_id' => (string) ($paymentData['payment_method_id'] ?? ''),
            'issuer_id' => isset($paymentData['issuer_id']) && $paymentData['issuer_id'] !== '' ? (string) $paymentData['issuer_id'] : null,
            'external_reference' => isset($paymentData['external_reference']) ? (string) $paymentData['external_reference'] : null,
            'binary_mode' => true,
            'payer' => [
                'email' => (string) ($paymentData['payer_email'] ?? ''),
            ],
        ], fn ($value) => $value !== null && $value !== '');

        try {
            $payment = (new PaymentClient())->create($request);
        } catch (MPApiException) {
            return null;
        }

        return [
            'id' => (string) $payment->id,
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'external_reference' => $payment->external_reference,
            'payment_method' => $payment->payment_method_id ?? null,
            'transaction_amount' => $payment->transaction_amount ?? null,
            'payer_email' => $payment->payer->email ?? null,
        ];
    }
}
