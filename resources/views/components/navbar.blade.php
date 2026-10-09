@props([
    'active' => 'home',
])

@php
    $navItems = [
        'home' => ['label' => 'Início', 'href' => route('home')],
        'quem-somos' => ['label' => 'Quem Somos', 'href' => '#quem-somos'],
        'diretoria' => ['label' => 'Diretoria', 'href' => '#diretoria'],
        'igrejas' => ['label' => 'Igrejas', 'href' => '#igrejas'],
        'noticias' => ['label' => 'Notícias', 'href' => '#noticias'],
        'artigos' => ['label' => 'Artigos', 'href' => '#artigos'],
        'contato' => ['label' => 'Contato', 'href' => '#contato'],
    ];
@endphp

<header
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    class="sticky top-0 z-40 bg-cbn-navy text-white border-b border-cbn-gold/30 shadow-md"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand / Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded-lg p-1">
                <div class="w-11 h-11 rounded-lg bg-cbn-gold/20 border border-cbn-gold/40 flex items-center justify-center font-bold text-cbn-gold font-brand-serif text-2xl shadow-inner group-hover:scale-105 transition-transform">
                    CBN
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-white group-hover:text-cbn-gold-light transition font-brand-serif leading-none">
                        CBN-GO
                    </span>
                    <span class="text-[11px] font-medium text-slate-300 tracking-wider uppercase mt-1 leading-none font-brand-sans">
                        Convenção Batista Nacional de Goiás
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-1 xl:gap-2 text-sm font-medium" aria-label="Navegação principal">
                @foreach ($navItems as $key => $item)
                    @php
                        $isActive = ($active === $key);
                        $linkClasses = $isActive
                            ? 'text-cbn-gold font-semibold bg-white/5 shadow-inner'
                            : 'text-slate-200 hover:text-white hover:bg-white/5';
                    @endphp
                    <a
                        href="{{ $item['href'] }}"
                        class="px-3 py-2 rounded-md transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold {{ $linkClasses }}"
                        @if ($isActive) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <!-- Actions / Portal Admin CTA -->
            <div class="hidden sm:flex items-center gap-3">
                <x-button variant="gold" size="sm" href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Área das Igrejas
                </x-button>
            </div>

            <!-- Mobile Hamburger & Close Button -->
            <div class="flex lg:hidden">
                <button
                    type="button"
                    id="cbn-mobile-menu-toggle"
                    :aria-expanded="open.toString()"
                    aria-expanded="false"
                    aria-controls="cbn-mobile-menu"
                    aria-label="Alternar menu de navegação"
                    @click="open = !open"
                    class="p-2 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold transition"
                >
                    <!-- Icon Hamburger (visible by default; Alpine hides when open) -->
                    <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!-- Icon Close X (hidden until Alpine opens the menu) -->
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu (visibility via Alpine x-show only — do not use Tailwind `hidden` here) -->
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
        class="lg:hidden border-t border-white/10 bg-cbn-navy-dark px-4 pt-3 pb-6 space-y-2 shadow-2xl"
    >
        <nav class="flex flex-col space-y-1" aria-label="Navegação mobile">
            @foreach ($navItems as $key => $item)
                @php
                    $isActive = ($active === $key);
                    $linkClasses = $isActive
                        ? 'text-cbn-gold font-semibold bg-white/5'
                        : 'text-slate-200 hover:text-white hover:bg-white/5';
                @endphp
                <a
                    href="{{ $item['href'] }}"
                    class="px-3 py-2.5 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold transition {{ $linkClasses }}"
                    @if ($isActive) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="pt-4 border-t border-white/10 flex flex-col gap-2">
            <x-button variant="gold" size="md" href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer" class="w-full">
                Área das Igrejas
            </x-button>
        </div>
    </div>
</header>
