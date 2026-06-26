@php
    $styles = [
        'status' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'info' => 'bg-sky-50 text-sky-800 ring-sky-200',
        'error' => 'bg-rose-50 text-rose-800 ring-rose-200',
    ];
@endphp

@foreach (['status', 'info', 'error'] as $key)
    @if (session($key))
        <div class="mb-6 flex items-start gap-3 rounded-xl px-4 py-3 text-sm ring-1 ring-inset {{ $styles[$key] }}">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-11.25a.75.75 0 0 0 0 1.5h.75v3.5a.75.75 0 0 0 1.5 0V8a.75.75 0 0 0-.75-.75h-1.5Zm.75-2.5a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z" clip-rule="evenodd" />
            </svg>
            <p>{{ session($key) }}</p>
        </div>
    @endif
@endforeach
