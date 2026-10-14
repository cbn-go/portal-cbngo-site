<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * Exibe o perfil público do articulista credenciado com sua biografia e artigos publicados.
     */
    public function show(string $author): View
    {
        $authorModel = Author::query()
            ->where(function ($query) use ($author) {
                $query->where('slug', $author)
                    ->orWhere('id', $author);
            })
            ->firstOrFail();

        $articles = $authorModel->articles()
            ->published()
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $totalArticles = $authorModel->articles()
            ->published()
            ->count();

        return view('autores.show', [
            'author' => $authorModel,
            'articles' => $articles,
            'totalArticles' => $totalArticles,
        ]);
    }
}
