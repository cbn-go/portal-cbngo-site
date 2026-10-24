<?php

namespace Database\Factories;

use App\Models\UrgentNotice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UrgentNotice>
 */
class UrgentNoticeFactory extends Factory
{
    protected $model = UrgentNotice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'user_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'content' => '<p>'.fake()->sentence(12).'</p>',
            'action_url' => fake()->url(),
            'action_label' => 'Saiba mais',
            'priority' => fake()->randomElement(['normal', 'high', 'urgent']),
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
        ];
    }
}
