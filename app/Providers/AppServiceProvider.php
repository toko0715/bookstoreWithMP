<?php

namespace App\Providers;

use App\Services\CloudinaryService;
use App\Services\MercadoPagoService;
use App\Services\OrderService;
use App\Support\Cart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Cart::class);
        $this->app->singleton(CloudinaryService::class);
        $this->app->singleton(MercadoPagoService::class);
        $this->app->singleton(OrderService::class);
    }

    public function boot(): void
    {
        // Fechas en español ("25 de junio de 2026").
        Carbon::setLocale(config('app.locale', 'es'));

        // Detrás del proxy TLS de Render generamos siempre URLs https
        // (evita el mixed-content que bloquea CSS/JS).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
