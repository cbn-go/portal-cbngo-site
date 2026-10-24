<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Perfil público do articulista (tabela `author_profiles` + `users` do banco compartilhado).
 *
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string|null $pastoral_title
 * @property string|null $bio
 * @property string|null $avatar
 * @property array<string, string>|null $social_links
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $name
 * @property-read string $role_title
 * @property-read string|null $avatar_url
 * @property-read User|null $user
 * @property-read Collection<int, Article> $articles
 *
 * @method static Builder<Author> active()
 */
class Author extends Model
{
    use HasFactory;

    /**
     * Título exibido quando o painel não informa o título pastoral.
     */
    public const DEFAULT_ROLE_TITLE = 'Articulista CBN-GO';

    /**
     * Tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'author_profiles';

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'slug',
        'pastoral_title',
        'bio',
        'avatar',
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
     * Relacionamentos carregados por padrão: o nome de exibição vem de users.name.
     *
     * @var array<int, string>
     */
    protected $with = ['user'];

    /**
     * Usuário do painel dono do perfil.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Artigos escritos pelo usuário dono do perfil.
     *
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'user_id', 'user_id');
    }

    /**
     * Escopo para perfis de usuários ativos no painel.
     *
     * @param  Builder<Author>  $query
     * @return Builder<Author>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('user', fn (Builder $user) => $user->where('is_active', true));
    }

    /**
     * Nome de exibição do articulista (users.name).
     *
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::get(fn (): string => (string) $this->user?->name);
    }

    /**
     * Título pastoral, com fallback institucional.
     *
     * @return Attribute<string, never>
     */
    protected function roleTitle(): Attribute
    {
        return Attribute::get(fn (): string => trim((string) $this->pastoral_title) ?: self::DEFAULT_ROLE_TITLE);
    }

    /**
     * URL do avatar no storage de mídia.
     *
     * @return Attribute<string|null, never>
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => Media::url($this->avatar));
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
                $email = trim(substr($trimmed, 7));
                if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    $safe[(string) $network] = 'mailto:'.$email;
                }

                continue;
            }

            if (str_contains($trimmed, '@') && ! str_starts_with($trimmed, 'http://') && ! str_starts_with($trimmed, 'https://')) {
                if (filter_var($trimmed, FILTER_VALIDATE_EMAIL) !== false) {
                    $safe[(string) $network] = 'mailto:'.$trimmed;
                }

                continue;
            }

            if (preg_match('/^https?:\/\//i', $trimmed)) {
                $safe[(string) $network] = $trimmed;
            }
        }

        return $safe;
    }
}
