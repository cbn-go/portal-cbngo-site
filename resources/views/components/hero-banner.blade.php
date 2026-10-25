@props([
    'banners',
    'interval' => 7000,
])

@php
    $total = $banners->count();
    $isCarousel = $total > 1;
    // Largura máxima do quadro 16:9: limita a altura em telas muito largas sem cortar a arte.
    $frameWidth = 'max-w-[min(1600px,calc(78svh*16/9))]';
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
    {{ $attributes->merge(['class' => 'relative overflow-hidden bg-cbn-black border-b border-neutral-200']) }}
>
    <div class="relative">
        @foreach ($banners as $banner)
            @php($first = $loop->first)
            <div
                @if ($isCarousel)
                    role="group"
                    aria-roledescription="slide"
                    aria-label="{{ $loop->iteration }} de {{ $total }}: {{ $banner->title }}"
                    x-bind:class="{ 'opacity-100 z-10': active === {{ $loop->index }}, 'opacity-0 z-0': active !== {{ $loop->index }} }"
                    x-bind:aria-hidden="active !== {{ $loop->index }}"
                    x-bind:inert="active !== {{ $loop->index }}"
                    @unless ($first) x-cloak aria-hidden="true" inert @endunless
                @endif
                class="absolute inset-0 motion-safe:transition-opacity motion-safe:duration-700 {{ $first ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
            >
                {{-- Fundo: a própria arte desfocada preenche as laterais e as faixas de artes fora do 16:9 --}}
                @if ($banner->backdrop_url)
                    <div
                        class="absolute inset-0 bg-cover bg-center blur-2xl scale-110 opacity-50"
                        style="background-image: url('{{ $banner->backdrop_url }}');"
                        aria-hidden="true"
                    ></div>
                @endif

                <div class="absolute inset-y-0 inset-x-0 mx-auto w-full {{ $frameWidth }}">
                    @if ($banner->link)
                        <a
                            href="{{ $banner->link }}"
                            @if ($banner->opens_new_tab) target="_blank" rel="noopener noreferrer" @endif
                            class="block w-full h-full focus:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-cbn-orange"
                        >
                    @endif

                    <x-media-image
                        :path="$banner->image"
                        :alt="$banner->alt_text"
                        variant="cover"
                        fit="contain"
                        fallback-variant="cover"
                        sizes="(min-width: 1600px) 1600px, 100vw"
                        :loading="$first ? 'eager' : 'lazy'"
                        :fetchpriority="$first ? 'high' : null"
                        :width="\App\Models\Banner::WIDTH"
                        :height="\App\Models\Banner::HEIGHT"
                        decoding="async"
                        data-hero-image
                    />

                    @if ($banner->link)
                            @if ($banner->opens_new_tab)
                                <span class="sr-only">(abre em nova aba)</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Reserva a altura do quadro 16:9 (sem CLS) --}}
        <div class="mx-auto w-full aspect-video {{ $frameWidth }}" aria-hidden="true"></div>
    </div>

    @if ($isCarousel)
        <div data-testid="hero-banner-controls" class="relative z-20 border-t border-white/10">
            <div class="mx-auto w-full {{ $frameWidth }} px-4 sm:px-6 py-3 flex items-center justify-between gap-6">
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
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/25 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
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
