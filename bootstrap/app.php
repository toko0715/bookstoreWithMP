<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render (y otros PaaS) terminan TLS en un proxy: confiamos en sus
        // cabeceras X-Forwarded-* para detectar correctamente HTTPS y el host.
        $middleware->trustProxies(at: '*');

        // Mercado Pago posts server-to-server, so its webhook is exempt from CSRF.
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
