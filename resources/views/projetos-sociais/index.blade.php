@extends('layouts.app')

@section('title', 'Projetos Sociais')
@section('meta_description', 'Projetos sociais, culturais, de educação e de liderança das igrejas da Convenção Batista Nacional do Estado de Goiás. Conheça e ajude.')

@php
    // Filtros atuais, sem vazios, para montar os links das categorias preservando os demais.
    $currentFilters = array_filter(['q' => $q, 'cidade' => $cidade, 'igreja' => $igreja], fn ($value) => $value !== '');
    $hasFilters = $currentFilters !== [] || $categoria !== null;
    $chipBase = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange';
@endphp

@section('content')
<div class="bg-[#FFFDFB]">
    {{-- Header da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Missão integral</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Projetos Sociais
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    As igrejas da CBN Goiás anunciam o Evangelho com palavras e com obras: cuidado com quem sofre, cultura, educação e formação de líderes nas comunidades goianas.
                </p>
                <p class="font-brand-sans text-neutral-600 text-sm leading-relaxed">
                    As categorias seguem as dimensões da missão integral do
                    <a href="{{ config('cbngo.lausanne_url') }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-cbn-orange-ink underline underline-offset-2 hover:text-cbn-orange-mid focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">Pacto de Lausanne<span class="sr-only"> (abre em nova aba)</span></a>.
                    Conheça os projetos e descubra como ajudar.
                </p>
            </div>
        </div>
    </section>

    {{-- Filtros e Busca --}}
    <section class="border-b border-neutral-200/80 bg-white py-6 space-y-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 space-y-5">
            {{-- Categorias (links compartilháveis que preservam os demais filtros) --}}
            <nav aria-label="Filtrar por categoria">
                <ul class="flex flex-wrap gap-2">
                    <li>
                        <a
                            href="{{ route('projetos-sociais.index', $currentFilters) }}"
                            @if ($categoria === null) aria-current="page" @endif
                            class="{{ $chipBase }} {{ $categoria === null ? 'bg-cbn-black text-white border-cbn-black' : 'bg-white text-neutral-700 border-neutral-300 hover:border-neutral-500' }}"
                        >Todas</a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a
                                href="{{ route('projetos-sociais.index', $currentFilters + ['categoria' => $category->slug()]) }}"
                                title="{{ $category->description() }}"
                                @if ($categoria === $category) aria-current="page" @endif
                                class="{{ $chipBase }} {{ $categoria === $category ? $category->badgeClasses().' border-transparent' : 'bg-white text-neutral-700 border-neutral-300 hover:border-neutral-500' }}"
                            >{{ $category->value }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <form method="GET" action="{{ route('projetos-sociais.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                @if ($categoria)
                    <input type="hidden" name="categoria" value="{{ $categoria->slug() }}" />
                @endif

                {{-- Campo de busca --}}
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-neutral-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <label for="busca-projeto" class="sr-only">Buscar projetos por nome ou descrição</label>
                    <input
                        id="busca-projeto"
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Buscar projetos por nome ou descrição..."
                        class="w-full rounded border border-neutral-300 bg-white pl-10 pr-4 py-2.5 text-sm text-neutral-800 placeholder-neutral-400 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    />
                </div>

                {{-- Filtro por Cidade --}}
                <div class="w-full lg:w-56">
                    <label for="filtro-cidade" class="sr-only">Filtrar projetos por município</label>
                    <select
                        id="filtro-cidade"
                        name="cidade"
                        class="w-full rounded border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-800 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    >
                        <option value="">Todos os municípios</option>
                        @foreach ($availableCities as $cityOption)
                            <option value="{{ $cityOption }}" {{ $cidade === $cityOption ? 'selected' : '' }}>
                                {{ $cityOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Igreja --}}
                <div class="w-full lg:w-64">
                    <label for="filtro-igreja" class="sr-only">Filtrar projetos por igreja</label>
                    <select
                        id="filtro-igreja"
                        name="igreja"
                        class="w-full rounded border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-800 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    >
                        <option value="">Todas as igrejas</option>
                        @foreach ($availableChurches as $churchSlug => $churchName)
                            <option value="{{ $churchSlug }}" {{ $igreja === $churchSlug ? 'selected' : '' }}>
                                {{ $churchName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botões de ação --}}
                <div class="flex items-center gap-2 shrink-0">
                    <x-button variant="primary" size="md" type="submit" class="w-full lg:w-auto">
                        Filtrar
                    </x-button>

                    @if ($hasFilters)
                        <a
                            href="{{ route('projetos-sociais.index') }}"
                            class="inline-flex items-center justify-center px-3.5 py-2.5 text-sm font-medium text-neutral-600 hover:text-cbn-orange-ink border border-neutral-200 rounded hover:bg-neutral-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            Limpar filtros
                        </a>
                    @endif
                </div>
            </form>

            @if ($categoria)
                <p class="text-sm text-neutral-600">
                    <strong class="text-cbn-black">{{ $categoria->value }}:</strong>
                    {{ $categoria->description() }}
                    <span class="text-neutral-500">({{ $categoria->lausanneReference() }})</span>
                </p>
            @endif
        </div>
    </section>

    {{-- Listagem de Projetos --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-10 sm:py-14">
        @if ($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>

            {{-- Paginação --}}
            @if ($projects->hasPages())
                <div class="mt-12">
                    <x-pagination
                        :current-page="$projects->currentPage()"
                        :total-pages="$projects->lastPage()"
                        :prev-url="$projects->previousPageUrl()"
                        :next-url="$projects->nextPageUrl()"
                    />
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-16 px-4 bg-white rounded-lg border border-neutral-200/80 max-w-xl mx-auto space-y-4">
                <div class="w-14 h-14 mx-auto rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                    Nenhum projeto encontrado
                </h2>
                <p class="font-brand-sans text-neutral-600 text-sm max-w-md mx-auto leading-relaxed">
                    Não encontramos projetos para os filtros selecionados. Experimente outra categoria, município ou termo de busca.
                </p>
                @if ($hasFilters)
                    <div class="pt-2">
                        <x-button variant="primary" size="md" href="{{ route('projetos-sociais.index') }}">
                            Limpar filtros
                        </x-button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
