@props(['value'])

<span {{ $attributes }}>{{ \App\Support\Money::format($value) }}</span>
