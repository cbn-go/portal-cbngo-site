<?php

namespace App\Support;

class Media
{
    /**
     * Monta a URL pública de um arquivo do storage de mídia compartilhado com o painel.
     *
     * O painel salva caminhos relativos (ex.: news/featured/abc.jpg); a URL final é MEDIA_URL/{caminho}.
     * URLs http(s) absolutas são mantidas. Valores vazios, protocolo relativo (//) ou outros esquemas
     * (javascript:, data:) retornam null (proteção XSS).
     */
    public static function url(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || str_starts_with($path, '//')) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (preg_match('/^[a-z][a-z0-9+.\-]*:/i', $path)) {
            return null;
        }

        return rtrim((string) config('media.url'), '/').'/'.ltrim($path, '/');
    }

    /**
     * Monta as URLs de uma lista de caminhos, descartando os inválidos.
     *
     * @param  array<int, mixed>|null  $paths
     * @return array<int, string>
     */
    public static function urls(?array $paths): array
    {
        return array_values(array_filter(
            array_map(fn (mixed $path): ?string => is_string($path) ? self::url($path) : null, $paths ?? []),
        ));
    }
}
