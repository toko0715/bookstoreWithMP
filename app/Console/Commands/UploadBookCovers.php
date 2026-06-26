<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;

class UploadBookCovers extends Command
{
    protected $signature = 'covers:cloudinary {--force : Re-subir también las portadas que ya están en Cloudinary}';

    protected $description = 'Sube las portadas de los libros a Cloudinary y guarda la URL resultante en la base de datos';

    public function handle(CloudinaryService $cloudinary): int
    {
        if (! $cloudinary->configured()) {
            $this->error('Cloudinary no está configurado. Define CLOUDINARY_URL (o las variables CLOUDINARY_*) en tu .env.');

            return self::FAILURE;
        }

        $books = Book::query()
            ->whereNotNull('cover_url')
            ->when(! $this->option('force'), fn ($query) => $query->where('cover_url', 'not like', '%res.cloudinary.com%'))
            ->get();

        if ($books->isEmpty()) {
            $this->info('No hay portadas pendientes. Usa --force para re-subirlas todas.');

            return self::SUCCESS;
        }

        $this->info("Subiendo {$books->count()} portada(s) a Cloudinary…");
        $failures = 0;

        $this->withProgressBar($books, function (Book $book) use ($cloudinary, &$failures) {
            try {
                $url = $cloudinary->upload($book->cover_url, 'libro-'.$book->slug);
                $book->update(['cover_url' => $url]);
            } catch (\Throwable $e) {
                $failures++;
                $this->newLine();
                $this->warn("  ✗ {$book->title}: {$e->getMessage()}");
            }
        });

        $this->newLine(2);
        $this->info('Listo. Portadas subidas: '.($books->count() - $failures).' de '.$books->count().'.');

        return self::SUCCESS;
    }
}
