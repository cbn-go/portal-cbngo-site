<?php

namespace App\Support;

class Media
{
    /**
     * Tamanhos de variantes WebP gerados pelo painel administrativo.
     *
     * @var array<int, string>
     */
    public const SIZES = ['thumb', 'card', 'cover', 'full'];

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

    /**
     * Retorna a URL da variante WebP para o tamanho especificado (thumb, card, cover, full).
     *
     * Regra do painel: para caminho relativo "news/featured/abc.jpg", a variante é "news/featured/abc-{size}.webp".
     * Documentos (PDF, DOC), URLs externas completas, caminhos inseguros ou tamanhos inválidos retornam null.
     */
    public static function variant(?string $path, string $size): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || ! in_array($size, self::SIZES, true)) {
            return null;
        }

        if (str_starts_with($path, '//')) {
            return null;
        }

        // Se for URL absoluta, só aceita se for do próprio storage de mídia (MEDIA_URL)
        if (preg_match('/^[a-z][a-z0-9+.\-]*:/i', $path)) {
            $mediaBaseUrl = rtrim((string) config('media.url'), '/');
            if ($mediaBaseUrl !== '' && str_starts_with($path, $mediaBaseUrl.'/')) {
                $path = substr($path, strlen($mediaBaseUrl) + 1);
            } else {
                return null;
            }
        }

        // Verifica se é uma extensão de imagem suportada
        if (! preg_match('/\.(jpe?g|png|webp|gif|bmp|tiff)$/i', $path)) {
            return null;
        }

        $webpPath = preg_replace('/\.[a-zA-Z0-9]+$/', "-{$size}.webp", $path);

        return self::url($webpPath);
    }

    /**
     * Monta a string do atributo srcset contendo as variantes WebP disponíveis com seus descritores de largura.
     *
     * @param  array<string, string>  $sizesMap  Tamanho => descritor (ex: ['thumb' => '400w', 'card' => '800w', 'cover' => '1600w'])
     *
     * A variante "full" (2400px) atende telas de alta densidade na capa das notícias.
     */
    public static function srcset(?string $path, array $sizesMap = ['thumb' => '400w', 'card' => '800w', 'cover' => '1600w', 'full' => '2400w']): ?string
    {
        $sources = [];

        foreach ($sizesMap as $size => $descriptor) {
            $variantUrl = self::variant($path, (string) $size);
            if ($variantUrl !== null) {
                $sources[] = "{$variantUrl} {$descriptor}";
            }
        }

        return count($sources) > 0 ? implode(', ', $sources) : null;
    }
}
