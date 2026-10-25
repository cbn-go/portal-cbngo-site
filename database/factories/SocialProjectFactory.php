<?php

namespace Database\Factories;

use App\Enums\ProjectCategory;
use App\Models\Church;
use App\Models\SocialProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SocialProject>
 */
class SocialProjectFactory extends Factory
{
    protected $model = SocialProject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Projeto '.fake()->words(3, true);

        return [
            'church_id' => Church::factory(),
            'user_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'category' => fake()->randomElement(ProjectCategory::cases()),
            'summary' => fake()->sentence(18),
            'description' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
            'cover_image' => null,
            'gallery' => null,
            'target_audience' => 'Famílias em situação de vulnerabilidade',
            'beneficiaries_count' => fake()->numberBetween(20, 400),
            'city' => 'Goiânia',
            'started_at' => now()->subMonths(6)->startOfMonth(),
            'ended_at' => null,
            'contact_name' => fake()->name(),
            'contact_phone' => null,
            'contact_email' => null,
            'how_to_help' => 'Seja voluntário aos sábados ou doe alimentos não perecíveis.',
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 month', '-1 hour'),
            'is_featured' => false,
            'review_notes' => null,
        ];
    }

    /**
     * Projeto de uma igreja específica (a cidade acompanha a da igreja).
     */
    public function forChurch(Church $church): static
    {
        return $this->state(fn (array $attributes) => [
            'church_id' => $church->id,
            'city' => $church->city,
        ]);
    }

    /**
     * Projeto de uma categoria específica.
     */
    public function category(ProjectCategory $category): static
    {
        return $this->state(fn (array $attributes) => ['category' => $category]);
    }

    /**
     * Projeto destacado pela administração.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }

    /**
     * Projeto já encerrado.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => now()->subYear()->startOfMonth(),
            'ended_at' => now()->subMonths(2)->endOfMonth(),
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
     * Projeto aguardando revisão no painel.
     */
    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending_review']);
    }

    /**
     * Projeto arquivado.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'archived']);
    }
}
