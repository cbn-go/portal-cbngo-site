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
 * @property array<string, string>|null $social_links
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
        'social_links',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'social_links' => 'array',
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

    /**
     * Retorna os canais sociais sanitizados com verificação estrita de esquema (https, http, mailto).
     *
     * @return array<string, string>
     */
    public function getSafeSocialLinks(): array
    {
        if (! is_array($this->social_links)) {
            return [];
        }

        $safe = [];
        foreach ($this->social_links as $network => $link) {
            $trimmed = trim((string) $link);

            if (str_starts_with($trimmed, 'mailto:')) {
                $safe[(string) $network] = $trimmed;

                continue;
            }

            if (str_contains($trimmed, '@') && ! str_starts_with($trimmed, 'http://') && ! str_starts_with($trimmed, 'https://')) {
                $safe[(string) $network] = 'mailto:'.$trimmed;

                continue;
            }

            if (preg_match('/^https?:\/\//i', $trimmed)) {
                $safe[(string) $network] = $trimmed;
            }
        }

        return $safe;
    }
}
