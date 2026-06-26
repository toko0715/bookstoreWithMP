@props([
    'title',
    'message' => null,
])

<div class="card flex flex-col items-center justify-center px-6 py-16 text-center">
    <div class="grid h-14 w-14 place-items-center rounded-2xl bg-stone-100 text-stone-400">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
        </svg>
    </div>
    <h3 class="mt-4 font-serif text-xl font-semibold text-stone-900">{{ $title }}</h3>
    @if ($message)
        <p class="mt-1 max-w-sm text-sm text-stone-500">{{ $message }}</p>
    @endif
    @if (trim($slot))
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
