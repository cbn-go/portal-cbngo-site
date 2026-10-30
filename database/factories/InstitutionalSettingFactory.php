<?php

namespace Database\Factories;

use App\Models\InstitutionalSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionalSetting>
 */
class InstitutionalSettingFactory extends Factory
{
    protected $model = InstitutionalSetting::class;

    /**
     * Sede oficial (Art. 2º do Estatuto) e redes atuais, como no seed do painel.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => 'contato@cbngo.org.br',
            'phone' => '(62) 3223-0000',
            'whatsapp' => '(62) 98100-0000',
            'office_hours' => 'Segunda a sexta, das 9h às 18h30',
            'street' => 'Rua 04',
            'number' => '515',
            'complement' => '12º andar, sala 1.207, Edifício Parthenon Center',
            'district' => 'Setor Central',
            'city' => 'Goiânia',
            'state' => 'GO',
            'postal_code' => '74020-045',
            'maps_url' => null,
            'instagram_url' => 'https://www.instagram.com/cbn_go/',
            'facebook_url' => 'https://www.facebook.com/cbnacionalgoias',
            'youtube_url' => 'https://youtube.com/@cbngoias',
            'tiktok_url' => null,
            'spotify_url' => null,
            'linkedin_url' => null,
        ];
    }
}
