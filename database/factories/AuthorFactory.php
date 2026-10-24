<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\User;
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
        return [
            'user_id' => User::factory()->author(),
            'slug' => fn (array $attributes) => Str::slug((string) User::query()->whereKey($attributes['user_id'])->value('name'))
                .'-'.fake()->unique()->numberBetween(100, 999),
            'pastoral_title' => fake()->randomElement(['Pastor e Teólogo', 'Presidente da CBN-GO', 'Coordenador Teológico']),
            'avatar' => null,
            'bio' => fake()->paragraph(),
            'social_links' => null,
        ];
    }

    /**
     * Perfil de autor de um usuário com o nome informado.
     */
    public function named(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->author()->state(['name' => $name]),
        ]);
    }

    /**
     * Perfil cujo usuário foi desativado no painel.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->author()->state(['is_active' => false]),
        ]);
    }
}
