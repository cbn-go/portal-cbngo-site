<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $query = News::published()->with('church');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($cidade !== '') {
            $query->whereHas('church', fn ($church) => $church->where('city', $cidade));
        }

        if ($igreja !== '') {
            $query->whereHas('church', fn ($church) => $church->where('slug', $igreja));
        }

        if ($apenasOficiais) {
            $query->where('is_official', true);
        }

        $news = $query->latest('published_at')->paginate(9)->withQueryString();

        $publishingChurches = Church::active()
            ->whereIn('id', News::published()->whereNotNull('church_id')->select('church_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'city']);

        $availableCities = $publishingChurches
            ->pluck('city')
            ->filter(fn (?string $city): bool => trim((string) $city) !== '')
            ->unique()
            ->sort()
            ->values();

        /** @var Collection<string, string> $availableChurches */
        $availableChurches = $publishingChurches->pluck('name', 'slug');

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
        $news = News::published()->with('church')->where('slug', $slug)->firstOrFail();

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
