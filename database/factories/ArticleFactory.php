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
            'author_id' => Author::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'excerpt' => fake()->paragraph(2),
            'content' => fake()->paragraphs(5, true),
            'reading_time' => fake()->numberBetween(3, 10),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
