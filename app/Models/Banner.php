<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Banner do hero da home, cadastrado no painel administrativo (tabela `banners` do banco compartilhado).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string|null $subtitle
 * @property string $image
 * @property string $focal_point
 * @property string $alt_text
 * @property string|null $link_url
 * @property string|null $link_label
 * @property bool $opens_in_new_tab
 * @property int $position
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 * @property-read string|null $image_srcset
 * @property-read string $object_position
 * @property-read string|null $link
 * @property-read string $link_text
 * @property-read bool $is_external
 * @property-read bool $opens_new_tab
 *
 * @method static Builder<Banner> visible()
 */
class Banner extends Model
{
    use HasFactory;

    /**
     * Ponto focal do painel → object-position (BannerFocalPoint::objectPosition() no painel).
     *
     * @var array<string, string>
     */
    public const OBJECT_POSITIONS = [
        'center' => 'center center',
        'left' => 'left center',
        'right' => 'right center',
    ];

    /**
     * Texto do botão quando o banner tem link e link_label está vazio.
     */
    public const DEFAULT_LINK_LABEL = 'Saiba mais';

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'image',
        'focal_point',
        'alt_text',
        'link_url',
        'link_label',
        'opens_in_new_tab',
        'position',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opens_in_new_tab' => 'boolean',
        'position' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Escopo dos banners exibidos agora no hero (ativos e vigentes), na ordem definida no painel.
     *
     * @param  Builder<Banner>  $query
     * @return Builder<Banner>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('position')
            ->orderBy('id');
    }

    /**
     * URL pública da imagem original no storage de mídia compartilhado.
     *
     * @return Attribute<string|null, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::url($this->image));
    }

    /**
     * srcset com as variantes WebP geradas pelo painel.
     *
     * @return Attribute<string|null, never>
     */
    protected function imageSrcset(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::srcset($this->image));
    }

    /**
     * Corte da imagem em telas estreitas a partir do ponto focal, com fallback no centro.
     *
     * @return Attribute<string, never>
     */
    protected function objectPosition(): Attribute
    {
        return Attribute::get(fn (): string => self::OBJECT_POSITIONS[(string) $this->focal_point] ?? self::OBJECT_POSITIONS['center']);
    }

    /**
     * Link do botão apenas com esquema http/https ou caminho relativo local (proteção XSS).
     *
     * @return Attribute<string|null, never>
     */
    protected function link(): Attribute
    {
        return Attribute::get(function (): ?string {
            $url = trim((string) $this->link_url);

            if (preg_match('/^https?:\/\//i', $url) || (str_starts_with($url, '/') && ! str_starts_with($url, '//'))) {
                return $url;
            }

            return null;
        });
    }

    /**
     * Rótulo do botão, com fallback "Saiba mais".
     *
     * @return Attribute<string, never>
     */
    protected function linkText(): Attribute
    {
        return Attribute::get(fn (): string => trim((string) $this->link_label) ?: self::DEFAULT_LINK_LABEL);
    }

    /**
     * Link absoluto para um host diferente do portal.
     *
     * @return Attribute<bool, never>
     */
    protected function isExternal(): Attribute
    {
        return Attribute::get(function (): bool {
            $link = $this->link;

            if ($link === null || str_starts_with($link, '/')) {
                return false;
            }

            $host = strtolower((string) parse_url($link, PHP_URL_HOST));
            $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

            return $host !== $appHost;
        });
    }

    /**
     * Abre em nova aba quando marcado no painel ou quando o link é externo.
     *
     * @return Attribute<bool, never>
     */
    protected function opensNewTab(): Attribute
    {
        return Attribute::get(fn (): bool => $this->opens_in_new_tab || $this->is_external);
    }
}
