<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');

Route::get('/design-system', function () {
    return view('design-system.index');
})->name('design-system');

// Rotas Placeholder (WDLC Fase 3 -> Resolução de 404 até implementação das issues dedicadas #4, #5, #6, #7)
Route::get('/quem-somos', function () {
    return view('placeholder', ['title' => 'Quem Somos — Convenção Batista Nacional de Goiás']);
})->name('quem-somos');

Route::get('/diretoria', function () {
    return view('placeholder', ['title' => 'Diretoria Executiva da CBN-GO']);
})->name('diretoria');

Route::get('/contato', function () {
    return view('placeholder', ['title' => 'Contato Institucional CBN-GO']);
})->name('contato');

Route::get('/igrejas', function () {
    return view('placeholder', ['title' => 'Guia e Diretório de Igrejas Batistas Nacionais em Goiás']);
})->name('igrejas');

Route::get('/noticias', [NewsController::class, 'index'])->name('noticias.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('noticias.show');

Route::get('/artigos', [ArticleController::class, 'index'])->name('artigos.index');
Route::get('/artigos/{slug}', [ArticleController::class, 'show'])->name('artigos.show');
Route::get('/autores/{author}', [AuthorController::class, 'show'])->name('autores.show');
