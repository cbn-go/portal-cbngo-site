@props([
    'active' => 'home',
])

<header class="sticky top-0 z-40 bg-cbn-navy text-white border-b border-cbn-gold/30 shadow-md">
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
                    <span class="text-[11px] font-medium text-slate-300 tracking-wider uppercase mt-1 leading-none">
                        Convenção Batista Nacional de Goiás
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-1 xl:gap-2 text-sm font-medium" aria-label="Navegação principal">
                <a
                    href="{{ route('home') }}"
                    class="px-3 py-2 rounded-md transition {{ $active === 'home' ? 'text-cbn-gold font-semibold bg-white/5' : 'text-slate-200 hover:text-white hover:bg-white/5' }}"
                >
                    Início
                </a>
                <a href="#quem-somos" class="px-3 py-2 rounded-md text-slate-200 hover:text-white hover:bg-white/5 transition">
                    Quem Somos
                </a>
                <a href="#diretoria" class="px-3 py-2 rounded-md text-slate-200 hover:text-white hover:bg-white/5 transition">
                    Diretoria
                </a>
                <a href="#igrejas" class="px-3 py-2 rounded-md text-slate-200 hover:text-white hover:bg-white/5 transition">
                    Igrejas
                </a>
                <a href="#noticias" class="px-3 py-2 rounded-md text-slate-200 hover:text-white hover:bg-white/5 transition">
                    Notícias
                </a>
                <a href="#artigos" class="px-3 py-2 rounded-md text-slate-200 hover:text-white hover:bg-white/5 transition">
                    Artigos
                </a>
                <a href="#contato" class="px-3 py-2 rounded-md text-slate-200 hover:text-white hover:bg-white/5 transition">
                    Contato
                </a>
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

            <!-- Mobile Hamburger Button -->
            <div class="flex lg:hidden">
                <button
                    type="button"
                    id="cbn-mobile-menu-toggle"
                    aria-expanded="false"
                    aria-controls="cbn-mobile-menu"
                    aria-label="Alternar menu de navegação"
                    onclick="
                        const menu = document.getElementById('cbn-mobile-menu');
                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                        this.setAttribute('aria-expanded', !isExpanded);
                        menu.classList.toggle('hidden');
                    "
                    class="p-2 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold transition"
                >
                    <!-- Icon Menu -->
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div
        id="cbn-mobile-menu"
        class="hidden lg:hidden border-t border-white/10 bg-cbn-navy-dark px-4 pt-3 pb-6 space-y-2 shadow-2xl"
    >
        <nav class="flex flex-col space-y-1" aria-label="Navegação mobile">
            <a href="{{ route('home') }}" class="px-3 py-2.5 rounded-md font-semibold text-cbn-gold bg-white/5">
                Início
            </a>
            <a href="#quem-somos" class="px-3 py-2.5 rounded-md text-slate-200 hover:text-white hover:bg-white/5">
                Quem Somos
            </a>
            <a href="#diretoria" class="px-3 py-2.5 rounded-md text-slate-200 hover:text-white hover:bg-white/5">
                Diretoria
            </a>
            <a href="#igrejas" class="px-3 py-2.5 rounded-md text-slate-200 hover:text-white hover:bg-white/5">
                Igrejas
            </a>
            <a href="#noticias" class="px-3 py-2.5 rounded-md text-slate-200 hover:text-white hover:bg-white/5">
                Notícias
            </a>
            <a href="#artigos" class="px-3 py-2.5 rounded-md text-slate-200 hover:text-white hover:bg-white/5">
                Artigos
            </a>
            <a href="#contato" class="px-3 py-2.5 rounded-md text-slate-200 hover:text-white hover:bg-white/5">
                Contato
            </a>
        </nav>

        <div class="pt-4 border-t border-white/10 flex flex-col gap-2">
            <x-button variant="gold" size="md" href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer" class="w-full">
                Área das Igrejas
            </x-button>
        </div>
    </div>
</header>
