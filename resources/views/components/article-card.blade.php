@props([
    'title',
    'slug',
    'authorName',
    'authorAvatar' => null,
    'readingTime' => 5,
    'excerpt' => null,
    'publishedAt' => null,
])

<article {{ $attributes->merge(['class' => 'group flex flex-col justify-between bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md hover:border-cbn-gold/40 transition-all duration-200']) }}>
    <div class="space-y-3">
        <!-- Metadados de Topo: Tempo de Leitura e Data -->
        <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
            <span class="inline-flex items-center gap-1 bg-cbn-navy/5 text-cbn-navy px-2.5 py-0.5 rounded-full font-semibold">
                <svg class="w-3.5 h-3.5 text-cbn-gold-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $readingTime }} min de leitura
            </span>

            @if ($publishedAt)
                <time class="text-slate-400">
                    {{ is_a($publishedAt, '\Carbon\Carbon') ? $publishedAt->translatedFormat('d/m/Y') : $publishedAt }}
                </time>
            @endif
        </div>

        <!-- Título -->
        <h3 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-cbn-gold-dark transition font-brand-serif line-clamp-2 leading-snug">
            <a
                href="/artigos/{{ $slug }}"
                class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded"
            >
                {{ $title }}
            </a>
        </h3>

        <!-- Resumo -->
        @if ($excerpt)
            <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed font-brand-sans">
                {{ $excerpt }}
            </p>
        @endif
    </div>

    <!-- Bloco do Articulista -->
    <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if ($authorAvatar)
                <img
                    src="{{ $authorAvatar }}"
                    alt="{{ $authorName }}"
                    loading="lazy"
                    class="w-10 h-10 rounded-full object-cover border border-slate-200"
                />
            @else
                <div class="w-10 h-10 rounded-full bg-cbn-navy text-cbn-gold flex items-center justify-center font-bold text-sm font-brand-serif border border-cbn-gold/30 shrink-0">
                    {{ strtoupper(substr($authorName, 0, 2)) }}
                </div>
            @endif
            <div>
                <span class="block text-xs font-semibold text-slate-900 leading-tight">
                    {{ $authorName }}
                </span>
                <span class="block text-[11px] text-slate-500 leading-tight">
                    Articulista CBN-GO
                </span>
            </div>
        </div>

        <a
            href="/artigos/{{ $slug }}"
            class="text-xs font-semibold text-cbn-navy group-hover:text-cbn-gold-dark transition inline-flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded px-1.5 py-0.5"
        >
            <span>Ler</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</article>
