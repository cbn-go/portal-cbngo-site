<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $category
 * @property string $excerpt
 * @property string $content
 * @property string|null $image_url
 * @property string|null $church_name
 * @property string|null $city
 * @property bool $is_official
 * @property bool $is_published
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class News extends Model
{
    use HasFactory;

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
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'image_url',
        'church_name',
        'city',
        'is_official',
        'is_published',
        'published_at',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_official' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Escopo para filtrar apenas notícias publicadas e com data de publicação válida.
     *
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
