@props(['title' => null])
@inject('cart', 'App\Support\Cart')
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col">
    <header class="sticky top-0 z-40 border-b border-stone-200/80 bg-stone-50/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3 sm:gap-4 sm:px-6">
            <a href="{{ route('catalog.index') }}" class="flex shrink-0 items-center gap-2 text-stone-900">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-stone-900 text-amber-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75A5.25 5.25 0 0 0 6.75 1.5H3.75A.75.75 0 0 0 3 2.25v15c0 .414.336.75.75.75h3a5.25 5.25 0 0 1 5.25 5.25m0-16.5v16.5m0-16.5A5.25 5.25 0 0 1 17.25 1.5h3a.75.75 0 0 1 .75.75v15a.75.75 0 0 1-.75.75h-3A5.25 5.25 0 0 0 12 23.25" />
                    </svg>
                </span>
                <span class="font-serif text-xl font-semibold tracking-tight">Librería</span>
            </a>

            <form action="{{ route('catalog.index') }}" method="GET" class="relative ml-2 hidden flex-1 md:block">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.34-4.34M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" /></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por título o autor…" class="field pl-10">
            </form>

            <nav class="ml-auto flex items-center gap-1 sm:gap-2">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    <span class="hidden sm:inline">Mis compras</span>
                </a>
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" /></svg>
                    <span class="hidden sm:inline">Carrito</span>
                    @if ($cart->count() > 0)
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-amber-500 px-1 text-[11px] font-bold text-stone-950">{{ $cart->count() }}</span>
                    @endif
                </a>
            </nav>
        </div>

        <div class="border-t border-stone-200/70 px-4 py-2 md:hidden">
            <form action="{{ route('catalog.index') }}" method="GET" class="relative">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.34-4.34M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" /></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por título o autor…" class="field pl-10">
            </form>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6">
        <x-flash />
        {{ $slot }}
    </main>

    <footer class="mt-8 border-t border-stone-200/80 py-8">
        <div class="mx-auto max-w-6xl px-4 text-center text-sm text-stone-500 sm:px-6">
            <p class="font-serif text-base font-semibold text-stone-700">Librería</p>
            <p class="mt-1">Proyecto de portafolio · Laravel · Blade + TailwindCSS</p>
        </div>
    </footer>
</body>
</html>
