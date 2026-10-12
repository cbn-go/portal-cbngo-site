<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $content
 * @property int $reading_time
 * @property bool $is_published
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Author $author
 */
class Article extends Model
{
    use HasFactory;

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'reading_time',
        'is_published',
        'published_at',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reading_time' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Articulista responsável pelo artigo.
     *
     * @return BelongsTo<Author, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Escopo para filtrar apenas artigos publicados e com data de publicação válida.
     *
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
