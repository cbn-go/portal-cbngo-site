<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'role_title' => fake()->randomElement(['Pastor e Teólogo', 'Articulista CBN-GO', 'Presidente da CBN-GO', 'Coordenador Teológico']),
            'avatar_url' => null,
            'bio' => fake()->paragraph(),
        ];
    }
}
