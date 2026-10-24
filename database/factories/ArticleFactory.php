<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'user_id' => fn () => Author::factory()->create()->user_id,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'category' => fake()->randomElement(['Teologia', 'Missões', 'Liderança', 'Família', 'Discipulado', 'Devocional']),
            'excerpt' => fake()->paragraph(2),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(5)).'</p>',
            'reading_time' => fake()->numberBetween(3, 10),
            'cover_image' => null,
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Artigo escrito pelo usuário dono do perfil de autor informado.
     */
    public function byAuthor(Author $author): static
    {
        return $this->state(fn (array $attributes) => ['user_id' => $author->user_id]);
    }

    /**
     * Rascunho ainda não enviado para revisão.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    /**
     * Artigo aguardando revisão no painel.
     */
    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending_review']);
    }

    /**
     * Artigo arquivado.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'archived']);
    }
}
