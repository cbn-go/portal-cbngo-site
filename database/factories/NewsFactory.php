<?php

namespace Database\Factories;

use App\Models\News;
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
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'category' => fake()->randomElement(['Geral', 'Comunicado Oficial', 'Missões', 'Eventos', 'Juventude']),
            'excerpt' => fake()->paragraph(2),
            'content' => fake()->paragraphs(4, true),
            'image_url' => null,
            'church_name' => 'Igreja Batista Nacional '.fake()->city(),
            'city' => fake()->city(),
            'is_official' => fake()->boolean(30),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
