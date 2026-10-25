@props([
    'banners',
    'interval' => 7000,
    'firstHeading' => 'h1', // a página deve ter um único h1; o design system usa h2
])

@php
    $total = $banners->count();
    $isCarousel = $total > 1;
@endphp

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
    {{ $attributes->merge(['class' => 'relative h-[560px] lg:h-[640px] overflow-hidden bg-cbn-black border-b border-neutral-200']) }}
>
    @foreach ($banners as $banner)
        @php($first = $loop->first)
        <div
            @if ($isCarousel)
                role="group"
                aria-roledescription="slide"
                aria-label="{{ $loop->iteration }} de {{ $total }}"
                x-bind:class="{ 'opacity-100 z-10': active === {{ $loop->index }}, 'opacity-0 z-0': active !== {{ $loop->index }} }"
                x-bind:aria-hidden="active !== {{ $loop->index }}"
                x-bind:inert="active !== {{ $loop->index }}"
                @unless ($first) x-cloak aria-hidden="true" inert @endunless
            @endif
            class="absolute inset-0 motion-safe:transition-opacity motion-safe:duration-700 {{ $first ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
        >
            <x-media-image
                :path="$banner->image"
                :alt="$banner->alt_text"
                variant="cover"
                fallback-variant="cover"
                sizes="100vw"
                :loading="$first ? 'eager' : 'lazy'"
                :fetchpriority="$first ? 'high' : null"
                width="1600"
                height="667"
                decoding="async"
                data-hero-image
                style="object-position: {{ $banner->object_position }};"
                class="absolute inset-0"
            />

            <div class="absolute inset-0 bg-gradient-to-t from-cbn-black/90 via-cbn-black/60 to-cbn-black/20 lg:bg-gradient-to-r lg:from-cbn-black/85 lg:via-cbn-black/55 lg:to-cbn-black/10" aria-hidden="true"></div>

            <div class="relative h-full max-w-7xl mx-auto px-6 lg:px-12 flex flex-col justify-end lg:justify-center {{ $isCarousel ? 'pb-24' : 'pb-14' }} lg:pb-0">
                <div class="max-w-2xl">
                    @php($heading = $first ? $firstHeading : 'h2')
                    <{{ $heading }} class="font-brand-serif font-extrabold text-3xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-[1.1] text-balance">
                        {{ $banner->title }}
                    </{{ $heading }}>

                    @if (filled($banner->subtitle))
                        <p class="mt-4 lg:mt-6 font-brand-sans text-base sm:text-lg lg:text-xl text-white/85 leading-relaxed text-balance">
                            {{ $banner->subtitle }}
                        </p>
                    @endif

                    @if ($banner->link)
                        <div class="mt-8">
                            <x-button
                                variant="primary"
                                size="lg"
                                :href="$banner->link"
                                :target="$banner->opens_new_tab ? '_blank' : null"
                                :rel="$banner->opens_new_tab ? 'noopener noreferrer' : null"
                                class="w-full sm:w-auto focus-visible:ring-offset-cbn-black"
                            >
                                {{ $banner->link_text }}
                                @if ($banner->opens_new_tab)
                                    <span class="sr-only">(abre em nova aba)</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                @endif
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @if ($isCarousel)
        <div class="absolute inset-x-0 bottom-0 z-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 pb-6 lg:pb-8 flex items-center justify-between gap-6">
                <div class="flex items-center gap-2 flex-1 max-w-xs">
                    @foreach ($banners as $banner)
                        <button
                            type="button"
                            x-on:click="goTo({{ $loop->index }})"
                            x-bind:aria-current="active === {{ $loop->index }} ? 'true' : 'false'"
                            @if ($loop->first) aria-current="true" @endif
                            aria-label="Ir para o banner {{ $loop->iteration }}: {{ $banner->title }}"
                            class="group flex-1 py-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-white rounded"
                        >
                            <span class="block h-1 rounded-full bg-white/30 overflow-hidden">
                                <span
                                    class="block h-full bg-white {{ $loop->first ? 'w-full' : 'w-0' }}"
                                    x-bind:style="'width: ' + progressFor({{ $loop->index }}) + '%'"
                                ></span>
                            </span>
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-on:click="togglePause()"
                        x-bind:aria-label="paused ? 'Retomar rotação dos banners' : 'Pausar rotação dos banners'"
                        aria-label="Pausar rotação dos banners"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/25 text-white backdrop-blur-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    >
                        <svg x-show="!paused" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
                        <svg x-show="paused" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                    <button
                        type="button"
                        x-on:click="prev()"
                        aria-label="Banner anterior"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-cbn-black shadow transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-cbn-black"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    </button>
                    <button
                        type="button"
                        x-on:click="next()"
                        aria-label="Próximo banner"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-cbn-black shadow transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-cbn-black"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <p class="sr-only" aria-live="off" x-bind:aria-live="running ? 'off' : 'polite'" x-text="'Banner ' + (active + 1) + ' de {{ $total }}'"></p>
    @endif
</section>
