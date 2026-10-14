<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Exibe o feed público de artigos e reflexões bíblicas da CBN Goiás.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $autor = trim((string) $request->query('autor', ''));
        $categoria = trim((string) $request->query('categoria', ''));

        $query = Article::published()->with('author');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($autor !== '') {
            $query->whereHas('author', function ($sub) use ($autor) {
                $sub->where(function ($q) use ($autor) {
                    $q->where('slug', $autor)
                        ->orWhere('id', $autor);
                });
            });
        }

        if ($categoria !== '') {
            $query->where('category', $categoria);
        }

        $articles = $query->latest('published_at')->paginate(9)->withQueryString();

        $availableCategories = Article::published()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $availableAuthors = Author::whereHas('articles', function ($sub) {
            $sub->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        })->orderBy('name')->get(['id', 'name', 'slug']);

        return view('artigos.index', [
            'articles' => $articles,
            'availableCategories' => $availableCategories,
            'availableAuthors' => $availableAuthors,
            'q' => $q,
            'autor' => $autor,
            'categoria' => $categoria,
        ]);
    }

    /**
     * Exibe a página detalhada para leitura do artigo.
     */
    public function show(string $slug): View
    {
        $article = Article::published()->with('author')->where('slug', $slug)->firstOrFail();

        $relatedArticles = Article::published()
            ->with('author')
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('artigos.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
