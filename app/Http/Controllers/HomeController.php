<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\News;
use App\Models\UrgentNotice;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial do portal institucional com dados dinâmicos do banco.
     */
    public function index(): View
    {
        $urgentNotice = UrgentNotice::active()
            ->orderByDesc('starts_at')
            ->latest('id')
            ->first();

        $latestNews = News::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $latestArticles = Article::with('author')
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('home', compact('urgentNotice', 'latestNews', 'latestArticles'));
    }
}
