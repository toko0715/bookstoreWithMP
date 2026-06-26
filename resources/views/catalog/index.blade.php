<x-layout title="Catálogo">
    @unless ($q)
        <section class="card relative mb-10 overflow-hidden bg-stone-900 px-6 py-12 sm:px-10 sm:py-16">
            <div class="relative z-10 max-w-xl">
                <span class="chip bg-white/10 text-amber-300 ring-0">Envío gratis en todo el Perú</span>
                <h1 class="mt-4 font-serif text-3xl font-semibold leading-tight text-white sm:text-4xl">
                    Historias que vale la pena tener en papel
                </h1>
                <p class="mt-3 text-stone-300">
                    Una selección de clásicos de la literatura. Elige, agrega al carrito y paga en segundos.
                </p>
                <a href="#catalogo" class="btn btn-accent mt-6">Ver catálogo</a>
            </div>
            <div class="pointer-events-none absolute -right-12 -top-12 h-64 w-64 rounded-full bg-amber-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-16 right-24 h-52 w-52 rounded-full bg-sky-500/10 blur-3xl"></div>
        </section>
    @endunless

    <div id="catalogo" class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-stone-900">{{ $q ? 'Resultados de búsqueda' : 'Catálogo' }}</h2>
            <p class="mt-0.5 text-sm text-stone-500">
                @if ($q)
                    {{ $books->total() }} resultado(s) para «{{ $q }}».
                    <a href="{{ route('catalog.index') }}" class="font-medium text-amber-700 hover:underline">Limpiar búsqueda</a>
                @else
                    {{ $books->total() }} libros disponibles
                @endif
            </p>
        </div>
    </div>

    @if ($books->isEmpty())
        <x-empty-state title="No encontramos libros" message="Prueba con otro título o autor.">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Ver todo el catálogo</a>
        </x-empty-state>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
            @foreach ($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $books->links() }}
        </div>
    @endif
</x-layout>
