@props(['book'])

<a href="{{ route('catalog.show', $book) }}"
   class="card group flex flex-col overflow-hidden transition duration-200 hover:-translate-y-1 hover:shadow-md">
    <div class="relative">
        <x-book-cover :title="$book->title" :author="$book->author" :category="$book->category"
                      :cover="$book->cover_url" class="aspect-[2/3] w-full" />
        @if ($book->isOutOfStock())
            <span class="badge absolute right-2 top-2 bg-stone-900/85 text-white ring-0">Agotado</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-stone-900">{{ $book->title }}</h3>
        <p class="mt-1 text-xs text-stone-500">{{ $book->author }}</p>
        <div class="mt-3 flex items-center justify-between">
            <x-price :value="$book->price" class="text-base font-bold text-stone-900" />
            <span class="text-xs font-medium text-amber-700 opacity-0 transition group-hover:opacity-100">Ver →</span>
        </div>
    </div>
</a>
