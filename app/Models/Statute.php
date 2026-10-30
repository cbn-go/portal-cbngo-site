<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Versão do Estatuto da CBN-GO, gerida no painel administrativo (tabela `statutes` do banco compartilhado).
 * O XML Akoma Ntoso 3.0 (`akn_xml`) é a fonte da verdade; o site só o lê e renderiza.
 *
 * @property int $id
 * @property string $title
 * @property string $version_label
 * @property Carbon $approved_at
 * @property string $approved_place
 * @property string $approval_body
 * @property string|null $minutes_reference
 * @property string|null $registry_info
 * @property Carbon $effective_at
 * @property string $status
 * @property string $akn_xml
 * @property string|null $original_pdf
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<Statute> published()
 * @method static Builder<Statute> history()
 */
class Statute extends Model
{
    use HasFactory;

    /**
     * Status do painel que torna a versão vigente no site.
     */
    public const STATUS_PUBLISHED = 'published';

    /**
     * Status das versões anteriores, mantidas como histórico.
     */
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'version_label',
        'approved_at',
        'approved_place',
        'approval_body',
        'minutes_reference',
        'registry_info',
        'effective_at',
        'status',
        'akn_xml',
        'original_pdf',
        'published_at',
    ];

    /**
     * Os atributos convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'date',
        'effective_at' => 'date',
        'published_at' => 'datetime',
    ];

    /**
     * Versão publicada e já em vigor (o painel garante uma só publicada).
     *
     * @param  Builder<Statute>  $query
     * @return Builder<Statute>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->whereDate('effective_at', '<=', today())
            ->latest('id');
    }

    /**
     * Versões visíveis no histórico público: a publicada e as arquivadas, da mais recente para a mais antiga.
     *
     * @param  Builder<Statute>  $query
     * @return Builder<Statute>
     */
    public function scopeHistory(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PUBLISHED, self::STATUS_ARCHIVED])
            ->orderByDesc('approved_at')
            ->orderByDesc('id');
    }

    /**
     * Versão vigente (publicada).
     */
    public function isCurrent(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Segmento da URL da versão (data de aprovação, Y-m-d).
     */
    public function versionKey(): string
    {
        return $this->approved_at->format('Y-m-d');
    }
}
