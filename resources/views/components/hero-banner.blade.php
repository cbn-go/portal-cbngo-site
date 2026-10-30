@props([
    'banners',
    'interval' => 7000,
])

@php
    $total = $banners->count();
    $isCarousel = $total > 1;
@endphp

{{-- Hero de avisos (Issue #39): foto 1:1 à direita e selo, título, subtítulo, botão e data/local à esquerda --}}
<section
    data-testid="hero-banner"
    @if ($isCarousel)
        x-data="heroCarousel({ count: {{ $total }}, interval: {{ (int) $interval }} })"
        x-on:mouseenter="hover = true"
        x-on:mouseleave="hover = false"
        x-on:focusin="focus = true"
        x-on:focusout="focus = $el.contains($event.relatedTarget)"
        x-on:keydown.left.prevent="prev()"
        x-on:keydown.right.prevent="next()"
        x-on:touchstart.passive="swipeStart($event)"
        x-on:touchend="swipeEnd($event)"
        aria-roledescription="carousel"
        aria-label="Avisos em destaque"
    @else
        aria-label="Aviso em destaque"
    @endif
    {{ $attributes->merge(['class' => 'relative overflow-hidden bg-cbn-sand border-b border-neutral-200']) }}
>
    {{-- Fundo decorativo: a foto ativa desfocada e clara, com gradiente até o creme da página --}}
    <div class="absolute inset-0" aria-hidden="true">
        @foreach ($banners as $banner)
            @if ($banner->thumb_url)
                <div
                    @if ($isCarousel)
                        x-bind:class="{ 'opacity-25': active === {{ $loop->index }}, 'opacity-0': active !== {{ $loop->index }} }"
                    @endif
                    class="absolute inset-0 bg-cover bg-center blur-3xl scale-125 motion-safe:transition-opacity motion-safe:duration-700 {{ $loop->first ? 'opacity-25' : 'opacity-0' }}"
                    style="background-image: url('{{ $banner->thumb_url }}');"
                ></div>
            @endif
        @endforeach
        <div class="absolute inset-0 bg-gradient-to-b from-cbn-cream/40 via-cbn-cream/70 to-cbn-cream"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-8 sm:py-12 lg:py-16">
        <div class="grid lg:grid-cols-12 gap-8 lg:gap-14 items-center">
            {{-- Foto 1:1 --}}
            <div class="min-w-0 lg:col-span-6 lg:order-2">
                <div class="relative aspect-square w-full max-w-[560px] mx-auto rounded-2xl overflow-hidden shadow-2xl shadow-cbn-black/20 ring-1 ring-black/5 bg-neutral-200">
                    @foreach ($banners as $banner)
                        @php($first = $loop->first)
                        <div
                            @if ($isCarousel)
                                x-bind:class="{ 'opacity-100 z-10': active === {{ $loop->index }}, 'opacity-0 z-0': active !== {{ $loop->index }} }"
                                x-bind:aria-hidden="active !== {{ $loop->index }}"
                                @unless ($first) aria-hidden="true" @endunless
                            @endif
                            class="absolute inset-0 motion-safe:transition-opacity motion-safe:duration-700 {{ $first ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                        >
                            @if ($banner->image_url)
                                <img
                                    src="{{ $banner->image_url }}"
                                    @if ($banner->image_srcset) srcset="{{ $banner->image_srcset }}" @endif
                                    sizes="(min-width: 1024px) 560px, 100vw"
                                    width="{{ \App\Models\Banner::WIDTH }}"
                                    height="{{ \App\Models\Banner::HEIGHT }}"
                                    alt="{{ $banner->alt_text }}"
                                    loading="{{ $first ? 'eager' : 'lazy' }}"
                                    @if ($first) fetchpriority="high" @endif
                                    decoding="async"
                                    data-hero-image
                                    @if ($isCarousel)
                                        x-bind:class="{ 'motion-safe:scale-105': active !== {{ $loop->index }} }"
                                    @endif
                                    class="w-full h-full object-cover motion-safe:transition-transform motion-safe:duration-700"
                                />
                            @endif

                            @if (filled($banner->tag))
                                <span data-testid="hero-banner-tag" class="absolute top-4 left-4 inline-flex items-center rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-cbn-orange-ink shadow">
                                    {{ $banner->tag }}
                                </span>
                            @endif
                        </div>
                    @endforeach

                    @if ($isCarousel)
                        <span
                            data-testid="hero-banner-counter"
                            x-text="(active + 1) + ' / {{ $total }}'"
                            class="absolute bottom-4 right-4 z-20 rounded-full bg-cbn-black/70 text-white text-xs font-semibold px-3 py-1 tabular-nums"
                            aria-hidden="true"
                        >1 / {{ $total }}</span>
                    @endif
                </div>
            </div>

            {{-- Texto do aviso e navegação --}}
            <div class="min-w-0 lg:col-span-6 lg:order-1">
                <div class="inline-flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Avisos da Convenção</span>
                </div>

                {{-- Os avisos ficam empilhados na mesma célula: a altura acompanha o maior texto, sem espaço fixo --}}
                <div class="grid">
                    @foreach ($banners as $banner)
                        @php($first = $loop->first)
                        <div
                            @if ($isCarousel)
                                role="group"
                                aria-roledescription="slide"
                                aria-label="{{ $loop->iteration }} de {{ $total }}: {{ $banner->title }}"
                                x-bind:class="{ 'opacity-100 translate-y-0': active === {{ $loop->index }}, 'opacity-0 translate-y-3 pointer-events-none': active !== {{ $loop->index }} }"
                                x-bind:aria-hidden="active !== {{ $loop->index }}"
                                x-bind:inert="active !== {{ $loop->index }}"
                                @unless ($first) aria-hidden="true" inert @endunless
                            @endif
                            class="[grid-area:1/1] motion-safe:transition-all motion-safe:duration-500 {{ $first ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                        >
                            <h2 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl leading-[1.1] tracking-tight text-cbn-black text-balance">
                                {{ $banner->title }}
                            </h2>

                            @if (filled($banner->subtitle))
                                <p data-hero-subtitle class="mt-4 text-base sm:text-lg text-neutral-700 leading-relaxed max-w-xl text-balance">
                                    {{ $banner->subtitle }}
                                </p>
                            @endif

                            @if ($banner->link || filled($banner->meta))
                                <div class="mt-7 flex flex-wrap items-center gap-x-4 gap-y-3">
                                    @if ($banner->link)
                                        <a
                                            href="{{ $banner->link }}"
                                            @if ($banner->opens_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                            data-hero-cta
                                            class="inline-flex items-center gap-2 min-h-11 rounded-lg bg-cbn-orange-ink hover:bg-cbn-black text-white font-semibold px-6 py-3 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange focus-visible:ring-offset-2"
                                        >
                                            {{ $banner->button_label }}
                                            @if ($banner->opens_new_tab)
                                                <span class="sr-only">(abre em nova aba)</span>
                                            @endif
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                        </a>
                                    @endif

                                    @if (filled($banner->meta))
                                        <span data-hero-meta class="text-sm text-neutral-500">{{ $banner->meta }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($isCarousel)
                    <div data-testid="hero-banner-controls" class="mt-8 lg:mt-10 flex items-center gap-3">
                        <ol class="flex gap-3 flex-1 min-w-0 overflow-x-auto p-1 -m-1">
                            @foreach ($banners as $banner)
                                <li class="shrink-0">
                                    <button
                                        type="button"
                                        x-on:click="goTo({{ $loop->index }})"
                                        x-bind:aria-current="active === {{ $loop->index }} ? 'true' : 'false'"
                                        @if ($loop->first) aria-current="true" @endif
                                        aria-label="Ir para o aviso {{ $loop->iteration }}: {{ $banner->title }}"
                                        class="group block w-16 sm:w-20 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange focus-visible:ring-offset-2"
                                    >
                                        <span
                                            x-bind:class="{ 'ring-cbn-orange': active === {{ $loop->index }}, 'ring-transparent opacity-60 group-hover:opacity-100': active !== {{ $loop->index }} }"
                                            class="block aspect-square rounded-lg overflow-hidden ring-2 bg-neutral-200 transition {{ $loop->first ? 'ring-cbn-orange' : 'ring-transparent opacity-60 group-hover:opacity-100' }}"
                                        >
                                            @if ($banner->thumb_url)
                                                <img src="{{ $banner->thumb_url }}" alt="" width="160" height="160" loading="lazy" decoding="async" class="w-full h-full object-cover" />
                                            @endif
                                        </span>
                                        <span class="mt-2 block h-1 rounded-full bg-neutral-300 overflow-hidden">
                                            <span
                                                class="block h-full bg-cbn-orange {{ $loop->first ? 'w-full' : 'w-0' }}"
                                                x-bind:style="'width: ' + progressFor({{ $loop->index }}) + '%'"
                                            ></span>
                                        </span>
                                    </button>
                                </li>
                            @endforeach
                        </ol>

                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                type="button"
                                x-on:click="togglePause()"
                                x-bind:aria-label="paused ? 'Retomar rotação dos avisos' : 'Pausar rotação dos avisos'"
                                aria-label="Pausar rotação dos avisos"
                                class="w-11 h-11 rounded-full border border-neutral-300 bg-white flex items-center justify-center text-cbn-black hover:border-cbn-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                            >
                                <svg x-show="!paused" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
                                <svg x-show="paused" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            </button>
                            <button
                                type="button"
                                x-on:click="prev()"
                                aria-label="Aviso anterior"
                                class="hidden sm:flex w-11 h-11 rounded-full border border-neutral-300 bg-white items-center justify-center text-cbn-black hover:border-cbn-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            </button>
                            <button
                                type="button"
                                x-on:click="next()"
                                aria-label="Próximo aviso"
                                class="hidden sm:flex w-11 h-11 rounded-full bg-cbn-black text-white items-center justify-center hover:bg-cbn-orange-ink transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange focus-visible:ring-offset-2"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        </div>
                    </div>

                    <p class="sr-only" aria-live="off" x-bind:aria-live="running ? 'off' : 'polite'" x-text="'Aviso ' + (active + 1) + ' de {{ $total }}'"></p>
                @endif
            </div>
        </div>
    </div>
</section>
