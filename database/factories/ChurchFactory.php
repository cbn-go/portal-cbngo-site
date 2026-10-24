<?php

namespace Database\Factories;

use App\Models\Church;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Church>
 */
class ChurchFactory extends Factory
{
    protected $model = Church::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = [
            ['city' => 'Goiânia', 'lat' => -16.6869, 'lng' => -49.2648],
            ['city' => 'Anápolis', 'lat' => -16.3285, 'lng' => -48.9534],
            ['city' => 'Rio Verde', 'lat' => -17.7923, 'lng' => -50.9192],
            ['city' => 'Aparecida de Goiânia', 'lat' => -16.8233, 'lng' => -49.2439],
            ['city' => 'Jataí', 'lat' => -17.8814, 'lng' => -51.7144],
            ['city' => 'Luziânia', 'lat' => -16.2525, 'lng' => -47.9503],
        ];

        $chosen = $this->faker->randomElement($cities);
        $name = 'Igreja Batista Nacional '.$this->faker->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo' => null,
            'pastor_name' => 'Pr. '.$this->faker->name('male'),
            'address' => 'Rua '.$this->faker->lastName(),
            'number' => (string) $this->faker->buildingNumber(),
            'complement' => null,
            'neighborhood' => 'Setor '.$this->faker->word(),
            'city' => $chosen['city'],
            'state' => 'GO',
            'zip_code' => '74000-000',
            'latitude' => $chosen['lat'],
            'longitude' => $chosen['lng'],
            'phone' => '(62) 3200-0000',
            'cellphone' => null,
            'email' => $this->faker->safeEmail(),
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto de Celebração'],
                ['day' => 'Quarta-feira', 'time' => '20h00', 'label' => 'Reunião de Oração'],
            ],
            'is_active' => true,
        ];
    }
}
