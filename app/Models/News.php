<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Notícia gerida no painel administrativo (tabela `news` do banco compartilhado).
 *
 * @property int $id
 * @property int|null $church_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $category
 * @property string|null $excerpt
 * @property string $content
 * @property string|null $featured_image
 * @property array<int, string>|null $gallery
 * @property Carbon|null $event_date
 * @property bool $is_official
 * @property string $status
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $featured_image_url
 * @property-read array<int, string> $gallery_urls
 * @property-read Church|null $church
 *
 * @method static Builder<News> published()
 */
class News extends Model
{
    use HasFactory;

    /**
     * Status do painel que torna o conteúdo público (com published_at vencido).
     */
    public const STATUS_PUBLISHED = 'published';

    /**
     * Nome exibido quando a notícia não tem igreja emissora (notícia da CBN-GO Estadual).
     */
    public const STATE_PUBLISHER = 'CBN Goiás';

    /**
     * Tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'news';

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
        'excerpt',
        'content',
        'featured_image',
        'gallery',
        'event_date',
        'is_official',
        'status',
        'published_at',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gallery' => 'array',
        'event_date' => 'date',
        'is_official' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Igreja emissora ativa; nula para notícias da CBN-GO Estadual ou igrejas desativadas.
     *
     * @return BelongsTo<Church, $this>
     */
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class)->where('is_active', true);
    }

    /**
     * Escopo para filtrar apenas notícias publicadas e com data de publicação vencida.
     *
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * URL da imagem de capa no storage de mídia.
     *
     * @return Attribute<string|null, never>
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::url($this->featured_image));
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
     * Nome de quem publicou: a igreja emissora ou a CBN Goiás.
     */
    public function publisherName(): string
    {
        return $this->church?->name ?? self::STATE_PUBLISHER;
    }

    /**
     * Município da igreja emissora, quando houver.
     */
    public function publisherCity(): ?string
    {
        return $this->church?->city;
    }
}
