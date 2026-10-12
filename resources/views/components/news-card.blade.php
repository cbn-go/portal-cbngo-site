@props([
    'title',
    'slug',
    'category' => 'Geral',
    'date' => null,
    'image' => null,
    'excerpt' => null,
    'featured' => false,
])

<article {{ $attributes->merge(['class' => 'group flex flex-col bg-[#FFFDFB] border border-neutral-200/90 rounded overflow-hidden hover:border-neutral-400 hover:shadow-md transition-all duration-200']) }}>
    <a
        href="/noticias/{{ $slug }}"
        class="relative block aspect-video w-full overflow-hidden bg-neutral-100 shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
    >
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
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

        <span class="absolute top-3 left-3 bg-white/95 text-cbn-black text-[11px] font-bold tracking-wider uppercase px-2.5 py-1 rounded shadow-sm border border-neutral-200">
            {{ $category }}
        </span>
    </a>

    <div class="p-6 flex flex-col flex-1 justify-between">
        <div>
            @if ($date)
                <time class="text-xs font-medium text-neutral-500 mb-2.5 block">
                    {{ is_a($date, '\Carbon\Carbon') ? $date->translatedFormat('d \d\e F, Y') : $date }}
                </time>
            @endif

            <h3 class="font-brand-serif font-bold text-lg text-cbn-black leading-snug group-hover:text-cbn-orange transition-colors line-clamp-2">
                <a
                    href="/noticias/{{ $slug }}"
                    class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
                >
                    {{ $title }}
                </a>
            </h3>

            @if ($excerpt)
                <p class="text-neutral-600 text-sm mt-3 leading-relaxed line-clamp-3">
                    {{ $excerpt }}
                </p>
            @endif
        </div>

        <div class="pt-5 mt-4 border-t border-neutral-100">
            <a
                href="/noticias/{{ $slug }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-cbn-orange hover:text-cbn-orange-mid tracking-wide focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <span>Ver o impacto</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>
</article>
