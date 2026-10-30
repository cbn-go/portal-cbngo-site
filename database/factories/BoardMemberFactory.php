<?php

namespace Database\Factories;

use App\Models\BoardMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardMember>
 */
class BoardMemberFactory extends Factory
{
    protected $model = BoardMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $group = $this->faker->randomElement(array_keys(BoardMember::GROUPS));

        return [
            'title' => 'Pr.',
            'name' => $this->faker->name(),
            'role' => 'Membro da Diretoria',
            'group' => $group,
            'region' => $group === 'coordenacao_regional' ? $this->faker->randomElement(array_keys(BoardMember::REGIONS)) : null,
            'photo' => null,
            'term_label' => 'Triênio 2026–2029',
            'term_starts_on' => '2026-10-01',
            'term_ends_on' => '2029-09-30',
            'position' => 0,
            'is_active' => true,
        ];
    }
}
