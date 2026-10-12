@extends('layouts.app')

@section('title', 'Design System Oficial')

@section('banner')
    <x-urgent-banner
        message="Ambiente de Vitrine do Design System Oficial CBN-GO (Acessibilidade WCAG 2.1 AA)"
        link="#componentes"
        linkText="Explorar Componentes"
    />
@endsection

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-16">
    <!-- Cabeçalho da Vitrine -->
    <header class="border-b border-slate-200 pb-8 space-y-3">
        <div class="flex items-center gap-2">
            <x-badge variant="gold" :dot="true">Fase 2: Concluída</x-badge>
            <x-badge variant="navy">WCAG 2.1 AA</x-badge>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-cbn-navy font-brand-serif">
            Design System Oficial CBN-GO
        </h1>
        <p class="text-base text-slate-600 max-w-3xl font-brand-sans">
            Guia de estilos e componentes Blade padronizados para o novo portal público da Convenção Batista Nacional do Estado de Goiás.
        </p>
    </header>

    <!-- Seção 1: Design Tokens & Cores (Manual da Marca CBN) -->
    <section class="space-y-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-brand-serif border-l-4 border-cbn-red pl-3">
                Tokens de Cores — Manual da Marca CBN
            </h2>
            <p class="text-sm text-slate-600 mt-2 font-brand-sans">
                Portal como peça institucional: protagonismo da paleta <strong>3.2</strong>
                (<code class="text-xs">#F43517</code>, <code class="text-xs">#F36529</code>, <code class="text-xs">#EFA162</code>, <code class="text-xs">#F1D6A9</code>).
                Preto/vermelho da <strong>3.1</strong> só para marca e texto.
            </p>
        </div>

        <div class="space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">3.1 Marca</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-cbn-red text-white space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Red</span>
                    <span class="block font-mono text-xs">#E00209</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-black text-cbn-gold-light space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Black</span>
                    <span class="block font-mono text-xs">#1E120D</span>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">3.2 Institucionais (base do portal — tokens *-navy* e *-gold*)</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-cbn-navy text-white space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Orange / Navy</span>
                    <span class="block font-mono text-xs">#F43517</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-navy-light text-white space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Orange Mid</span>
                    <span class="block font-mono text-xs">#F36529</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-gold text-cbn-black space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Gold</span>
                    <span class="block font-mono text-xs">#EFA162</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-gold-light text-cbn-black space-y-1 shadow-sm border border-slate-200">
                    <span class="block text-xs uppercase tracking-wider font-bold">Gold Light</span>
                    <span class="block font-mono text-xs">#F1D6A9</span>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">3.3 Informativos (secundário)</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-cbn-info-dark text-white space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Info Dark</span>
                    <span class="block font-mono text-xs">#001D4D</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-info text-white space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Info</span>
                    <span class="block font-mono text-xs">#026A8E</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-teal text-white space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Teal</span>
                    <span class="block font-mono text-xs">#038794</span>
                </div>
                <div class="p-4 rounded-xl bg-cbn-teal-light text-cbn-navy space-y-1 shadow-sm">
                    <span class="block text-xs uppercase tracking-wider font-bold">Teal Light</span>
                    <span class="block font-mono text-xs">#69A195</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção 2: Componentes -->
    <section id="componentes" class="space-y-12">
        <h2 class="text-2xl font-bold text-slate-900 font-brand-serif border-l-4 border-cbn-navy pl-3">
            Componentes da Biblioteca
        </h2>

        <!-- Botões -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 space-y-6">
            <h3 class="text-lg font-bold text-slate-900 font-brand-serif">Botões (&lt;x-button&gt;)</h3>
            <div class="flex flex-wrap items-center gap-4">
                <x-button variant="primary" size="sm">Primário SM</x-button>
                <x-button variant="primary" size="md">Primário MD</x-button>
                <x-button variant="primary" size="lg">Primário LG</x-button>
                <x-button variant="gold">Dourado Oficial</x-button>
                <x-button variant="secondary">Secundário</x-button>
                <x-button variant="outline">Outline</x-button>
                <x-button variant="ghost">Ghost</x-button>
                <x-button href="https://example.com" variant="gold" target="_blank">Como Link &lt;a&gt;</x-button>
            </div>
        </div>

        <!-- Badges -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 space-y-6">
            <h3 class="text-lg font-bold text-slate-900 font-brand-serif">Badges (&lt;x-badge&gt;)</h3>
            <div class="flex flex-wrap items-center gap-3">
                <x-badge variant="navy">Institucional</x-badge>
                <x-badge variant="gold" :dot="true">Destaque</x-badge>
                <x-badge variant="emerald" :dot="true">Ativo</x-badge>
                <x-badge variant="amber" :dot="true">Pendente</x-badge>
                <x-badge variant="rose" :dot="true">Urgente</x-badge>
                <x-badge variant="slate">Geral</x-badge>
            </div>
        </div>

        <!-- Cards de Notícia -->
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-900 font-brand-serif">Cards de Notícia (&lt;x-news-card&gt;)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-news-card
                    title="Assembleia Geral Ordinária reúne mais de 120 pastores em Goiânia"
                    slug="assembleia-geral-ordinaria-2026"
                    category="Convenção"
                    date="09/10/2026"
                    excerpt="Convenção estadual delibera sobre novos projetos missionários e investimentos no seminário teológico."
                />
                <x-news-card
                    title="Juventude Batista Nacional realiza congresso estadual com recorde de inscritos"
                    slug="congresso-jubang-2026"
                    category="JUBANG"
                    date="05/10/2026"
                    excerpt="Mais de mil jovens de diversas cidades goianas participaram do evento focado em missões e liderança."
                />
                <x-news-card
                    title="Nota Oficial da Diretoria da CBN-GO sobre o Projeto Missionário Estadual"
                    slug="nota-oficial-diretoria"
                    category="Comunicado"
                    date="01/10/2026"
                    excerpt="Diretoria executiva publica documento oficial com diretrizes para plantação de novas igrejas."
                />
            </div>
        </div>

        <!-- Cards de Artigo -->
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-900 font-brand-serif">Cards de Artigo (&lt;x-article-card&gt;)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-article-card
                    title="A Centralidade das Escrituras na Vida Pastoral Contemporânea"
                    slug="a-centralidade-das-escrituras"
                    authorName="Pr. Silas de Oliveira"
                    readingTime="6"
                    publishedAt="08/10/2026"
                    excerpt="Uma reflexão bíblica sobre o compromisso com a sã doutrina em tempos de relativismo e desafios ministeriais."
                />
                <x-article-card
                    title="O Papel da Família Cristã na Sociedade Pós-Moderna"
                    slug="o-papel-da-familia-crista"
                    authorName="Dra. Miriam Santos"
                    readingTime="4"
                    publishedAt="02/10/2026"
                    excerpt="Princípios cristãos perenes para o fortalecimento do lar e edificação espiritual das próximas gerações."
                />
            </div>
        </div>

        <!-- Paginação -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 space-y-6">
            <h3 class="text-lg font-bold text-slate-900 font-brand-serif">Paginação (&lt;x-pagination&gt;)</h3>
            <x-pagination :currentPage="2" :totalPages="5" prevUrl="?page=1" nextUrl="?page=3" />
        </div>
    </section>
</div>
@endsection
