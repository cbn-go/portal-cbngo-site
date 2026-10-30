<?php

namespace App\Models;

use App\Enums\ProjectCategory;
use App\Support\ContactLinks;
use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Projeto social de uma igreja, gerido no painel administrativo (tabela `social_projects` do banco compartilhado).
 *
 * @property int $id
 * @property int $church_id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property ProjectCategory $category
 * @property string $summary
 * @property string $description
 * @property string|null $cover_image
 * @property array<int, string>|null $gallery
 * @property string|null $target_audience
 * @property int|null $beneficiaries_count
 * @property string $city
 * @property Carbon $started_at
 * @property Carbon|null $ended_at
 * @property string|null $contact_name
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property string|null $how_to_help
 * @property string $status
 * @property Carbon|null $published_at
 * @property bool $is_featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $cover_image_url
 * @property-read array<int, string> $gallery_urls
 * @property-read bool $is_ongoing
 * @property-read string $period_label
 * @property-read string|null $whatsapp_url
 * @property-read string|null $safe_contact_email
 * @property-read string|null $church_directory_url
 * @property-read Church|null $church
 *
 * @method static Builder<SocialProject> published()
 * @method static Builder<SocialProject> ordered()
 */
class SocialProject extends Model
{
    use HasFactory;

    /**
     * Status do painel que torna o projeto público (com published_at vencido).
     */
    public const STATUS_PUBLISHED = 'published';

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'church_id',
        'user_id',
        'title',
        'slug',
        'category',
        'summary',
        'description',
        'cover_image',
        'gallery',
        'target_audience',
        'beneficiaries_count',
        'city',
        'started_at',
        'ended_at',
        'contact_name',
        'contact_phone',
        'contact_email',
        'how_to_help',
        'status',
        'published_at',
        'is_featured',
        'review_notes',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category' => ProjectCategory::class,
        'gallery' => 'array',
        'beneficiaries_count' => 'integer',
        'started_at' => 'date',
        'ended_at' => 'date',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Igreja responsável ativa; nula se a igreja foi desativada no painel.
     *
     * @return BelongsTo<Church, $this>
     */
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class)->where('is_active', true);
    }

    /**
     * Escopo para filtrar apenas projetos publicados e com data de publicação vencida.
     *
     * @param  Builder<SocialProject>  $query
     * @return Builder<SocialProject>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Ordem pública: destaques primeiro, depois os publicados mais recentemente.
     *
     * @param  Builder<SocialProject>  $query
     * @return Builder<SocialProject>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /**
     * URL da imagem de capa no storage de mídia.
     *
     * @return Attribute<string|null, never>
     */
    protected function coverImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::url($this->cover_image));
    }

    /**
     * URLs das fotos da galeria, na ordem definida no painel.
     *
     * @return Attribute<array<int, string>, never>
     */
    protected function galleryUrls(): Attribute
    {
        return Attribute::get(fn (): array => Media::urls($this->gallery));
    }

    /**
     * Projeto em andamento: sem data de término ou com término hoje ou no futuro.
     *
     * @return Attribute<bool, never>
     */
    protected function isOngoing(): Attribute
    {
        return Attribute::get(fn (): bool => $this->ended_at === null || $this->ended_at->greaterThanOrEqualTo(today()));
    }

    /**
     * Período em texto curto: "Desde mar/2026" ou "mar/2026 – jun/2026".
     *
     * @return Attribute<string, never>
     */
    protected function periodLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $start = self::monthLabel($this->started_at);

            return $this->ended_at === null
                ? "Desde {$start}"
                : $start.' – '.self::monthLabel($this->ended_at);
        });
    }

    /**
     * Link de WhatsApp do contato, com mensagem pré-preenchida; null sem telefone válido (DDD + número).
     *
     * @return Attribute<string|null, never>
     */
    protected function whatsappUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $phone = (string) $this->contact_phone;
            $digits = ContactLinks::digits($phone);

            if (strlen($digits) < 12 || strlen($digits) > 13) {
                return null;
            }

            return ContactLinks::whatsapp($phone).'?text='.rawurlencode("Olá! Quero ajudar no projeto \"{$this->title}\".");
        });
    }

    /**
     * E-mail de contato validado, ou null se ausente/inválido.
     *
     * @return Attribute<string|null, never>
     */
    protected function safeContactEmail(): Attribute
    {
        return Attribute::get(function (): ?string {
            $email = trim((string) $this->contact_email);

            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : null;
        });
    }

    /**
     * Link para a página da igreja responsável.
     *
     * @return Attribute<string|null, never>
     */
    protected function churchDirectoryUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->church
            ? route('igrejas.show', $this->church->slug)
            : null);
    }

    /**
     * Mês abreviado em português com ano (ex.: "mar/2026").
     */
    private static function monthLabel(Carbon $date): string
    {
        $months = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

        return $months[$date->month - 1].'/'.$date->year;
    }
}
