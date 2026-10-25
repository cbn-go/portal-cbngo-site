<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'title' => fake()->sentence(4),
            'subtitle' => fake()->sentence(10),
            'image' => 'banners/'.fake()->unique()->uuid().'.jpg',
            'focal_point' => 'center',
            'alt_text' => fake()->sentence(6),
            'link_url' => '/noticias',
            'link_label' => 'Saiba mais',
            'opens_in_new_tab' => false,
            'position' => fake()->unique()->numberBetween(1, 1000),
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => true,
        ];
    }

    /**
     * Banner desativado no painel.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    /**
     * Banner cuja vigência já terminou.
     */
    public function expired(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);
    }

    /**
     * Banner com vigência ainda por começar.
     */
    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(10),
        ]);
    }

    /**
     * Banner com link para site externo.
     */
    public function external(): static
    {
        return $this->state(fn (): array => [
            'link_url' => 'https://www.cbn.org.br/campanha',
            'opens_in_new_tab' => true,
        ]);
    }
}
