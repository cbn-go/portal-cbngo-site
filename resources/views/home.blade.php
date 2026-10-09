@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<section class="relative bg-gradient-to-b from-cbn-navy to-cbn-navy-dark text-white py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto text-center space-y-6">
        <div class="flex items-center justify-center gap-2">
            <x-badge variant="gold" :dot="true">
                Fase 2: Design System Integrado
            </x-badge>
            <a href="{{ route('design-system') }}" class="inline-flex items-center text-xs font-semibold text-cbn-gold-light hover:underline">
                Ver Vitrine &rarr;
            </a>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white font-brand-serif">
            Convenção Batista Nacional do Estado de Goiás
        </h1>
        <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto font-brand-sans">
            Portal institucional oficial da CBN-GO. Comunhão, proclamação do evangelho e suporte integral às igrejas batistas nacionais em território goiano.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <x-button variant="gold" size="lg" href="#sobre">
                Conheça a CBN-GO
            </x-button>
            <x-button variant="secondary" size="lg" href="#igrejas">
                Encontre uma Igreja
            </x-button>
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
            <h3 class="text-lg font-bold text-slate-900 mb-2 font-brand-serif">Design System Oficial</h3>
            <p class="text-slate-600 text-sm font-brand-sans">
                Biblioteca de componentes Blade com tokens oficiais de design e suporte a WCAG 2.1 AA.
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
