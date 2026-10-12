@props([
    'active' => null,
])

@php
    $activeKey = $active ?? match(true) {
        request()->routeIs('noticias.*') => 'noticias',
        request()->routeIs('artigos.*') => 'artigos',
        request()->routeIs('igrejas*') => 'igrejas',
        request()->routeIs('quem-somos*') => 'quem-somos',
        request()->routeIs('contato*') => 'contato',
        request()->routeIs('home') => 'home',
        default => null,
    };

    $navItems = [
        'home' => ['label' => 'Início', 'href' => route('home')],
        'quem-somos' => ['label' => 'Institucional', 'href' => route('quem-somos')],
        'igrejas' => ['label' => 'Igrejas', 'href' => route('igrejas')],
        'noticias' => ['label' => 'Notícias', 'href' => route('noticias.index')],
        'artigos' => ['label' => 'Artigos', 'href' => route('artigos.index')],
        'contato' => ['label' => 'Contato', 'href' => route('contato')],
    ];
@endphp

<header
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-neutral-200/80 shadow-sm"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex items-center justify-between h-20">
            {{-- Logo oficial em fundo claro --}}
            <a
                href="{{ route('home') }}"
                class="flex items-center shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded-sm py-1 group"
            >
                <img
                    src="{{ asset('images/brand/cbn-go.png') }}"
                    alt="CBN | GO — Convenção Batista Nacional Goiás"
                    width="180"
                    height="69"
                    class="h-12 sm:h-14 w-auto"
                />
            </a>

            <nav class="hidden lg:flex items-center gap-8 text-sm font-medium" aria-label="Navegação principal">
                @foreach ($navItems as $key => $item)
                    @php
                        $isActive = ($activeKey === $key);
                        $linkClasses = $isActive
                            ? 'text-cbn-black font-semibold border-b-2 border-cbn-black pb-1'
                            : 'text-neutral-600 hover:text-cbn-black transition-colors';
                    @endphp
                    <a
                        href="{{ $item['href'] }}"
                        class="{{ $linkClasses }} focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded-sm"
                        @if ($isActive) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="hidden sm:flex items-center gap-3">
                <a
                    href="{{ route('igrejas') }}"
                    class="hidden md:inline-flex items-center gap-1.5 text-sm font-medium text-neutral-800 hover:text-cbn-orange px-3 py-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded-sm"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 21v-8m0 0V5.5L7 9v4m5-4l5 3.5V13m-10 8h10"/>
                    </svg>
                    <span>Encontre acolhimento</span>
                </a>
                <x-button variant="primary" size="sm" href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer">
                    Área das Igrejas
                </x-button>
            </div>

            <div class="flex lg:hidden">
                <button
                    type="button"
                    id="cbn-mobile-menu-toggle"
                    :aria-expanded="open.toString()"
                    aria-expanded="false"
                    aria-controls="cbn-mobile-menu"
                    aria-label="Alternar menu de navegação"
                    @click="open = !open"
                    class="p-2 rounded-lg text-neutral-700 hover:text-cbn-black hover:bg-neutral-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange transition"
                >
                    <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        id="cbn-mobile-menu"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-neutral-200 bg-white px-4 pt-3 pb-6 space-y-2 shadow-lg"
    >
        <nav class="flex flex-col space-y-1" aria-label="Navegação mobile">
            @foreach ($navItems as $key => $item)
                @php
                    $isActive = ($activeKey === $key);
                    $linkClasses = $isActive
                        ? 'text-cbn-black font-semibold border-b-2 border-cbn-black pb-1 bg-neutral-50'
                        : 'text-neutral-700 hover:text-cbn-black hover:bg-neutral-50';
                @endphp
                <a
                    href="{{ $item['href'] }}"
                    class="px-3 py-2.5 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange transition {{ $linkClasses }}"
                    @if ($isActive) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="pt-4 border-t border-neutral-100 flex flex-col gap-2">
            <x-button variant="primary" size="md" href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer" class="w-full">
                Área das Igrejas
            </x-button>
        </div>
    </div>
</header>
