<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Dados institucionais da CBN-GO (contato, endereço da sede e redes sociais), mantidos pela secretaria
 * no painel administrativo (tabela `institutional_settings` do banco compartilhado, registro único).
 * O site só lê; veja App\Support\InstitutionalInfo.
 *
 * @property int $id
 * @property string $email
 * @property string|null $phone
 * @property string|null $whatsapp
 * @property string|null $office_hours
 * @property string|null $street
 * @property string|null $number
 * @property string|null $complement
 * @property string|null $district
 * @property string $city
 * @property string $state
 * @property string|null $postal_code
 * @property string|null $maps_url
 * @property string|null $instagram_url
 * @property string|null $facebook_url
 * @property string|null $youtube_url
 * @property string|null $tiktok_url
 * @property string|null $spotify_url
 * @property string|null $linkedin_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InstitutionalSetting extends Model
{
    use HasFactory;

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'phone',
        'whatsapp',
        'office_hours',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'postal_code',
        'maps_url',
        'instagram_url',
        'facebook_url',
        'youtube_url',
        'tiktok_url',
        'spotify_url',
        'linkedin_url',
    ];
}
