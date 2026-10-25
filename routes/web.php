<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstitutionalController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SocialProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');

Route::get('/design-system', function () {
    return view('design-system.index');
})->name('design-system');

Route::get('/quem-somos', [InstitutionalController::class, 'about'])->name('quem-somos');
Route::get('/diretoria', [InstitutionalController::class, 'board'])->name('diretoria');
Route::get('/contato', [InstitutionalController::class, 'contact'])->name('contato');

Route::get('/igrejas', [ChurchController::class, 'index'])->name('igrejas');

Route::get('/noticias', [NewsController::class, 'index'])->name('noticias.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('noticias.show');

Route::get('/projetos-sociais', [SocialProjectController::class, 'index'])->name('projetos-sociais.index');
Route::get('/projetos-sociais/{slug}', [SocialProjectController::class, 'show'])->name('projetos-sociais.show');

Route::get('/artigos', [ArticleController::class, 'index'])->name('artigos.index');
Route::get('/artigos/{slug}', [ArticleController::class, 'show'])->name('artigos.show');
Route::get('/autores/{author}', [AuthorController::class, 'show'])->name('autores.show');
