<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moneda de la tienda
    |--------------------------------------------------------------------------
    |
    | Define el código ISO, el símbolo y el locale usados para mostrar y
    | cobrar los precios. Por defecto: Soles peruanos (PEN).
    |
    */

    'currency' => env('STORE_CURRENCY', 'PEN'),

    'currency_symbol' => env('STORE_CURRENCY_SYMBOL', 'S/'),

    'locale' => env('STORE_LOCALE', 'es_PE'),

];
