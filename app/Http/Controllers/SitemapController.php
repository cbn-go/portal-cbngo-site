<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Author;
use App\Models\News;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Rotas públicas estáticas incluídas no sitemap.
     *
     * @var array<int, string>
     */
    private const STATIC_ROUTES = [
        'home',
        'quem-somos',
        'diretoria',
        'contato',
        'igrejas',
        'noticias.index',
        'artigos.index',
    ];

    /**
     * Gera o sitemap.xml com as rotas estáticas e os conteúdos publicados.
     *
     * O conteúdo é publicado pelo painel, que o portal não observa; por isso o cache tem TTL curto
     * (config cbngo.sitemap.cache_ttl, 10 minutos por padrão) em vez de invalidação por evento.
     */
    public function index(): Response
    {
        $entries = Cache::remember('sitemap.entries', (int) config('cbngo.sitemap.cache_ttl'), fn () => $this->entries());

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Gera o robots.txt apontando para o sitemap com URL absoluta.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /design-system',
            'Disallow: /health',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * Monta as entradas do sitemap.
     *
     * @return array<int, array{loc: string, lastmod: string|null}>
     */
    private function entries(): array
    {
        $entries = array_map(fn (string $route) => ['loc' => route($route), 'lastmod' => null], self::STATIC_ROUTES);

        News::published()->latest('published_at')->get(['slug', 'updated_at'])
            ->each(function (News $news) use (&$entries) {
                $entries[] = ['loc' => route('noticias.show', $news->slug), 'lastmod' => $news->updated_at?->toAtomString()];
            });

        Article::published()->latest('published_at')->get(['slug', 'updated_at'])
            ->each(function (Article $article) use (&$entries) {
                $entries[] = ['loc' => route('artigos.show', $article->slug), 'lastmod' => $article->updated_at?->toAtomString()];
            });

        Author::active()->whereIn('user_id', Article::published()->select('user_id'))->orderBy('slug')->get(['id', 'user_id', 'slug', 'updated_at'])
            ->each(function (Author $author) use (&$entries) {
                $entries[] = ['loc' => route('autores.show', $author->slug), 'lastmod' => $author->updated_at?->toAtomString()];
            });

        return $entries;
    }
}
