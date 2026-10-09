@props([
    'title',
    'slug',
    'category' => 'Geral',
    'date' => null,
    'image' => null,
    'excerpt' => null,
    'featured' => false,
])

<article {{ $attributes->merge(['class' => 'group flex flex-col bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md hover:border-cbn-gold/40 transition-all duration-200']) }}>
    <!-- Imagem / Thumbnail -->
    <a
        href="/noticias/{{ $slug }}"
        class="relative block aspect-[16/9] w-full overflow-hidden bg-cbn-navy shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold"
    >
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
        @else
            <!-- Fallback SVG Nobre Institucional -->
            <div class="w-full h-full bg-gradient-to-br from-cbn-navy via-cbn-navy-light to-cbn-navy-dark flex items-center justify-center p-6 text-white group-hover:scale-105 transition-transform duration-300">
                <svg class="w-16 h-16 text-cbn-gold/40 group-hover:text-cbn-gold/60 transition" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect width="48" height="48" rx="8" fill="currentColor" fill-opacity="0.1"/>
                    <path d="M24 10V38M14 20H34" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                <span class="absolute bottom-3 right-3 text-[10px] font-bold text-cbn-gold uppercase tracking-widest font-brand-serif bg-black/40 px-2 py-0.5 rounded">
                    CBN-GO
                </span>
            </div>
        @endif

        <div class="absolute top-3 left-3">
            <x-badge variant="gold" size="sm">
                {{ $category }}
            </x-badge>
        </div>
    </a>

    <!-- Conteúdo -->
    <div class="flex flex-col flex-grow p-5 space-y-3">
        @if ($date)
            <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <time>{{ is_a($date, '\Carbon\Carbon') ? $date->translatedFormat('d \d\e F, Y') : $date }}</time>
            </div>
        @endif

        <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-cbn-gold-dark transition font-brand-serif line-clamp-2 leading-snug">
            <a
                href="/noticias/{{ $slug }}"
                class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded"
            >
                {{ $title }}
            </a>
        </h3>

        @if ($excerpt)
            <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed font-brand-sans">
                {{ $excerpt }}
            </p>
        @endif

        <div class="pt-2 mt-auto">
            <a
                href="/noticias/{{ $slug }}"
                class="inline-flex items-center justify-between w-full text-xs font-semibold text-cbn-navy group-hover:text-cbn-gold-dark transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded py-0.5"
            >
                <span>Ler notícia completa</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>
</article>
