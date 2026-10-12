<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $role_title
 * @property string|null $avatar_url
 * @property string|null $bio
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Article> $articles
 */
class Author extends Model
{
    use HasFactory;

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'role_title',
        'avatar_url',
        'bio',
    ];

    /**
     * Artigos publicados pelo articulista.
     *
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
