<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->books() as $data) {
            $data['slug'] = Str::slug($data['title']);
            // Portada de respaldo (Open Library). El comando `covers:cloudinary`
            // la re-sube a Cloudinary cuando hay credenciales.
            $data['cover_url'] = "https://covers.openlibrary.org/b/isbn/{$data['isbn']}-L.jpg";

            Book::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function books(): array
    {
        return [
            [
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'description' => 'La saga de la familia Buendía y la mítica Macondo, obra cumbre del realismo mágico.',
                'category' => 'Realismo mágico',
                'price' => 59.90,
                'isbn' => '9780307474728',
                'pages' => 471,
                'published_year' => 1967,
                'stock' => 18,
            ],
            [
                'title' => 'La ciudad y los perros',
                'author' => 'Mario Vargas Llosa',
                'description' => 'La dura vida de los cadetes del colegio militar Leoncio Prado en Lima.',
                'category' => 'Novela',
                'price' => 52.00,
                'isbn' => '9788420471839',
                'pages' => 432,
                'published_year' => 1963,
                'stock' => 12,
            ],
            [
                'title' => 'Ficciones',
                'author' => 'Jorge Luis Borges',
                'description' => 'Relatos que convirtieron a Borges en un maestro universal del cuento.',
                'category' => 'Cuento',
                'price' => 45.00,
                'isbn' => '9788420633139',
                'pages' => 224,
                'published_year' => 1944,
                'stock' => 20,
            ],
            [
                'title' => 'Rayuela',
                'author' => 'Julio Cortázar',
                'description' => 'Una novela que puede leerse de múltiples maneras; un clásico experimental.',
                'category' => 'Novela',
                'price' => 64.90,
                'isbn' => '9788437604572',
                'pages' => 736,
                'published_year' => 1963,
                'stock' => 9,
            ],
            [
                'title' => 'Pedro Páramo',
                'author' => 'Juan Rulfo',
                'description' => 'Juan Preciado viaja a Comala buscando a su padre entre voces de muertos.',
                'category' => 'Novela',
                'price' => 39.90,
                'isbn' => '9788437604183',
                'pages' => 124,
                'published_year' => 1955,
                'stock' => 15,
            ],
            [
                'title' => 'El amor en los tiempos del cólera',
                'author' => 'Gabriel García Márquez',
                'description' => 'Una historia de amor que espera más de cincuenta años para cumplirse.',
                'category' => 'Romance',
                'price' => 57.50,
                'isbn' => '9780307389732',
                'pages' => 464,
                'published_year' => 1985,
                'stock' => 14,
            ],
            [
                'title' => 'Conversación en La Catedral',
                'author' => 'Mario Vargas Llosa',
                'description' => '«¿En qué momento se había jodido el Perú?»: retrato descarnado de una dictadura.',
                'category' => 'Novela',
                'price' => 62.00,
                'isbn' => '9788420427867',
                'pages' => 608,
                'published_year' => 1969,
                'stock' => 7,
            ],
            [
                'title' => 'La casa de los espíritus',
                'author' => 'Isabel Allende',
                'description' => 'Tres generaciones de la familia Trueba entre lo íntimo y lo político.',
                'category' => 'Novela',
                'price' => 55.00,
                'isbn' => '9788401242141',
                'pages' => 433,
                'published_year' => 1982,
                'stock' => 11,
            ],
            [
                'title' => 'Don Quijote de la Mancha',
                'author' => 'Miguel de Cervantes',
                'description' => 'Las aventuras del ingenioso hidalgo y su fiel escudero Sancho Panza.',
                'category' => 'Clásico',
                'price' => 79.90,
                'isbn' => '9788424116378',
                'pages' => 1056,
                'published_year' => 1605,
                'stock' => 6,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => 'El Gran Hermano vigila: la distopía totalitaria más influyente del siglo XX.',
                'category' => 'Distopía',
                'price' => 42.00,
                'isbn' => '9780451524935',
                'pages' => 352,
                'published_year' => 1949,
                'stock' => 25,
            ],
            [
                'title' => 'El Principito',
                'author' => 'Antoine de Saint-Exupéry',
                'description' => 'Un piloto y un pequeño príncipe reflexionan sobre la amistad y lo esencial.',
                'category' => 'Infantil',
                'price' => 34.90,
                'isbn' => '9780156012195',
                'pages' => 96,
                'published_year' => 1943,
                'stock' => 30,
            ],
            [
                'title' => 'Crónica de una muerte anunciada',
                'author' => 'Gabriel García Márquez',
                'description' => 'Todos sabían que iban a matar a Santiago Nasar. Nadie hizo nada por impedirlo.',
                'category' => 'Novela',
                'price' => 38.00,
                'isbn' => '9781400034956',
                'pages' => 122,
                'published_year' => 1981,
                'stock' => 16,
            ],
            [
                'title' => 'El túnel',
                'author' => 'Ernesto Sabato',
                'description' => 'El pintor Juan Pablo Castel narra, obsesivo, el crimen que cometió.',
                'category' => 'Novela',
                'price' => 36.50,
                'isbn' => '9788437600222',
                'pages' => 160,
                'published_year' => 1948,
                'stock' => 0,
            ],
            [
                'title' => 'Los detectives salvajes',
                'author' => 'Roberto Bolaño',
                'description' => 'Dos poetas buscan a una escritora desaparecida por medio continente.',
                'category' => 'Novela',
                'price' => 66.00,
                'isbn' => '9788433968517',
                'pages' => 609,
                'published_year' => 1998,
                'stock' => 8,
            ],
            [
                'title' => 'Fahrenheit 451',
                'author' => 'Ray Bradbury',
                'description' => 'En un futuro donde los libros se queman, un bombero empieza a dudar.',
                'category' => 'Ciencia ficción',
                'price' => 44.00,
                'isbn' => '9781451673319',
                'pages' => 256,
                'published_year' => 1953,
                'stock' => 19,
            ],
            [
                'title' => 'La tregua',
                'author' => 'Mario Benedetti',
                'description' => 'El diario de Martín Santomé y un amor inesperado a punto de jubilarse.',
                'category' => 'Novela',
                'price' => 37.90,
                'isbn' => '9788420471655',
                'pages' => 192,
                'published_year' => 1960,
                'stock' => 13,
            ],
            [
                'title' => 'Sapiens: De animales a dioses',
                'author' => 'Yuval Noah Harari',
                'description' => 'Una breve historia de la humanidad, del Homo sapiens a la era moderna.',
                'category' => 'Ensayo',
                'price' => 72.00,
                'isbn' => '9788499926223',
                'pages' => 496,
                'published_year' => 2011,
                'stock' => 22,
            ],
            [
                'title' => 'El laberinto de la soledad',
                'author' => 'Octavio Paz',
                'description' => 'Ensayo fundamental sobre la identidad y el carácter del mexicano.',
                'category' => 'Ensayo',
                'price' => 48.00,
                'isbn' => '9786071604941',
                'pages' => 351,
                'published_year' => 1950,
                'stock' => 10,
            ],
        ];
    }
}
