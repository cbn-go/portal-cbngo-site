@props([
    'title',
    'slug',
    'authorName',
    'authorSlug' => null,
    'authorAvatar' => null,
    'category' => null,
    'readingTime' => 5,
    'excerpt' => null,
    'publishedAt' => null,
])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col justify-between bg-white border border-neutral-200/90 rounded p-7 hover:shadow-md hover:border-neutral-300 transition-all duration-200']) }}>
    <div>
        <div class="flex items-center justify-between gap-2 text-xs text-neutral-400 mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $readingTime }} min de leitura</span>
                @if ($publishedAt)
                    <span class="text-neutral-300" aria-hidden="true">·</span>
                    <time datetime="{{ is_a($publishedAt, '\Carbon\Carbon') ? $publishedAt->toISOString() : '' }}">
                        {{ is_a($publishedAt, '\Carbon\Carbon') ? $publishedAt->translatedFormat('d/m/Y') : $publishedAt }}
                    </time>
                @endif
            </div>

            @if ($category)
                <span class="bg-neutral-100 text-neutral-700 text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border border-neutral-200/80">
                    {{ $category }}
                </span>
            @endif
        </div>

        <h3 class="font-brand-serif font-bold text-xl text-cbn-black leading-snug group-hover:text-cbn-orange transition-colors line-clamp-2">
            <a
                href="{{ route('artigos.show', $slug) }}"
                class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                {{ $title }}
            </a>
        </h3>

        @if ($excerpt)
            <p class="text-neutral-600 text-sm mt-4 leading-relaxed italic border-l-2 border-cbn-gold pl-3.5 line-clamp-3 font-brand-sans">
                {{ $excerpt }}
            </p>
        @endif
    </div>

    <div class="pt-6 mt-6 border-t border-neutral-100 flex items-center justify-between">
        @if ($authorSlug)
            <a href="{{ route('autores.show', $authorSlug) }}" class="flex items-center gap-3 group/author focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded p-1 -m-1">
                @if ($authorAvatar)
                    <img
                        src="{{ $authorAvatar }}"
                        alt="{{ $authorName }}"
                        loading="lazy"
                        class="w-10 h-10 rounded-full object-cover border border-neutral-200 group-hover/author:border-cbn-orange transition-colors"
                    />
                @else
                    <div class="w-10 h-10 rounded-full bg-cbn-black text-white flex items-center justify-center font-bold text-xs font-brand-serif shrink-0 group-hover/author:bg-cbn-orange transition-colors">
                        {{ strtoupper(substr($authorName, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <span class="block text-xs font-bold text-cbn-black leading-tight group-hover/author:text-cbn-orange transition-colors">
                        {{ $authorName }}
                    </span>
                    <span class="block text-[11px] text-neutral-500 leading-tight">
                        Articulista CBN-GO
                    </span>
                </div>
            </a>
        @else
            <div class="flex items-center gap-3">
                @if ($authorAvatar)
                    <img
                        src="{{ $authorAvatar }}"
                        alt="{{ $authorName }}"
                        loading="lazy"
                        class="w-10 h-10 rounded-full object-cover border border-neutral-200"
                    />
                @else
                    <div class="w-10 h-10 rounded-full bg-cbn-black text-white flex items-center justify-center font-bold text-xs font-brand-serif shrink-0">
                        {{ strtoupper(substr($authorName, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <span class="block text-xs font-bold text-cbn-black leading-tight">
                        {{ $authorName }}
                    </span>
                    <span class="block text-[11px] text-neutral-500 leading-tight">
                        Articulista CBN-GO
                    </span>
                </div>
            </div>
        @endif

        <a
            href="{{ route('artigos.show', $slug) }}"
            aria-label="Ler artigo: {{ $title }}"
            class="text-neutral-400 hover:text-cbn-orange transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded p-1"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>
</article>
