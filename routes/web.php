<?php

use App\Http\Controllers\HomeController;
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

Route::get('/noticias', function () {
    return view('placeholder', ['title' => 'Notícias e Comunicados da CBN-GO']);
})->name('noticias.index');

Route::get('/noticias/{slug}', function (string $slug) {
    return view('placeholder', ['title' => 'Notícia Oficial', 'slug' => $slug]);
})->name('noticias.show');

Route::get('/artigos', function () {
    return view('placeholder', ['title' => 'Artigos e Reflexões Pastorais']);
})->name('artigos.index');

Route::get('/artigos/{slug}', function (string $slug) {
    return view('placeholder', ['title' => 'Artigo Pastoral', 'slug' => $slug]);
})->name('artigos.show');
