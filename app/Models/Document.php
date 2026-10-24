<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $category
 * @property string|null $file
 * @property string|null $external_url
 * @property string $file_format
 * @property int $position
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    /**
     * Os atributos atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'file',
        'external_url',
        'file_format',
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
     * Escopo para documentos oficiais publicados.
     *
     * @param  Builder<Document>  $query
     * @return Builder<Document>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Retorna a URL do documento: o arquivo no storage de mídia ou, sem arquivo, o link externo
     * (apenas http/https ou caminho relativo local — proteção XSS).
     */
    public function getSafeFileUrl(): ?string
    {
        if (trim((string) $this->file) !== '') {
            return Media::url($this->file);
        }

        $trimmed = trim((string) $this->external_url);

        if (preg_match('/^https?:\/\//i', $trimmed)) {
            return $trimmed;
        }

        if (str_starts_with($trimmed, '/') && ! str_starts_with($trimmed, '//')) {
            return $trimmed;
        }

        return null;
    }
}
