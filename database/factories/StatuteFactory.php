<?php

namespace Database\Factories;

use App\Models\Statute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Statute>
 */
class StatuteFactory extends Factory
{
    protected $model = Statute::class;

    /**
     * Estatuto vigente (seed do painel), em Akoma Ntoso 3.0.
     */
    public const FIXTURE = 'tests/fixtures/estatuto-2025-10-18.akn.xml';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Estatuto da Convenção Batista Nacional do Estado de Goiás',
            'version_label' => 'v'.fake()->unique()->numberBetween(2000, 9999),
            'approved_at' => '2025-10-18',
            'approved_place' => 'Anápolis',
            'approval_body' => 'Assembleia Geral',
            'minutes_reference' => 'Ata da Assembleia Geral Extraordinária de 18 de outubro de 2025',
            'registry_info' => 'Cartório de Pessoas Jurídicas (Averbação à margem do registro nº 806 em 22/01/2026)',
            'effective_at' => '2025-10-18',
            'status' => 'published',
            'akn_xml' => (string) file_get_contents(base_path(self::FIXTURE)),
            'original_pdf' => 'statutes/04-estatuto-cbngo-atualizado.pdf',
            'published_at' => now()->subDay(),
        ];
    }

    /**
     * Versão anterior, mantida como histórico.
     */
    public function archived(string $approvedAt = '2019-03-16'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'approved_at' => $approvedAt,
            'effective_at' => $approvedAt,
            'published_at' => null,
        ]);
    }

    /**
     * Rascunho em edição no painel.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft', 'published_at' => null]);
    }
}
