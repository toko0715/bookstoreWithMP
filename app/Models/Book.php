<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author',
        'description',
        'category',
        'price',
        'cover_url',
        'isbn',
        'pages',
        'published_year',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'pages' => 'integer',
            'published_year' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Búsqueda por título o autor.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%");
        });
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Degradado determinístico (derivado del título) que se usa como portada
     * de respaldo cuando la imagen remota no carga.
     *
     * @return array{0:string,1:string}
     */
    public function coverGradient(): array
    {
        $hash = crc32((string) ($this->title ?: 'libro'));
        $from = $hash % 360;
        $to = ($from + 35) % 360;

        return [
            "hsl({$from} 42% 30%)",
            "hsl({$to} 55% 18%)",
        ];
    }
}
