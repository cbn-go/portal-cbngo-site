@extends('layouts.app')

@section('title', $news->title . ' — CBN Goiás')

@section('content')
<article class="bg-[#FFFDFB] py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb acessível --}}
        <nav aria-label="Breadcrumb" class="mb-8">
            <ol class="flex items-center gap-2 text-xs sm:text-sm text-neutral-500 flex-wrap">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-cbn-orange transition">Início</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li>
                    <a href="{{ route('noticias.index') }}" class="hover:text-cbn-orange transition">Notícias</a>
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
                    <span class="bg-cbn-orange text-white text-xs font-bold tracking-wider uppercase px-3 py-1 rounded shadow-sm">
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

                    @if ($news->city)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $news->city }}
                        </span>
                    @endif

                    @if ($news->church_name)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $news->church_name }}
                        </span>
                    @endif
                </div>

                {{-- Botões de Compartilhamento Social Superior --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-neutral-400 mr-1 hidden sm:inline">Compartilhar:</span>
                    
                    {{-- WhatsApp --}}
                    <a
                        href="https://api.whatsapp.com/send?text={{ rawurlencode($news->title . ' — ' . url()->current()) }}"
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
                        href="https://twitter.com/intent/tweet?text={{ rawurlencode($news->title) }}&url={{ rawurlencode(url()->current()) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Compartilhar no Twitter / X"
                        class="p-2 rounded bg-neutral-100 text-neutral-700 hover:text-white hover:bg-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    {{-- Copiar Link --}}
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
                        aria-label="Copiar link da notícia"
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

        {{-- Imagem de Capa em Alta Resolução --}}
        @if ($news->image_url)
            <figure class="mb-10 rounded-lg overflow-hidden border border-neutral-200/80 bg-neutral-100 shadow-sm">
                <img
                    src="{{ $news->image_url }}"
                    alt="{{ $news->title }}"
                    class="w-full max-h-[500px] object-cover"
                />
            </figure>
        @else
            <div class="mb-10 aspect-[21/9] w-full rounded-lg bg-gradient-to-br from-cbn-black via-neutral-900 to-cbn-black flex items-center justify-center p-8 text-white shadow-sm border border-neutral-200/60">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-16 h-16 text-cbn-orange/60" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect width="48" height="48" rx="8" fill="currentColor" fill-opacity="0.1"/>
                        <path d="M24 10V38M14 20H34" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-xs font-mono uppercase tracking-widest text-neutral-400">Convenção Batista Nacional de Goiás</span>
                </div>
            </div>
        @endif

        {{-- Conteúdo Formatado e Sanitizado (XSS Guard) --}}
        <div class="prose max-w-none text-neutral-800 text-base sm:text-lg leading-relaxed font-brand-sans space-y-5">
            {!! nl2br(e($news->content)) !!}
        </div>

        {{-- Galeria de Fotos Interativa (Lightbox com Alpine.js) --}}
        @if (!empty($news->gallery_images) && count($news->gallery_images) > 0)
            <section
                x-data="{
                    isOpen: false,
                    currentIndex: 0,
                    images: {{ Js::from(array_values($news->gallery_images)) }},
                    open(idx) {
                        this.currentIndex = idx;
                        this.isOpen = true;
                        document.body.classList.add('overflow-hidden');
                    },
                    close() {
                        this.isOpen = false;
                        document.body.classList.remove('overflow-hidden');
                    },
                    next() {
                        this.currentIndex = (this.currentIndex + 1) % this.images.length;
                    },
                    prev() {
                        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                    }
                }"
                @keydown.escape.window="close()"
                @keydown.arrow-right.window="if (isOpen) next()"
                @keydown.arrow-left.window="if (isOpen) prev()"
                class="mt-14 pt-10 border-t border-neutral-200"
            >
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                            Galeria de Fotos
                        </h2>
                        <p class="text-xs sm:text-sm text-neutral-500 font-brand-sans">
                            Registros fotográficos do evento e ação ministerial.
                        </p>
                    </div>
                    <span class="text-xs text-neutral-400 font-medium">
                        {{ count($news->gallery_images) }} {{ count($news->gallery_images) === 1 ? 'foto' : 'fotos' }}
                    </span>
                </div>

                {{-- Grid de Miniaturas --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($news->gallery_images as $index => $photoUrl)
                        <button
                            type="button"
                            @click="open({{ $index }})"
                            class="group relative aspect-square rounded-lg overflow-hidden bg-neutral-100 border border-neutral-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange transition"
                            aria-label="Ver foto {{ $index + 1 }} em tamanho ampliado"
                        >
                            <img
                                src="{{ $photoUrl }}"
                                alt="Foto {{ $index + 1 }} - {{ $news->title }}"
                                loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                </svg>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Modal Lightbox --}}
                <div
                    x-show="isOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 sm:p-6"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Visualizador de fotos"
                >
                    {{-- Fechar com clique fora ou botão --}}
                    <button
                        type="button"
                        @click="close()"
                        class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        aria-label="Fechar galeria (Esc)"
                    >
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    {{-- Botão Anterior --}}
                    <button
                        type="button"
                        x-show="images.length > 1"
                        @click.stop="prev()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full hover:bg-white/10 transition z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        aria-label="Foto anterior"
                    >
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    {{-- Imagem em exibição --}}
                    <div class="relative max-w-5xl max-h-[85vh] flex flex-col items-center" @click.outside="close()">
                        <img
                            :src="images[currentIndex]"
                            :alt="'Foto ' + (currentIndex + 1)"
                            class="max-w-full max-h-[80vh] object-contain rounded shadow-2xl"
                        />
                        <div class="mt-3 text-center text-xs text-white/70 font-mono">
                            <span x-text="currentIndex + 1"></span> de <span x-text="images.length"></span>
                        </div>
                    </div>

                    {{-- Botão Próxima --}}
                    <button
                        type="button"
                        x-show="images.length > 1"
                        @click.stop="next()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full hover:bg-white/10 transition z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        aria-label="Próxima foto"
                    >
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </section>
        @endif

        {{-- Rodapé da Matéria com Compartilhamento e Voltar --}}
        <footer class="mt-14 pt-8 border-t border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a
                href="{{ route('noticias.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-cbn-orange hover:text-cbn-orange-mid transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar para todas as notícias</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs text-neutral-500 font-medium">Compartilhar matéria:</span>
                <a
                    href="https://api.whatsapp.com/send?text={{ rawurlencode($news->title . ' — ' . url()->current()) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Compartilhar no WhatsApp"
                    class="p-2 rounded bg-neutral-100 text-neutral-700 hover:text-white hover:bg-[#25D366] transition"
                >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06s-1.05-.39-2-1.23c-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.48c-.16 0-.43.06-.66.31-.22.25-.86.84-.86 2.06 0 1.21.88 2.39 1.01 2.56.12.17 1.74 2.66 4.22 3.73.59.25 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.17-.48-.29"/>
                    </svg>
                </a>
            </div>
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
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-cbn-orange hover:text-cbn-orange-mid transition"
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
                        :image="$related->image_url"
                        :excerpt="$related->excerpt"
                    />
                @endforeach
            </div>
        </div>
    </aside>
@endif
@endsection
