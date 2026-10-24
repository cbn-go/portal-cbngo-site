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
        return [
            'name' => 'Pr. '.$this->faker->name(),
            'role' => 'Membro da Diretoria',
            'group' => $this->faker->randomElement(array_keys(BoardMember::GROUPS)),
            'region' => null,
            'photo' => null,
            'term_label' => 'Gestão 2025–2027',
            'position' => 0,
            'is_active' => true,
        ];
    }
}
