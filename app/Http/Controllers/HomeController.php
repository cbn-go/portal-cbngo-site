<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Banner;
use App\Models\News;
use App\Models\UrgentNotice;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial do portal institucional com dados dinâmicos do banco.
     */
    public function index(): View
    {
        $urgentNotice = UrgentNotice::active()
            ->byPriority()
            ->first();

        $banners = $this->banners();

        $latestNews = News::published()
            ->with('church')
            ->latest('published_at')
            ->take(3)
            ->get();

        $latestArticles = Article::with('author')
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('home', compact('urgentNotice', 'banners', 'latestNews', 'latestArticles'));
    }

    /**
     * Banners vigentes do hero, em cache curto versionado pelo último updated_at e pela contagem da tabela.
     *
     * @return Collection<int, Banner>
     */
    private function banners(): Collection
    {
        $version = Banner::query()
            ->toBase()
            ->selectRaw('max(updated_at) as last_updated, count(*) as total')
            ->first();

        $key = 'home.banners.'.md5($version?->last_updated.'|'.$version?->total);

        return Cache::remember($key, (int) config('cbngo.home.banners_cache_ttl'), fn () => Banner::visible()->get());
    }
}
