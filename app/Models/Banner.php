<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Aviso do hero da home ("Avisos da Home" no painel; tabela `banners` do banco compartilhado):
 * foto 1:1 com selo, título, subtítulo, botão e linha de data/local (Issue #39).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string|null $tag
 * @property string|null $subtitle
 * @property string|null $meta
 * @property string $image
 * @property string $alt_text
 * @property string|null $link_url
 * @property string|null $link_label
 * @property bool $opens_in_new_tab
 * @property int $position
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property bool $is_active
 * @property bool $is_square
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 * @property-read string|null $image_srcset
 * @property-read string|null $thumb_url
 * @property-read string $button_label
 * @property-read string|null $link
 * @property-read bool $is_external
 * @property-read bool $opens_new_tab
 *
 * @method static Builder<Banner> visible()
 */
class Banner extends Model
{
    use HasFactory;

    /**
     * Dimensões da foto 1:1 do painel (Banner::RECOMMENDED_WIDTH/HEIGHT no painel),
     * usadas para reservar a proporção da foto e evitar CLS.
     */
    public const WIDTH = 1080;

    public const HEIGHT = 1080;

    /**
     * Variantes WebP quadradas geradas pelo painel para banners, com as larguras reais.
     */
    public const SRCSET = ['thumb' => '160w', 'card' => '800w', 'cover' => '1080w'];

    /**
     * Texto do botão quando o painel não informa link_label.
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
        'tag',
        'subtitle',
        'meta',
        'image',
        'alt_text',
        'link_url',
        'link_label',
        'opens_in_new_tab',
        'position',
        'starts_at',
        'ends_at',
        'is_active',
        'is_square',
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
        'is_square' => 'boolean',
    ];

    /**
     * Escopo dos avisos exibidos agora no hero (ativos, vigentes e com foto 1:1), na ordem definida no painel.
     * Os banners 16:9 antigos ficam com is_square = false até receberem uma foto quadrada.
     *
     * @param  Builder<Banner>  $query
     * @return Builder<Banner>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('is_square', true)
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
     * URL da foto (variante WebP cover de 1080px, com fallback no original) no storage de mídia compartilhado.
     *
     * @return Attribute<string|null, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::variant($this->image, 'cover') ?? Media::url($this->image));
    }

    /**
     * srcset com as variantes WebP quadradas geradas pelo painel.
     *
     * @return Attribute<string|null, never>
     */
    protected function imageSrcset(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::srcset($this->image, self::SRCSET));
    }

    /**
     * Versão leve da foto (variante thumb de 160px, com fallback no original) para miniaturas e fundo desfocado.
     *
     * @return Attribute<string|null, never>
     */
    protected function thumbUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::variant($this->image, 'thumb') ?? Media::url($this->image));
    }

    /**
     * Texto do botão do aviso.
     *
     * @return Attribute<string, never>
     */
    protected function buttonLabel(): Attribute
    {
        return Attribute::get(fn (): string => trim((string) $this->link_label) ?: self::DEFAULT_LINK_LABEL);
    }

    /**
     * Link do banner apenas com esquema http/https ou caminho relativo local (proteção XSS).
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
