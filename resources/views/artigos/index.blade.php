@extends('layouts.app')

@section('title', 'Artigos e Reflexões Pastorais')
@section('meta_description', 'Artigos, reflexões pastorais e estudos bíblicos escritos por pastores e articulistas credenciados da Convenção Batista Nacional de Goiás.')

@section('content')
<div class="bg-[#FFFDFB]">
    {{-- Header da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Voz Pastoral & Teológica</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Artigos e Reflexões
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    Pensamento bíblico, teologia prática, liderança eclesiástica e edificação pastoral para o fortalecimento das igrejas batistas nacionais em Goiás.
                </p>
            </div>
        </div>
    </section>

    {{-- Filtros e Busca --}}
    <section class="border-b border-neutral-200/80 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <form method="GET" action="{{ route('artigos.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                {{-- Campo de busca --}}
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-neutral-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <label for="busca-artigo" class="sr-only">Buscar artigos por título, conteúdo ou tema</label>
                    <input
                        id="busca-artigo"
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Buscar artigos por título, conteúdo ou tema..."
                        class="w-full rounded border border-neutral-300 bg-white pl-10 pr-4 py-2.5 text-sm text-neutral-800 placeholder-neutral-400 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    />
                </div>

                {{-- Filtro por Articulista --}}
                <div class="w-full lg:w-64">
                    <select
                        name="autor"
                        class="w-full rounded border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-800 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    >
                        <option value="">Todos os articulistas</option>
                        @foreach ($availableAuthors as $authorOption)
                            <option value="{{ $authorOption->slug }}" {{ $autor === $authorOption->slug || $autor == $authorOption->id ? 'selected' : '' }}>
                                {{ $authorOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Categoria Temática --}}
                <div class="w-full lg:w-56">
                    <select
                        name="categoria"
                        class="w-full rounded border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-800 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    >
                        <option value="">Todas as categorias</option>
                        @foreach ($availableCategories as $catOption)
                            <option value="{{ $catOption }}" {{ $categoria === $catOption ? 'selected' : '' }}>
                                {{ $catOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botões de ação --}}
                <div class="flex items-center gap-2 shrink-0">
                    <x-button variant="primary" size="md" type="submit" class="w-full lg:w-auto">
                        Filtrar
                    </x-button>

                    @if ($q !== '' || $autor !== '' || $categoria !== '')
                        <a
                            href="{{ route('artigos.index') }}"
                            class="inline-flex items-center justify-center px-3.5 py-2.5 text-sm font-medium text-neutral-600 hover:text-cbn-orange-ink border border-neutral-200 rounded hover:bg-neutral-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            Limpar filtros
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- Grid de Artigos --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-10 sm:py-14">
        @if ($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($articles as $article)
                    <x-article-card
                        :title="$article->title"
                        :slug="$article->slug"
                        :author-name="$article->author?->name ?? 'CBN Goiás'"
                        :author-slug="$article->author?->slug"
                        :author-avatar="$article->author?->avatar"
                        :category="$article->category"
                        :reading-time="$article->reading_time"
                        :excerpt="$article->excerpt"
                        :published-at="$article->published_at"
                    />
                @endforeach
            </div>

            {{-- Paginação --}}
            @if ($articles->hasPages())
                <div class="mt-12">
                    <x-pagination
                        :current-page="$articles->currentPage()"
                        :total-pages="$articles->lastPage()"
                        :prev-url="$articles->previousPageUrl()"
                        :next-url="$articles->nextPageUrl()"
                    />
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-16 px-4 bg-white rounded-lg border border-neutral-200/80 max-w-xl mx-auto space-y-4">
                <div class="w-14 h-14 mx-auto rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                    Nenhum artigo encontrado
                </h2>
                <p class="font-brand-sans text-neutral-600 text-sm max-w-md mx-auto leading-relaxed">
                    Não encontramos reflexões para os critérios de busca selecionados. Tente buscar por outros termos ou redefinir os filtros.
                </p>
                <div class="pt-2">
                    <x-button variant="primary" size="md" href="{{ route('artigos.index') }}">
                        Limpar filtros
                    </x-button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
