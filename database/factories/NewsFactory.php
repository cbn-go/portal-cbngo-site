<?php

namespace Database\Factories;

use App\Models\Church;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'church_id' => null,
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'category' => fake()->randomElement(['Geral', 'Comunicado Oficial', 'Missões', 'Educação Teológica', 'Eventos', 'Ação Social']),
            'excerpt' => fake()->paragraph(2),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(4)).'</p>',
            'featured_image' => null,
            'gallery' => null,
            'event_date' => null,
            'is_official' => fake()->boolean(30),
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Notícia emitida por uma igreja local (sem igreja, a notícia é da CBN-GO Estadual).
     */
    public function forChurch(?Church $church = null): static
    {
        return $this->state(fn (array $attributes) => [
            'church_id' => $church instanceof Church ? $church->id : Church::factory(),
        ]);
    }

    /**
     * Rascunho ainda não enviado para revisão.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    /**
     * Notícia aguardando revisão no painel.
     */
    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending_review']);
    }

    /**
     * Notícia arquivada.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'archived']);
    }
}
