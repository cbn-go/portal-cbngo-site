<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Seo
{
    /**
     * Tamanho máximo da meta description recomendado pelos buscadores.
     */
    public const DESCRIPTION_LIMIT = 160;

    /**
     * Monta o título da página no formato "{título} — CBN Goiás", ou o título da marca quando vazio.
     */
    public static function title(?string $title): string
    {
        $title = trim((string) $title);
        $brand = (string) config('cbngo.organization.short_name');

        if ($title === '') {
            return $brand.' — '.config('cbngo.organization.name');
        }

        return $title.' — '.$brand;
    }

    /**
     * Normaliza a descrição: remove HTML, colapsa espaços e limita a 160 caracteres.
     */
    public static function description(?string $description): string
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $description)));

        if ($text === '') {
            $text = (string) config('cbngo.seo.default_description');
        }

        return Str::limit($text, self::DESCRIPTION_LIMIT - 3, '...');
    }

    /**
     * Retorna a URL absoluta da imagem de compartilhamento, caindo na imagem padrão para valores vazios ou inseguros.
     */
    public static function image(?string $image): string
    {
        $image = trim((string) $image);

        if (preg_match('/^https?:\/\//i', $image)) {
            return $image;
        }

        if (str_starts_with($image, '/') && ! str_starts_with($image, '//')) {
            return url($image);
        }

        return asset((string) config('cbngo.seo.default_image'));
    }

    /**
     * Gera a URL canônica: a URL atual sem query string, mantendo apenas ?page=N quando N > 1.
     */
    public static function canonical(Request $request): string
    {
        $page = $request->integer('page', 1);

        return $request->url().($page > 1 ? '?page='.$page : '');
    }
}
