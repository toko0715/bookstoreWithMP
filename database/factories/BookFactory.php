<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim($this->faker->unique()->sentence(3), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['Novela', 'Cuento', 'Ensayo', 'Poesía']),
            'price' => $this->faker->randomFloat(2, 25, 120),
            'cover_url' => null,
            'isbn' => $this->faker->isbn13(),
            'pages' => $this->faker->numberBetween(80, 800),
            'published_year' => $this->faker->numberBetween(1950, 2024),
            'stock' => $this->faker->numberBetween(0, 30),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }
}
