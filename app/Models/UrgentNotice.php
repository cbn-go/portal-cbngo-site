<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Aviso oficial gerido no painel administrativo (tabela `notices` do banco compartilhado),
 * exibido como banner urgente na home.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $action_url
 * @property string|null $action_label
 * @property string $priority
 * @property bool $is_active
 * @property Carbon|null $starts_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $message
 * @property-read string|null $link
 * @property-read string $link_text
 * @property-read string $type
 *
 * @method static Builder<UrgentNotice> active()
 * @method static Builder<UrgentNotice> byPriority()
 */
class UrgentNotice extends Model
{
    use HasFactory;

    /**
     * Prioridade do painel → tipo visual do banner (NoticePriority::publicBannerType() no painel).
     *
     * @var array<string, string>
     */
    public const BANNER_TYPES = [
        'normal' => 'info',
        'high' => 'warning',
        'urgent' => 'critical',
    ];

    /**
     * Tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'notices';

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'action_url',
        'action_label',
        'priority',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Escopo para filtrar apenas avisos ativos e dentro do período de vigência temporal.
     *
     * @param  Builder<UrgentNotice>  $query
     * @return Builder<UrgentNotice>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Ordena pela maior prioridade (urgent > high > normal) e, no empate, pelo mais recente.
     *
     * @param  Builder<UrgentNotice>  $query
     * @return Builder<UrgentNotice>
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderByRaw("CASE priority WHEN 'urgent' THEN 3 WHEN 'high' THEN 2 ELSE 1 END DESC")
            ->orderByDesc('starts_at')
            ->latest('id');
    }

    /**
     * Texto do aviso: o painel não sanitiza o HTML de `content`, então o banner exibe só o texto.
     *
     * @return Attribute<string, never>
     */
    protected function message(): Attribute
    {
        return Attribute::get(fn (): string => trim((string) preg_replace(
            '/\s+/u',
            ' ',
            html_entity_decode(strip_tags((string) preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', (string) $this->content)), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        )));
    }

    /**
     * Link de ação apenas com esquema http/https ou caminho relativo local (proteção XSS).
     *
     * @return Attribute<string|null, never>
     */
    protected function link(): Attribute
    {
        return Attribute::get(function (): ?string {
            $url = trim((string) $this->action_url);

            if (preg_match('/^https?:\/\//i', $url) || (str_starts_with($url, '/') && ! str_starts_with($url, '//'))) {
                return $url;
            }

            return null;
        });
    }

    /**
     * Rótulo do link de ação, com fallback "Saiba mais".
     *
     * @return Attribute<string, never>
     */
    protected function linkText(): Attribute
    {
        return Attribute::get(fn (): string => trim((string) $this->action_label) ?: 'Saiba mais');
    }

    /**
     * Tipo visual do banner (info, warning, critical) a partir da prioridade do painel.
     *
     * @return Attribute<string, never>
     */
    protected function type(): Attribute
    {
        return Attribute::get(fn (): string => self::BANNER_TYPES[$this->priority] ?? 'warning');
    }
}
