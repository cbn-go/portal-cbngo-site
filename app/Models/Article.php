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
 * Artigo gerido no painel administrativo (tabela `articles` do banco compartilhado).
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $category
 * @property string|null $excerpt
 * @property string $content
 * @property int $reading_time
 * @property string|null $cover_image
 * @property string $status
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $cover_image_url
 * @property-read Author|null $author
 *
 * @method static Builder<Article> published()
 */
class Article extends Model
{
    use HasFactory;

    /**
     * Status do painel que torna o artigo público (com published_at vencido).
     */
    public const STATUS_PUBLISHED = 'published';

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'reading_time',
        'cover_image',
        'status',
        'published_at',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reading_time' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Perfil público do articulista: o autor é o usuário, e o perfil fica em author_profiles.
     * Perfis de usuários desativados no painel não são expostos (o artigo fica atribuído à CBN Goiás).
     *
     * @return BelongsTo<Author, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'user_id', 'user_id')
            ->whereHas('user', fn (Builder $user) => $user->where('is_active', true));
    }

    /**
     * Escopo para filtrar apenas artigos publicados e com data de publicação vencida.
     *
     * @param  Builder<Article>  $query
     * @return Builder<Article>
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
    protected function coverImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::url($this->cover_image));
    }
}
