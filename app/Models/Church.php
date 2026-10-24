<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\ChurchFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Igreja filiada gerida no painel administrativo (tabela `churches` do banco compartilhado).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo
 * @property string|null $pastor_name
 * @property string|null $address
 * @property string|null $number
 * @property string|null $complement
 * @property string|null $neighborhood
 * @property string $city
 * @property string $state
 * @property string|null $zip_code
 * @property float|string|null $latitude
 * @property float|string|null $longitude
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $email
 * @property array<string, string>|null $social_links
 * @property array<int, array{day: string, time: string, label: string}>|null $service_times
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $logo_url
 */
class Church extends Model
{
    /** @use HasFactory<ChurchFactory> */
    use HasFactory;

    /**
     * Limites geográficos estendidos do estado de Goiás para validação de coordenadas nas rotas externas.
     */
    private const MIN_LAT = -19.60;

    private const MAX_LAT = -12.35;

    private const MIN_LNG = -53.30;

    private const MAX_LNG = -45.85;

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'pastor_name',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'phone',
        'cellphone',
        'email',
        'social_links',
        'service_times',
        'is_active',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'social_links' => 'array',
        'service_times' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Escopo para congregações ativas no guia público.
     *
     * @param  Builder<Church>  $query
     * @return Builder<Church>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Notícias emitidas pela igreja.
     *
     * @return HasMany<News, $this>
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    /**
     * URL do logotipo no storage de mídia.
     *
     * @return Attribute<string|null, never>
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::url($this->logo));
    }

    /**
     * Logradouro no formato do painel: "address, number – complement", ignorando partes vazias.
     */
    public function getStreetAddress(): string
    {
        $street = implode(', ', array_filter(
            [trim((string) $this->address), trim((string) $this->number)],
            fn (string $part): bool => $part !== ''
        ));

        $complement = trim((string) $this->complement);

        if ($complement === '') {
            return $street;
        }

        return $street === '' ? $complement : $street.' – '.$complement;
    }

    /**
     * Verifica se a congregação possui coordenadas válidas dentro da faixa geográfica de Goiás.
     */
    public function hasValidCoordinates(): bool
    {
        if ($this->latitude === null || $this->longitude === null) {
            return false;
        }

        if (! is_numeric($this->latitude) || ! is_numeric($this->longitude)) {
            return false;
        }

        $lat = (float) $this->latitude;
        $lng = (float) $this->longitude;

        return $lat >= self::MIN_LAT
            && $lat <= self::MAX_LAT
            && $lng >= self::MIN_LNG
            && $lng <= self::MAX_LNG;
    }

    /**
     * Retorna o endereço completo formatado para exibição e busca de rotas.
     */
    public function getFullAddress(): string
    {
        $parts = [
            $this->getStreetAddress(),
            trim((string) $this->neighborhood),
            trim($this->city).' - '.trim($this->state ?: 'GO'),
        ];

        if ($this->zip_code !== null && trim($this->zip_code) !== '') {
            $parts[] = 'CEP '.trim($this->zip_code);
        }

        return implode(', ', array_filter($parts, fn (string $part): bool => $part !== ''));
    }

    /**
     * Gera URL segura para rota no Google Maps (priorizando coordenadas válidas ou usando endereço completo).
     */
    public function getGoogleMapsUrl(): string
    {
        if ($this->hasValidCoordinates()) {
            $destination = rawurlencode(((float) $this->latitude).','.((float) $this->longitude));

            return "https://www.google.com/maps/dir/?api=1&destination={$destination}";
        }

        $destination = rawurlencode($this->getFullAddress());

        return "https://www.google.com/maps/dir/?api=1&destination={$destination}";
    }

    /**
     * Gera URL segura para navegação no Waze (priorizando coordenadas válidas ou usando endereço completo).
     */
    public function getWazeUrl(): string
    {
        if ($this->hasValidCoordinates()) {
            $ll = rawurlencode(((float) $this->latitude).','.((float) $this->longitude));

            return "https://waze.com/ul?ll={$ll}&navigate=yes";
        }

        $query = rawurlencode($this->getFullAddress());

        return "https://waze.com/ul?q={$query}&navigate=yes";
    }

    /**
     * Retorna o e-mail institucional validado ou null se ausente/inválido.
     */
    public function getSafeEmail(): ?string
    {
        if ($this->email === null) {
            return null;
        }

        $trimmed = trim($this->email);
        if ($trimmed === '') {
            return null;
        }

        return filter_var($trimmed, FILTER_VALIDATE_EMAIL) !== false ? $trimmed : null;
    }

    /**
     * Retorna os links sociais sanitizados com verificação estrita de esquema (http, https).
     *
     * @return array<string, string>
     */
    public function getSafeSocialLinks(): array
    {
        if (! is_array($this->social_links)) {
            return [];
        }

        $safe = [];
        foreach ($this->social_links as $network => $link) {
            $trimmed = trim((string) $link);

            if (preg_match('/^https?:\/\//i', $trimmed)) {
                $safe[(string) $network] = $trimmed;
            }
        }

        return $safe;
    }
}
