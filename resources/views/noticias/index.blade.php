@extends('layouts.app')

@section('title', 'Notícias e Comunicados da CBN Goiás')

@section('content')
<div class="bg-[#FFFDFB]">
    {{-- Header da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange">Comunicação Oficial</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Notícias e Comunicados
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    Acompanhe as ações ministeriais, eventos das congregações e comunicados oficiais da Convenção Batista Nacional do Estado de Goiás.
                </p>
            </div>
        </div>
    </section>

    {{-- Filtros e Busca --}}
    <section class="border-b border-neutral-200/80 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <form method="GET" action="{{ route('noticias.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                {{-- Campo de busca --}}
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-neutral-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Buscar notícias por título ou conteúdo..."
                        class="w-full rounded border border-neutral-300 bg-white pl-10 pr-4 py-2.5 text-sm text-neutral-800 placeholder-neutral-400 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    />
                </div>

                {{-- Filtro por Cidade --}}
                <div class="w-full lg:w-56">
                    <select
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
                    <select
                        name="igreja"
                        class="w-full rounded border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-800 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    >
                        <option value="">Todas as congregações</option>
                        @foreach ($availableChurches as $churchOption)
                            <option value="{{ $churchOption }}" {{ $igreja === $churchOption ? 'selected' : '' }}>
                                {{ $churchOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Checkbox Comunicados Oficiais --}}
                <div class="flex items-center shrink-0">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700 select-none px-2 py-1.5 rounded hover:bg-neutral-50 transition">
                        <input
                            type="checkbox"
                            name="apenas_oficiais"
                            value="1"
                            {{ $apenasOficiais ? 'checked' : '' }}
                            class="rounded border-neutral-300 text-cbn-orange focus:ring-cbn-orange h-4 w-4"
                        />
                        <span>Somente oficiais</span>
                    </label>
                </div>

                {{-- Botões de ação --}}
                <div class="flex items-center gap-2 shrink-0">
                    <x-button variant="primary" size="md" type="submit" class="w-full lg:w-auto">
                        Filtrar
                    </x-button>

                    @if ($q !== '' || $cidade !== '' || $igreja !== '' || $apenasOficiais)
                        <a
                            href="{{ route('noticias.index') }}"
                            class="inline-flex items-center justify-center px-3.5 py-2.5 text-sm font-medium text-neutral-600 hover:text-cbn-orange border border-neutral-200 rounded hover:bg-neutral-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            Limpar filtros
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- Listagem de Notícias --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-10 sm:py-14">
        @if ($news->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($news as $item)
                    <article class="group flex flex-col bg-white border {{ $item->is_official ? 'border-cbn-orange/50 shadow-sm ring-1 ring-cbn-orange/20' : 'border-neutral-200/90' }} rounded overflow-hidden hover:border-neutral-400 hover:shadow-md transition-all duration-200">
                        <a
                            href="{{ route('noticias.show', $item->slug) }}"
                            class="relative block aspect-video w-full overflow-hidden bg-neutral-100 shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            @if ($item->image_url)
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->title }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-cbn-black via-neutral-800 to-cbn-black flex items-center justify-center p-6 text-white group-hover:scale-105 transition-transform duration-500">
                                    <svg class="w-14 h-14 text-cbn-orange/50" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <rect width="48" height="48" rx="8" fill="currentColor" fill-opacity="0.1"/>
                                        <path d="M24 10V38M14 20H34" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <span class="bg-white/95 text-cbn-black text-[11px] font-bold tracking-wider uppercase px-2.5 py-1 rounded shadow-sm border border-neutral-200">
                                    {{ $item->category }}
                                </span>
                                @if ($item->is_official)
                                    <span class="bg-cbn-orange text-white text-[11px] font-bold tracking-wider uppercase px-2.5 py-1 rounded shadow-sm">
                                        Comunicado Oficial
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="p-6 flex flex-col flex-1 justify-between">
                            <div>
                                <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 mb-2.5">
                                    @if ($item->published_at)
                                        <time datetime="{{ $item->published_at->toISOString() }}">
                                            {{ $item->published_at->translatedFormat('d \d\e F, Y') }}
                                        </time>
                                    @endif
                                    @if ($item->city)
                                        <span aria-hidden="true">·</span>
                                        <span>{{ $item->city }}</span>
                                    @endif
                                </div>

                                <h2 class="font-brand-serif font-bold text-xl text-cbn-black leading-snug group-hover:text-cbn-orange transition-colors line-clamp-2">
                                    <a
                                        href="{{ route('noticias.show', $item->slug) }}"
                                        class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
                                    >
                                        {{ $item->title }}
                                    </a>
                                </h2>

                                @if ($item->excerpt)
                                    <p class="text-neutral-600 text-sm mt-3 leading-relaxed line-clamp-3 font-brand-sans">
                                        {{ $item->excerpt }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-5 mt-5 border-t border-neutral-100 flex items-center justify-between">
                                <span class="text-xs text-neutral-500 truncate max-w-[160px]">
                                    {{ $item->church_name ?? 'CBN Goiás' }}
                                </span>
                                <a
                                    href="{{ route('noticias.show', $item->slug) }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-cbn-orange hover:text-cbn-orange-mid tracking-wide focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
                                >
                                    <span>Ler notícia</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Paginação --}}
            @if ($news->hasPages())
                <div class="mt-12">
                    <x-pagination
                        :current-page="$news->currentPage()"
                        :total-pages="$news->lastPage()"
                        :prev-url="$news->previousPageUrl()"
                        :next-url="$news->nextPageUrl()"
                    />
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-16 px-4 bg-white rounded-lg border border-neutral-200/80 max-w-xl mx-auto space-y-4">
                <div class="w-14 h-14 mx-auto rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                    Nenhuma notícia encontrada
                </h2>
                <p class="font-brand-sans text-neutral-600 text-sm max-w-md mx-auto leading-relaxed">
                    Não encontramos publicações para os critérios de busca selecionados. Experimente pesquisar por outros termos ou redefinir os filtros.
                </p>
                <div class="pt-2">
                    <x-button variant="primary" size="md" href="{{ route('noticias.index') }}">
                        Limpar filtros
                    </x-button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
