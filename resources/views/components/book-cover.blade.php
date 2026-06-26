@props([
    'title',
    'author' => null,
    'category' => null,
    'cover' => null,
])

@php
    // Degradado determinístico derivado del título: portada de respaldo
    // que se muestra si la imagen remota (Cloudinary / Open Library) no carga.
    $hue = crc32((string) ($title ?: 'libro')) % 360;
    $from = "hsl({$hue} 42% 30%)";
    $to = 'hsl('.(($hue + 35) % 360).' 55% 18%)';
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-stone-800']) }}
     style="background-image: linear-gradient(145deg, {{ $from }}, {{ $to }});">
    <div class="absolute inset-0 flex flex-col justify-between p-3">
        <span class="text-[10px] font-medium uppercase tracking-widest text-white/55">{{ $category }}</span>
        <div>
            <p class="line-clamp-3 font-serif text-sm font-semibold leading-snug text-white">{{ $title }}</p>
            @if ($author)
                <p class="mt-1 text-xs text-white/65">{{ $author }}</p>
            @endif
        </div>
    </div>

    @if ($cover)
        <img src="{{ $cover }}" alt="Portada de {{ $title }}" loading="lazy"
             class="absolute inset-0 h-full w-full object-cover"
             onerror="this.remove()">
    @endif
</div>
