<?php

namespace App\Support;

class Money
{
    public static function format(float|int|string $amount): string
    {
        return static::symbol().' '.number_format((float) $amount, 2, '.', ',');
    }

    public static function symbol(): string
    {
        return (string) config('store.currency_symbol', 'S/');
    }
}
