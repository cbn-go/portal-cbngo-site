@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<section class="relative bg-gradient-to-b from-cbn-navy to-cbn-navy-dark text-white py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto text-center space-y-6">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-cbn-gold/10 text-cbn-gold-light border border-cbn-gold/30">
            Fase 1: Setup Inicial Concluído
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white font-brand-serif">
            Convenção Batista Nacional do Estado de Goiás
        </h1>
        <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto font-brand-sans">
            Portal institucional oficial da CBN-GO. Comunhão, proclamação do evangelho e suporte integral às igrejas batistas nacionais em território goiano.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <a href="#sobre" class="px-6 py-3 rounded-lg font-semibold bg-cbn-gold text-cbn-navy-dark hover:bg-cbn-gold-light transition shadow-lg shadow-cbn-gold/20">
                Conheça a CBN-GO
            </a>
            <a href="#igrejas" class="px-6 py-3 rounded-lg font-semibold bg-slate-800 text-white hover:bg-slate-700 border border-slate-700 transition">
                Encontre uma Igreja
            </a>
        </div>
    </div>
</section>

<section id="sobre" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-cbn-navy/10 text-cbn-navy flex items-center justify-center font-bold mb-4 font-brand-serif text-lg">
                01
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2 font-brand-serif">Estrutura MVC & PHP 8.2+</h3>
            <p class="text-slate-600 text-sm font-brand-sans">
                Arquitetura limpa em Laravel 11 com rotas declarativas e testes automatizados em TDD.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-cbn-gold/20 text-cbn-gold flex items-center justify-center font-bold mb-4 font-brand-serif text-lg">
                02
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2 font-brand-serif">Tailwind CSS & Vite</h3>
            <p class="text-slate-600 text-sm font-brand-sans">
                Pipeline de compilação ultra-rápido com tokens oficiais de design e suporte a responsividade.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold mb-4 font-brand-serif text-lg">
                03
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2 font-brand-serif">Integração Contínua (CI)</h3>
            <p class="text-slate-600 text-sm font-brand-sans">
                GitHub Actions executando verificação de sintaxe, linting, análise estática e suite de testes a cada PR.
            </p>
        </div>
    </div>
</section>
@endsection
