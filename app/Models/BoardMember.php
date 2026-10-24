<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\BoardMemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string $group
 * @property string|null $region
 * @property string|null $photo
 * @property string|null $term_label
 * @property int $position
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BoardMember extends Model
{
    /** @use HasFactory<BoardMemberFactory> */
    use HasFactory;

    /**
     * Órgãos da convenção na ordem institucional de exibição em /diretoria.
     *
     * @var array<string, string>
     */
    public const GROUPS = [
        'presidencia' => 'Presidência',
        'vice_presidencia' => 'Vice-presidências',
        'secretaria' => 'Secretaria',
        'tesouraria' => 'Tesouraria',
        'conselho_fiscal' => 'Conselho Fiscal',
        'coordenacao_regional' => 'Coordenadores de Regiões',
    ];

    /**
     * Títulos e preposições ignorados no cálculo das iniciais.
     */
    private const IGNORED_INITIAL_WORDS = ['pr', 'pra', 'dr', 'dra', 'diác', 'diac', 'presb', 'da', 'de', 'do', 'das', 'dos', 'e'];

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'group',
        'region',
        'photo',
        'term_label',
        'position',
        'is_active',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Escopo para membros ativos da diretoria atual.
     *
     * @param  Builder<BoardMember>  $query
     * @return Builder<BoardMember>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Retorna até duas iniciais do nome, ignorando títulos eclesiásticos e preposições.
     */
    public function getInitials(): string
    {
        $words = preg_split('/\s+/u', trim($this->name)) ?: [];

        $relevant = array_values(array_filter(
            $words,
            fn (string $word): bool => $word !== ''
                && ! in_array(mb_strtolower(rtrim($word, '.')), self::IGNORED_INITIAL_WORDS, true)
        ));

        if ($relevant === []) {
            return '';
        }

        $initials = mb_substr($relevant[0], 0, 1);

        if (count($relevant) > 1) {
            $initials .= mb_substr($relevant[count($relevant) - 1], 0, 1);
        }

        return mb_strtoupper($initials);
    }

    /**
     * Retorna a URL da foto no storage de mídia, ou null se ausente ou insegura (proteção XSS).
     */
    public function getSafePhotoUrl(): ?string
    {
        return Media::url($this->photo);
    }
}
