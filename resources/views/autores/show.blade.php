@extends('layouts.app')

@section('title', $author->name . ' — Articulista')
@section('meta_description', $author->bio ?: $author->name . ', ' . $author->role_title . '. Artigos e reflexões publicados no portal da CBN Goiás.')
@section('og_type', 'profile')
@section('og_image', $author->avatar_url ?? '')

@section('content')
<div class="bg-[#FFFDFB] py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
        {{-- Breadcrumb acessível --}}
        <nav aria-label="Breadcrumb" class="mb-8">
            <ol class="flex items-center gap-2 text-xs sm:text-sm text-neutral-500 flex-wrap">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-cbn-orange-ink transition">Início</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li>
                    <a href="{{ route('artigos.index') }}" class="hover:text-cbn-orange-ink transition">Artigos</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li class="text-neutral-800 font-medium truncate max-w-xs sm:max-w-sm" aria-current="page">
                    {{ $author->name }}
                </li>
            </ol>
        </nav>

        {{-- Cartão de Perfil do Articulista --}}
        <div class="bg-white border border-neutral-200/90 rounded-2xl p-6 sm:p-10 shadow-sm mb-12 sm:mb-16">
            <div class="flex flex-col md:flex-row gap-6 sm:gap-8 items-start">
                {{-- Avatar ou Monograma com Fallback --}}
                <div class="shrink-0">
                    @if ($author->avatar_url)
                        <img
                            src="{{ $author->avatar_url }}"
                            alt="{{ $author->name }}"
                            loading="lazy"
                            class="w-24 h-24 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white shadow-md ring-1 ring-neutral-200"
                        />
                    @else
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-cbn-black text-white flex items-center justify-center font-bold text-3xl sm:text-4xl font-brand-serif shadow-md ring-1 ring-neutral-200">
                            {{ strtoupper(substr($author->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                {{-- Dados e Biografia --}}
                <div class="flex-1 space-y-4">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-orange-50 text-cbn-orange-ink border border-orange-200/60">
                                Articulista Credenciado
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-700 border border-neutral-200">
                                <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                <span>{{ $totalArticles }} {{ $totalArticles === 1 ? 'artigo publicado' : 'artigos publicados' }}</span>
                            </span>
                        </div>

                        <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl text-cbn-black tracking-tight">
                            {{ $author->name }}
                        </h1>

                        @if ($author->role_title)
                            <p class="text-sm sm:text-base font-semibold text-cbn-orange-ink">
                                {{ $author->role_title }}
                            </p>
                        @endif
                    </div>

                    @if ($author->bio)
                        <div class="text-neutral-700 text-sm sm:text-base leading-relaxed font-brand-sans max-w-3xl">
                            {!! nl2br(e($author->bio)) !!}
                        </div>
                    @endif

                    {{-- Links sociais / de contato com validação de esquema --}}
                    @php
                        $safeSocialLinks = $author->getSafeSocialLinks();
                    @endphp
                    @if (!empty($safeSocialLinks))
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            @foreach ($safeSocialLinks as $network => $link)
                                @if (str_starts_with($link, 'mailto:'))
                                    <a
                                        href="{{ $link }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-white text-xs font-medium text-neutral-700 hover:text-cbn-orange-ink transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                                    >
                                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ substr($link, 7) }}</span>
                                    </a>
                                @else
                                    <a
                                        href="{{ $link }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-white text-xs font-medium text-neutral-700 hover:text-cbn-orange-ink transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                                    >
                                        <span class="capitalize">{{ $network }}</span>
                                        <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Artigos Publicados pelo Autor --}}
        <div>
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-neutral-200">
                <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">
                    Artigos Publicados
                </h2>
                <a
                    href="{{ route('artigos.index') }}"
                    class="text-xs sm:text-sm font-semibold text-cbn-orange-ink hover:text-cbn-orange-mid transition inline-flex items-center gap-1"
                >
                    <span>Ver todos os artigos do portal</span>
                    &rarr;
                </a>
            </div>

            @if ($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach ($articles as $article)
                        <x-article-card
                            :title="$article->title"
                            :slug="$article->slug"
                            :author-name="$author->name"
                            :author-slug="$author->slug"
                            :author-avatar="$author->avatar_url"
                            :category="$article->category"
                            :reading-time="$article->reading_time"
                            :excerpt="$article->excerpt"
                            :published-at="$article->published_at"
                        />
                    @endforeach
                </div>

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
                <div class="text-center py-16 px-4 bg-white rounded-xl border border-neutral-200">
                    <svg class="mx-auto h-12 w-12 text-neutral-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="font-brand-serif text-lg font-bold text-cbn-black mb-1">
                        Nenhum artigo publicado encontrado
                    </h3>
                    <p class="text-sm text-neutral-500 max-w-sm mx-auto mb-6">
                        Este articulista ainda não possui artigos públicos veiculados no portal da CBN-GO.
                    </p>
                    <a
                        href="{{ route('artigos.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded bg-cbn-orange-ink text-white text-xs font-bold uppercase tracking-wider hover:bg-cbn-orange-ink-dark transition"
                    >
                        Explorar outros artigos
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
