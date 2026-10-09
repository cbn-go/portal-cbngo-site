<?php

namespace Database\Factories;

use App\Models\UrgentNotice;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'title' => fake()->sentence(4),
            'message' => fake()->sentence(12),
            'link' => fake()->url(),
            'link_text' => 'Saiba mais',
            'type' => fake()->randomElement(['warning', 'critical', 'info']),
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(7),
        ];
    }
}
