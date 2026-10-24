@extends('layouts.app')

@section('title', $article->title)
@section('meta_description', $article->excerpt ?? '')
@section('og_type', 'article')
@section('og_image', $article->cover_image_url ?? $article->author?->avatar_url ?? '')
@section('published_time', $article->published_at?->toIso8601String() ?? '')
@section('author_name', $article->author?->name ?? \App\Models\News::STATE_PUBLISHER)

@push('structured-data')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::article($article)) !!}</script>
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::breadcrumbs([
        ['name' => 'Início', 'url' => route('home')],
        ['name' => 'Artigos', 'url' => route('artigos.index')],
        ['name' => $article->title, 'url' => route('artigos.show', $article->slug)],
    ])) !!}</script>
@endpush

@section('content')
<article class="bg-[#FFFDFB] py-10 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
                    {{ $article->title }}
                </li>
            </ol>
        </nav>

        {{-- Cabeçalho Editorial do Artigo --}}
        <header class="space-y-6 mb-12">
            <div class="flex items-center gap-2">
                <span class="bg-neutral-100 text-cbn-black text-xs font-bold tracking-wider uppercase px-3 py-1 rounded border border-neutral-200">
                    {{ $article->category }}
                </span>
                <span class="text-neutral-500 text-xs font-medium inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $article->reading_time }} min de leitura</span>
                </span>
            </div>

            <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black leading-tight tracking-tight">
                {{ $article->title }}
            </h1>

            @if ($article->excerpt)
                <p class="font-brand-sans text-lg sm:text-xl text-neutral-600 leading-relaxed italic border-l-4 border-cbn-orange pl-4 py-1">
                    {{ $article->excerpt }}
                </p>
            @endif

            {{-- Autor e Metadados --}}
            <div class="pt-6 border-t border-neutral-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                @if ($article->author)
                <a
                    href="{{ route('autores.show', $article->author->slug) }}"
                    class="flex items-center gap-3.5 group/author focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded p-1 -m-1"
                >
                    @if ($article->author->avatar_url)
                        <img
                            src="{{ $article->author->avatar_url }}"
                            alt="{{ $article->author->name }}"
                            loading="lazy"
                            class="w-12 h-12 rounded-full object-cover border border-neutral-200 group-hover/author:border-cbn-orange transition-colors"
                        />
                    @else
                        <div class="w-12 h-12 rounded-full bg-cbn-black text-white flex items-center justify-center font-bold text-sm font-brand-serif shrink-0 group-hover/author:bg-cbn-orange-ink transition-colors">
                            {{ strtoupper(substr($article->author->name, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <span class="block font-bold text-sm text-cbn-black group-hover/author:text-cbn-orange-ink transition-colors">
                            {{ $article->author->name }}
                        </span>
                        <div class="flex items-center gap-2 text-xs text-neutral-500">
                            <span>{{ $article->author->role_title }}</span>
                            @if ($article->published_at)
                                <span aria-hidden="true">·</span>
                                <time datetime="{{ $article->published_at->toISOString() }}">
                                    {{ $article->published_at->translatedFormat('d \d\e F, Y') }}
                                </time>
                            @endif
                        </div>
                    </div>
                </a>
                @else
                    <div class="flex items-center gap-2 text-sm text-neutral-600">
                        <span class="font-bold text-cbn-black">{{ \App\Models\News::STATE_PUBLISHER }}</span>
                        @if ($article->published_at)
                            <span aria-hidden="true">·</span>
                            <time datetime="{{ $article->published_at->toISOString() }}">
                                {{ $article->published_at->translatedFormat('d \d\e F, Y') }}
                            </time>
                        @endif
                    </div>
                @endif

                {{-- Compartilhamento Social Superior --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-neutral-500 mr-1 hidden sm:inline">Compartilhar:</span>
                    
                    {{-- WhatsApp --}}
                    <a
                        href="https://api.whatsapp.com/send?text={{ rawurlencode($article->title . ' — ' . url()->current()) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Compartilhar no WhatsApp"
                        class="p-2 rounded bg-neutral-100 text-neutral-700 hover:text-white hover:bg-[#25D366] transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06s-1.05-.39-2-1.23c-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.48c-.16 0-.43.06-.66.31-.22.25-.86.84-.86 2.06 0 1.21.88 2.39 1.01 2.56.12.17 1.74 2.66 4.22 3.73.59.25 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.17-.48-.29"/>
                        </svg>
                    </a>

                    {{-- Facebook --}}
                    <a
                        href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Compartilhar no Facebook"
                        class="p-2 rounded bg-neutral-100 text-neutral-700 hover:text-white hover:bg-[#1877F2] transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
                        </svg>
                    </a>

                    {{-- Twitter / X --}}
                    <a
                        href="https://twitter.com/intent/tweet?text={{ rawurlencode($article->title) }}&url={{ rawurlencode(url()->current()) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Compartilhar no Twitter / X"
                        class="p-2 rounded bg-neutral-100 text-neutral-700 hover:text-white hover:bg-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    {{-- Copiar Link com Fallback --}}
                    <button
                        type="button"
                        x-data="{
                            copied: false,
                            async copyLink() {
                                const url = '{{ url()->current() }}';
                                if (navigator.clipboard && window.isSecureContext) {
                                    try {
                                        await navigator.clipboard.writeText(url);
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2500);
                                        return;
                                    } catch (e) {}
                                }
                                const el = document.createElement('textarea');
                                el.value = url;
                                el.setAttribute('readonly', '');
                                el.style.position = 'absolute';
                                el.style.left = '-9999px';
                                document.body.appendChild(el);
                                el.select();
                                try {
                                    document.execCommand('copy');
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2500);
                                } catch (err) {}
                                document.body.removeChild(el);
                            }
                        }"
                        @click="copyLink()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded border border-neutral-300 text-neutral-700 hover:text-cbn-black hover:bg-neutral-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        aria-label="Copiar link do artigo"
                    >
                        <svg x-show="!copied" class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-show="!copied">Copiar Link</span>
                        <span x-show="copied" x-cloak class="text-emerald-700 font-bold">Copiado!</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Imagem de Capa --}}
        @if ($article->cover_image_url)
            <figure class="mb-10 rounded-lg overflow-hidden border border-neutral-200/80 bg-neutral-100 shadow-sm">
                <img
                    src="{{ $article->cover_image_url }}"
                    alt="{{ $article->title }}"
                    width="1200"
                    height="500"
                    fetchpriority="high"
                    decoding="async"
                    class="w-full h-auto max-h-[500px] object-cover"
                />
            </figure>
        @endif

        {{-- Conteúdo HTML do RichEditor do painel, sanitizado novamente no portal (XSS Guard) --}}
        <div class="prose prose-lg max-w-none text-neutral-800 text-lg leading-relaxed font-brand-sans space-y-6">
            {!! \App\Support\Html::sanitize($article->content) !!}
        </div>

        {{-- Bloco do Autor ao final do Artigo --}}
        @if ($article->author)
        <div class="mt-16 pt-10 border-t border-neutral-200">
            <div class="bg-neutral-50 border border-neutral-200/90 rounded-xl p-6 sm:p-8 flex flex-col sm:flex-row gap-6 items-start">
                @if ($article->author->avatar_url)
                    <img
                        src="{{ $article->author->avatar_url }}"
                        alt="{{ $article->author->name }}"
                        loading="lazy"
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-white shadow-sm shrink-0"
                    />
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-cbn-black text-white flex items-center justify-center font-bold text-2xl font-brand-serif shadow-sm shrink-0">
                        {{ strtoupper(substr($article->author->name, 0, 2)) }}
                    </div>
                @endif

                <div class="space-y-3 flex-1">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-cbn-orange-ink">Sobre o Autor</span>
                        <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                            <a href="{{ route('autores.show', $article->author->slug) }}" class="hover:text-cbn-orange-ink transition-colors">
                                {{ $article->author->name }}
                            </a>
                        </h2>
                        <p class="text-xs sm:text-sm font-medium text-neutral-500">
                            {{ $article->author->role_title }}
                        </p>
                    </div>

                    @if ($article->author->bio)
                        <p class="text-neutral-700 text-sm leading-relaxed font-brand-sans">
                            {!! nl2br(e($article->author->bio)) !!}
                        </p>
                    @endif

                    {{-- Redes sociais do articulista com validação de esquema --}}
                    @php
                        $safeSocialLinks = $article->author->getSafeSocialLinks();
                    @endphp
                    @if (!empty($safeSocialLinks))
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            @foreach ($safeSocialLinks as $network => $link)
                                @if (str_starts_with($link, 'mailto:'))
                                    <a
                                        href="{{ $link }}"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-neutral-600 hover:text-cbn-orange-ink transition-colors"
                                    >
                                        <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ substr($link, 7) }}</span>
                                    </a>
                                @else
                                    <a
                                        href="{{ $link }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-neutral-600 hover:text-cbn-orange-ink transition-colors"
                                    >
                                        <span class="capitalize">{{ $network }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2">
                        <a
                            href="{{ route('autores.show', $article->author->slug) }}"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-cbn-orange-ink hover:text-cbn-orange-mid transition"
                        >
                            <span>Ver todos os artigos deste autor</span>
                            &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Rodapé com Ações e Voltar --}}
        <footer class="mt-12 pt-8 border-t border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a
                href="{{ route('artigos.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-cbn-orange-ink hover:text-cbn-orange-mid transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar para todos os artigos</span>
            </a>
        </footer>
    </div>
</article>

{{-- Artigos Recomendados --}}
@if ($relatedArticles->count() > 0)
    <aside class="bg-neutral-50/70 border-t border-neutral-200/80 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">
                        Outros Artigos Recomendados
                    </h2>
                    <p class="font-brand-sans text-neutral-600 text-sm mt-1">
                        Continue sua leitura com outras reflexões e estudos teológicos de nossos pastores.
                    </p>
                </div>
                <a
                    href="{{ route('artigos.index') }}"
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-cbn-orange-ink hover:text-cbn-orange-mid transition"
                >
                    Ver todos &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($relatedArticles as $related)
                    <x-article-card
                        :title="$related->title"
                        :slug="$related->slug"
                        :author-name="$related->author?->name ?? 'CBN Goiás'"
                        :author-slug="$related->author?->slug"
                        :author-avatar="$related->author?->avatar_url"
                        :category="$related->category"
                        :reading-time="$related->reading_time"
                        :excerpt="$related->excerpt"
                        :published-at="$related->published_at"
                    />
                @endforeach
            </div>
        </div>
    </aside>
@endif
@endsection
