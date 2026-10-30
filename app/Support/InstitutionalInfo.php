<?php

namespace App\Support;

use App\Models\InstitutionalSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PDOException;

/**
 * Fonte única dos dados institucionais exibidos no site (barra superior, rodapé, /contato e JSON-LD).
 * Lê o registro do painel (Issue #37) e cai no config/cbngo.php quando ele não existe ou o banco falha.
 */
class InstitutionalInfo
{
    /**
     * Redes exibidas no site, na ordem em que aparecem.
     */
    public const NETWORKS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'spotify' => 'Spotify',
        'linkedin' => 'LinkedIn',
    ];

    /**
     * @param  array<string, string|null>  $data  colunas de contato e endereço, mais `{rede}_url` para cada rede
     */
    public function __construct(private readonly array $data) {}

    /**
     * Carrega os dados do painel, em cache versionado pelo updated_at do registro.
     */
    public static function load(): self
    {
        try {
            $version = InstitutionalSetting::query()
                ->toBase()
                ->selectRaw('max(updated_at) as last_updated, count(*) as total')
                ->first();

            $key = 'institucional.'.md5($version?->last_updated.'|'.$version?->total);

            $data = Cache::remember($key, (int) config('cbngo.institutional.cache_ttl'), function (): ?array {
                /** @var array<string, string|null>|null $attributes */
                $attributes = InstitutionalSetting::query()->first()?->only([
                    'email', 'phone', 'whatsapp', 'office_hours',
                    'street', 'number', 'complement', 'district', 'city', 'state', 'postal_code', 'maps_url',
                    ...array_map(fn (string $network) => $network.'_url', array_keys(self::NETWORKS)),
                ]);

                return $attributes;
            });
        } catch (QueryException|PDOException $exception) {
            Log::warning('Dados institucionais indisponíveis; usando o fallback do config.', ['error' => $exception->getMessage()]);
            $data = null;
        }

        return new self($data ?? self::fallback());
    }

    /**
     * Valores de config/cbngo.php no mesmo formato da tabela do painel.
     *
     * @return array<string, string|null>
     */
    public static function fallback(): array
    {
        $data = (array) config('cbngo.contact');

        foreach ((array) config('cbngo.social') as $network => $url) {
            $data[$network.'_url'] = $url;
        }

        return $data;
    }

    public function email(): string
    {
        return (string) $this->value('email');
    }

    public function phone(): ?string
    {
        return $this->value('phone');
    }

    public function whatsapp(): ?string
    {
        return $this->value('whatsapp');
    }

    public function officeHours(): ?string
    {
        return $this->value('office_hours');
    }

    public function phoneHref(): ?string
    {
        return ContactLinks::tel((string) $this->phone());
    }

    public function whatsappUrl(): ?string
    {
        return ContactLinks::whatsapp((string) $this->whatsapp());
    }

    /**
     * Endereço em linha única, ex.: "Rua 04, 515, 12º andar… - Setor Central, Goiânia - GO, CEP 74020-045".
     */
    public function address(): string
    {
        $postalCode = $this->value('postal_code');

        return $this->join(', ', [
            $this->join(' - ', [$this->streetLine(), $this->value('district')]),
            $this->cityLine(),
            $postalCode !== null ? 'CEP '.$postalCode : null,
        ]);
    }

    /**
     * Endereço em linhas: logradouro; bairro e cidade; CEP.
     *
     * @return list<string>
     */
    public function addressLines(): array
    {
        $postalCode = $this->value('postal_code');

        return array_values(array_filter([
            $this->streetLine(),
            $this->join(', ', [$this->value('district'), $this->cityLine()]),
            $postalCode !== null ? 'CEP '.$postalCode : '',
        ], fn (string $line) => $line !== ''));
    }

    /**
     * Endereço no formato schema.org/PostalAddress.
     *
     * @return array<string, string|null>
     */
    public function postalAddress(): array
    {
        return [
            '@type' => 'PostalAddress',
            'streetAddress' => $this->join(' - ', [$this->streetLine(), $this->value('district')]) ?: null,
            'addressLocality' => $this->value('city'),
            'addressRegion' => $this->value('state'),
            'postalCode' => $this->value('postal_code'),
            'addressCountry' => 'BR',
        ];
    }

    /**
     * Link do "Como chegar": o do painel ou a busca do endereço no Google Maps.
     */
    public function mapsUrl(): string
    {
        return $this->value('maps_url') ?? ContactLinks::maps($this->address());
    }

    /**
     * Redes preenchidas, na ordem de NETWORKS.
     *
     * @return array<string, array{url: string, label: string}>
     */
    public function socialLinks(): array
    {
        $links = [];

        foreach (self::NETWORKS as $network => $label) {
            $url = $this->value($network.'_url');

            if ($url !== null) {
                $links[$network] = ['url' => $url, 'label' => $label];
            }
        }

        return $links;
    }

    /**
     * Valor preenchido do campo, ou null para vazio.
     */
    private function value(string $key): ?string
    {
        $value = trim((string) ($this->data[$key] ?? ''));

        return $value !== '' ? $value : null;
    }

    private function streetLine(): string
    {
        return $this->join(', ', [$this->value('street'), $this->value('number'), $this->value('complement')]);
    }

    private function cityLine(): string
    {
        return $this->join(' - ', [$this->value('city'), $this->value('state')]);
    }

    /**
     * @param  array<int, string|null>  $parts
     */
    private function join(string $separator, array $parts): string
    {
        return implode($separator, array_filter($parts, fn (?string $part) => $part !== null && $part !== ''));
    }
}
