<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Exibe o feed geral público de notícias e comunicados da CBN Goiás.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $cidade = trim((string) $request->query('cidade', ''));
        $igreja = trim((string) $request->query('igreja', ''));
        $apenasOficiais = $request->boolean('apenas_oficiais');

        $query = News::published();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($cidade !== '') {
            $query->where('city', $cidade);
        }

        if ($igreja !== '') {
            $query->where('church_name', $igreja);
        }

        if ($apenasOficiais) {
            $query->where('is_official', true);
        }

        $news = $query->latest('published_at')->paginate(9)->withQueryString();

        $availableCities = News::published()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $availableChurches = News::published()
            ->whereNotNull('church_name')
            ->where('church_name', '!=', '')
            ->distinct()
            ->orderBy('church_name')
            ->pluck('church_name');

        return view('noticias.index', [
            'news' => $news,
            'availableCities' => $availableCities,
            'availableChurches' => $availableChurches,
            'q' => $q,
            'cidade' => $cidade,
            'igreja' => $igreja,
            'apenasOficiais' => $apenasOficiais,
        ]);
    }

    /**
     * Exibe a página detalhada da notícia com galeria de fotos e compartilhamento social.
     */
    public function show(string $slug): View
    {
        $news = News::published()->where('slug', $slug)->firstOrFail();

        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('noticias.show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
        ]);
    }
}
