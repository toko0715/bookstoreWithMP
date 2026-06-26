<x-layout :title="$book->title">
    <nav class="mb-6 text-sm text-stone-500">
        <a href="{{ route('catalog.index') }}" class="hover:text-stone-700">Catálogo</a>
        <span class="mx-1.5">/</span>
        <span class="text-stone-700">{{ $book->title }}</span>
    </nav>

    <div class="grid gap-8 md:grid-cols-[300px_1fr] md:gap-10">
        <div>
            <x-book-cover :title="$book->title" :author="$book->author" :category="$book->category"
                          :cover="$book->cover_url"
                          class="aspect-[2/3] w-full rounded-2xl shadow-md ring-1 ring-stone-200" />
        </div>

        <div>
            @if ($book->category)
                <span class="chip">{{ $book->category }}</span>
            @endif
            <h1 class="mt-3 font-serif text-3xl font-semibold leading-tight text-stone-900 sm:text-4xl">{{ $book->title }}</h1>
            <p class="mt-1.5 text-lg text-stone-600">{{ $book->author }}</p>

            <div class="mt-5 flex flex-wrap items-baseline gap-3">
                <x-price :value="$book->price" class="text-3xl font-bold text-stone-900" />
                @if ($book->isOutOfStock())
                    <span class="badge bg-rose-50 text-rose-700 ring-rose-200">Agotado</span>
                @elseif ($book->stock <= 5)
                    <span class="badge bg-amber-50 text-amber-700 ring-amber-200">¡Últimas {{ $book->stock }} unidades!</span>
                @else
                    <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-200">En stock</span>
                @endif
            </div>

            @if ($book->description)
                <p class="mt-6 leading-relaxed text-stone-600">{{ $book->description }}</p>
            @endif

            <form action="{{ route('cart.store', $book) }}" method="POST" class="mt-8 flex flex-wrap items-center gap-3">
                @csrf
                @unless ($book->isOutOfStock())
                    <div class="flex items-center rounded-xl bg-white ring-1 ring-inset ring-stone-200">
                        <span class="pl-4 pr-2 text-sm text-stone-500">Cantidad</span>
                        <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $book->stock) }}"
                               class="w-16 border-0 bg-transparent py-2.5 text-center text-sm font-medium focus:ring-0">
                    </div>
                    <button class="btn btn-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                        Agregar al carrito
                    </button>
                @else
                    <button type="button" disabled class="btn btn-primary">Agotado</button>
                    <a href="{{ route('catalog.index') }}" class="btn btn-ghost">Ver otros libros</a>
                @endunless
            </form>

            <dl class="mt-8 grid grid-cols-2 gap-4 border-t border-stone-200 pt-6 text-sm sm:grid-cols-4">
                <div><dt class="text-stone-400">Páginas</dt><dd class="mt-0.5 font-medium text-stone-700">{{ $book->pages ?? '—' }}</dd></div>
                <div><dt class="text-stone-400">Año</dt><dd class="mt-0.5 font-medium text-stone-700">{{ $book->published_year ?? '—' }}</dd></div>
                <div><dt class="text-stone-400">ISBN</dt><dd class="mt-0.5 font-medium text-stone-700">{{ $book->isbn ?? '—' }}</dd></div>
                <div><dt class="text-stone-400">Categoría</dt><dd class="mt-0.5 font-medium text-stone-700">{{ $book->category ?? '—' }}</dd></div>
            </dl>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="mb-5 font-serif text-2xl font-semibold text-stone-900">También te puede gustar</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($related as $relatedBook)
                    <x-book-card :book="$relatedBook" />
                @endforeach
            </div>
        </section>
    @endif
</x-layout>
