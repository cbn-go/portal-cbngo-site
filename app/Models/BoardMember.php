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
 * @property string|null $title
 * @property string $name
 * @property string $role
 * @property string $group
 * @property string|null $region
 * @property string|null $photo
 * @property string|null $term_label
 * @property Carbon|null $term_starts_on
 * @property Carbon|null $term_ends_on
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
        'vice_presidencia' => 'Vice-presidência',
        'secretaria' => 'Secretaria',
        'tesouraria' => 'Tesouraria',
        'conplex' => 'Conselho de Planejamento e Execução (CONPLEX)',
        'conselho_fiscal' => 'Conselho Fiscal',
        'coordenacao_regional' => 'Diretorias Regionais (AMIGO)',
    ];

    /**
     * Regiões das Associações Missionárias Goianas (AMIGO), na ordem de exibição das diretorias regionais.
     *
     * @var array<string, string>
     */
    public const REGIONS = [
        'leste' => 'AMIGO Leste',
        'oeste' => 'AMIGO Oeste',
        'norte' => 'AMIGO Norte',
        'sudoeste' => 'AMIGO Sudoeste',
    ];

    /**
     * Títulos e preposições ignorados no cálculo das iniciais (nomes antigos ainda podem trazer o título no `name`).
     */
    private const IGNORED_INITIAL_WORDS = ['pr', 'pra', 'ev', 'miss', 'dr', 'dra', 'diác', 'diac', 'presb', 'da', 'de', 'do', 'das', 'dos', 'e'];

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'name',
        'role',
        'group',
        'region',
        'photo',
        'term_label',
        'term_starts_on',
        'term_ends_on',
        'position',
        'is_active',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'term_starts_on' => 'date',
        'term_ends_on' => 'date',
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
     * Nome exibido com o título eclesiástico antes (ex.: "Pr. André Luiz"); sem título, o membro é leigo.
     */
    public function getDisplayName(): string
    {
        return trim(trim((string) $this->title).' '.trim($this->name));
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
