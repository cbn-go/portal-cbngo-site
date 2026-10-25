@extends('layouts.app')

@section('title', $news->title)
@section('meta_description', $news->excerpt ?? '')
@section('og_type', 'article')
@section('og_image', $news->featured_image_url ?? '')
@section('published_time', $news->published_at?->toIso8601String() ?? '')

@push('structured-data')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::newsArticle($news)) !!}</script>
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::breadcrumbs([
        ['name' => 'Início', 'url' => route('home')],
        ['name' => 'Notícias', 'url' => route('noticias.index')],
        ['name' => $news->title, 'url' => route('noticias.show', $news->slug)],
    ])) !!}</script>
@endpush

@section('content')
<article class="bg-[#FFFDFB] py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb acessível --}}
        <nav aria-label="Breadcrumb" class="mb-8">
            <ol class="flex items-center gap-2 text-xs sm:text-sm text-neutral-500 flex-wrap">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-cbn-orange-ink transition">Início</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li>
                    <a href="{{ route('noticias.index') }}" class="hover:text-cbn-orange-ink transition">Notícias</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li class="text-neutral-800 font-medium truncate max-w-xs sm:max-w-sm" aria-current="page">
                    {{ $news->title }}
                </li>
            </ol>
        </nav>

        {{-- Cabeçalho da Notícia --}}
        <header class="space-y-4 mb-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="bg-neutral-100 text-cbn-black text-xs font-bold tracking-wider uppercase px-3 py-1 rounded border border-neutral-200">
                    {{ $news->category }}
                </span>
                @if ($news->is_official)
                    <span class="bg-cbn-orange-ink text-white text-xs font-bold tracking-wider uppercase px-3 py-1 rounded shadow-sm">
                        Comunicado Oficial
                    </span>
                @endif
            </div>

            <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black leading-tight tracking-tight">
                {{ $news->title }}
            </h1>

            @if ($news->excerpt)
                <p class="font-brand-sans text-lg sm:text-xl text-neutral-600 leading-relaxed font-normal">
                    {{ $news->excerpt }}
                </p>
            @endif

            {{-- Metadados de Publicação --}}
            <div class="pt-4 border-t border-neutral-200/80 flex flex-wrap items-center justify-between gap-4 text-xs sm:text-sm text-neutral-500">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    @if ($news->published_at)
                        <span class="inline-flex items-center gap-1.5 font-medium text-neutral-700">
                            <svg class="w-4 h-4 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <time datetime="{{ $news->published_at->toISOString() }}">
                                {{ $news->published_at->translatedFormat('d \d\e F, Y') }}
                            </time>
                        </span>
                    @endif

                    @if ($news->publisherCity())
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $news->publisherCity() }}
                        </span>
                    @endif

                    @if ($news->church)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $news->publisherName() }}
                        </span>
                    @endif
                </div>

                {{-- Botões de Compartilhamento Social Superior --}}
                <x-share-buttons :title="$news->title" copy-label="Copiar link da notícia" />
            </div>
        </header>

        {{-- Imagem de Capa em Alta Resolução ou Placeholder Institucional --}}
        <figure class="mb-10 rounded-lg overflow-hidden border border-neutral-200/80 bg-neutral-100 shadow-sm">
            <x-media-image
                :path="$news->featured_image"
                :alt="$news->title"
                variant="cover"
                fallback-variant="cover"
                loading="eager"
                fetchpriority="high"
                class="w-full h-auto max-h-[500px]"
            />
        </figure>

        {{-- Conteúdo HTML do RichEditor do painel, sanitizado novamente no portal (XSS Guard) --}}
        <div class="prose max-w-none text-neutral-800 text-base sm:text-lg leading-relaxed font-brand-sans space-y-5">
            {!! \App\Support\Html::sanitize($news->content) !!}
        </div>

        {{-- Galeria de Fotos Interativa (Lightbox com Alpine.js) --}}
        <x-gallery-lightbox :images="$news->gallery_urls" :title="$news->title" />

        {{-- Rodapé da Matéria com Compartilhamento e Voltar --}}
        <footer class="mt-14 pt-8 border-t border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a
                href="{{ route('noticias.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-cbn-orange-ink hover:text-cbn-orange-mid transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar para todas as notícias</span>
            </a>

            <x-share-buttons :title="$news->title" label="Compartilhar matéria:" whatsapp-only />
        </footer>
    </div>
</article>

{{-- Notícias Relacionadas / Recomendadas --}}
@if ($relatedNews->count() > 0)
    <aside class="bg-neutral-50/70 border-t border-neutral-200/80 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">
                        Outras Notícias
                    </h2>
                    <p class="font-brand-sans text-neutral-600 text-sm mt-1">
                        Acompanhe outros acontecimentos e comunicados recentes.
                    </p>
                </div>
                <a
                    href="{{ route('noticias.index') }}"
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-cbn-orange-ink hover:text-cbn-orange-mid transition"
                >
                    Ver todas &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($relatedNews as $related)
                    <x-news-card
                        :title="$related->title"
                        :slug="$related->slug"
                        :category="$related->category"
                        :date="$related->published_at"
                        :image="$related->featured_image"
                        :excerpt="$related->excerpt"
                    />
                @endforeach
            </div>
        </div>
    </aside>
@endif
@endsection
