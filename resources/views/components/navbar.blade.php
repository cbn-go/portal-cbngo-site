@props([
    'active' => null,
])

@php
    // Item ativo da página (granular); os grupos do menu acendem quando um dos seus links está ativo.
    $activeKey = $active ?? match (true) {
        request()->routeIs('noticias.*') => 'noticias',
        request()->routeIs('artigos.*') || request()->routeIs('autores.*') => 'artigos',
        request()->routeIs('igrejas*') => 'igrejas',
        request()->routeIs('projetos-sociais.*') => 'projetos-sociais',
        request()->routeIs('quem-somos*') => 'quem-somos',
        request()->routeIs('estatuto.*') => 'estatuto',
        request()->routeIs('diretoria*') => 'diretoria',
        request()->routeIs('contato*') => 'contato',
        request()->routeIs('home') => 'home',
        default => null,
    };

    $groups = [
        'convencao' => [
            'label' => 'A Convenção',
            'links' => [
                'quem-somos' => ['label' => 'Quem somos', 'description' => 'História, missão, visão e documentos oficiais', 'href' => route('quem-somos'), 'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33A48.36 48.36 0 0012 9.75c-2.55 0-5.06.2-7.5.58V21M3 21h18'],
                'diretoria' => ['label' => 'Diretoria', 'description' => 'Diretoria executiva, conselhos e coordenadores', 'href' => route('diretoria'), 'icon' => 'M15 19.13a9.38 9.38 0 002.63.37 9.34 9.34 0 004.12-.95 4.13 4.13 0 00-7.53-2.49M15 19.13v-.01c0-1.11-.29-2.16-.79-3.07M15 19.13v.1A12.32 12.32 0 018.62 21c-2.33 0-4.51-.65-6.37-1.77v-.1a6.38 6.38 0 0111.96-3.07M12 6.38a3.38 3.38 0 11-6.75 0 3.38 3.38 0 016.75 0zm8.25 2.25a2.63 2.63 0 11-5.25 0 2.63 2.63 0 015.25 0z'],
                'contato' => ['label' => 'Fale com a secretaria', 'description' => 'Endereço, telefones e horário de atendimento', 'href' => route('contato'), 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.24a2.25 2.25 0 01-1.07 1.92l-7.5 4.61a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.92V6.75'],
            ],
        ],
        'igrejas' => [
            'label' => 'Igrejas',
            'links' => [
                'igrejas' => ['label' => 'Diretório de igrejas', 'description' => 'Endereços, horários de culto e contatos', 'href' => route('igrejas')],
                'igrejas-mapa' => ['label' => 'Presença em Goiás', 'description' => 'Igrejas por município no mapa do estado', 'href' => route('igrejas').'#mapa-goias'],
            ],
        ],
        'comunicacao' => [
            'label' => 'Notícias e Artigos',
            'links' => [
                'noticias' => ['label' => 'Notícias e comunicados', 'description' => 'Da convenção e das igrejas filiadas', 'href' => route('noticias.index')],
                'artigos' => ['label' => 'Artigos pastorais', 'description' => 'Reflexões e ensino de pastores e professores', 'href' => route('artigos.index')],
            ],
        ],
    ];

    $activeGroup = collect($groups)->search(fn (array $group) => array_key_exists((string) $activeKey, $group['links']));
    // Páginas sem item próprio no menu (ex.: /estatuto, aberto pelos documentos de Quem Somos) acendem o grupo delas.
    $activeGroup = $activeGroup ?: match ($activeKey) { 'estatuto' => 'convencao', default => false };
    $churchAreaUrl = config('cbngo.panel_url', 'https://painel.cbngo.org.br');
    $searchScopes = [
        'noticias.index' => 'Notícias',
        'artigos.index' => 'Artigos',
        'igrejas' => 'Igrejas',
        'projetos-sociais.index' => 'Projetos Sociais',
    ];
@endphp

<div
    x-data="{
        open: null,
        mobileOpen: false,
        searchOpen: false,
        accordion: @js($activeGroup ?: null),
        scrolled: false,
        toggle(key) { this.open = this.open === key ? null : key },
        openSearch() { this.open = null; this.mobileOpen = false; this.searchOpen = true; this.$nextTick(() => this.$refs.searchInput.focus()) },
        closeAll() { this.open = null; this.mobileOpen = false; this.searchOpen = false },
    }"
    x-init="scrolled = window.scrollY > 40; window.addEventListener('scroll', () => scrolled = window.scrollY > 40, { passive: true })"
    x-effect="document.body.classList.toggle('overflow-hidden', mobileOpen || searchOpen)"
    @keydown.escape.window="closeAll()"
    class="contents"
>
    <header
        class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-neutral-200/80 transition-shadow"
        :class="scrolled ? 'shadow-md' : 'shadow-sm'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="flex items-center justify-between gap-6 h-20 transition-[height]" :class="scrolled ? 'lg:h-16' : 'lg:h-20'">
                {{-- Logo oficial em fundo claro (leva à página inicial) --}}
                <a
                    href="{{ route('home') }}"
                    class="flex items-center shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded-sm py-1"
                    @if ($activeKey === 'home') aria-current="page" @endif
                >
                    <img
                        src="{{ asset('images/brand/cbn-go.png') }}"
                        alt="CBN | GO — Convenção Batista Nacional Goiás"
                        width="180"
                        height="74"
                        class="h-12 sm:h-14 w-auto transition-[height]"
                        :class="scrolled ? 'lg:h-11' : ''"
                    />
                </a>

                {{-- Menu desktop: grupos com painéis (abrem com mouse, clique ou teclado) --}}
                <nav class="hidden lg:block" aria-label="Navegação principal">
                    <ul class="flex items-center gap-1 text-[15px] font-medium text-neutral-700" @mouseleave="open = null">
                        @foreach ($groups as $groupKey => $group)
                            @php($isGroupActive = $activeGroup === $groupKey)
                            <li class="relative" @mouseenter="open = '{{ $groupKey }}'">
                                <button
                                    type="button"
                                    @click="toggle('{{ $groupKey }}')"
                                    :aria-expanded="(open === '{{ $groupKey }}').toString()"
                                    aria-expanded="false"
                                    aria-controls="painel-{{ $groupKey }}"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange {{ $isGroupActive ? 'text-cbn-black font-semibold' : 'hover:text-cbn-black hover:bg-neutral-100' }}"
                                    :class="open === '{{ $groupKey }}' && 'bg-neutral-100 text-cbn-black'"
                                >
                                    <span class="{{ $isGroupActive ? 'border-b-2 border-cbn-black' : '' }}">{{ $group['label'] }}</span>
                                    <svg class="w-4 h-4 transition-transform" :class="open === '{{ $groupKey }}' && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>

                                <div
                                    id="painel-{{ $groupKey }}"
                                    x-show="open === '{{ $groupKey }}'"
                                    x-cloak
                                    x-transition.opacity.duration.150ms
                                    @click.outside="if (open === '{{ $groupKey }}') open = null"
                                    class="absolute top-full pt-3 {{ $groupKey === 'comunicacao' ? 'right-0' : 'left-0' }} {{ $groupKey === 'convencao' ? 'w-[620px]' : 'w-[580px]' }}"
                                >
                                    <div class="rounded-xl border border-neutral-200 bg-white shadow-xl p-5 grid grid-cols-2 gap-5">
                                        <ul class="space-y-1">
                                            @foreach ($group['links'] as $linkKey => $link)
                                                <li>
                                                    <a
                                                        href="{{ $link['href'] }}"
                                                        class="flex gap-3 rounded-lg p-3 hover:bg-[#FFFDFB] focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange {{ $activeKey === $linkKey ? 'bg-[#FFFDFB] ring-1 ring-neutral-200' : '' }}"
                                                        @if ($activeKey === $linkKey) aria-current="page" @endif
                                                    >
                                                        @isset($link['icon'])
                                                            <span class="mt-0.5 w-9 h-9 rounded-lg bg-cbn-orange/10 text-cbn-orange-ink grid place-items-center shrink-0" aria-hidden="true">
                                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" /></svg>
                                                            </span>
                                                        @endisset
                                                        <span>
                                                            <strong class="block text-cbn-black font-semibold">{{ $link['label'] }}</strong>
                                                            <span class="text-sm text-neutral-500">{{ $link['description'] }}</span>
                                                        </span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        @if ($groupKey === 'convencao')
                                            <a href="{{ route('projetos-sociais.index') }}" class="rounded-lg bg-cbn-black text-white p-5 flex flex-col justify-between gap-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange">
                                                <span class="text-[11px] font-bold uppercase tracking-wider text-cbn-gold">Missão integral</span>
                                                <span>
                                                    <strong class="block font-brand-serif text-xl leading-tight">Projetos sociais das igrejas</strong>
                                                    <span class="block text-sm text-white/70 mt-1">Cuidado social, cultura, educação e liderança em Goiás.</span>
                                                </span>
                                                <span class="text-sm font-semibold text-cbn-gold">Conheça e ajude &rarr;</span>
                                            </a>
                                        @elseif ($groupKey === 'igrejas')
                                            <form method="GET" action="{{ route('igrejas') }}" class="rounded-lg bg-[#FFFDFB] border border-neutral-200 p-5 flex flex-col gap-3">
                                                <span class="text-[11px] font-bold uppercase tracking-wider text-cbn-orange-ink">Busca rápida</span>
                                                <label for="menu-busca-igreja" class="text-sm font-semibold text-cbn-black">Encontre uma igreja pelo nome ou bairro</label>
                                                <input id="menu-busca-igreja" type="search" name="q" placeholder="Ex.: Ebenézer, Setor Bueno" class="rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-800 placeholder-neutral-400 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange" />
                                                <button type="submit" class="rounded-md bg-cbn-black hover:bg-cbn-black/90 text-white text-sm font-semibold py-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange focus-visible:ring-offset-2">Buscar igrejas</button>
                                            </form>
                                        @elseif ($groupKey === 'comunicacao')
                                            <a href="{{ route('noticias.index', ['apenas_oficiais' => 1]) }}" class="rounded-lg bg-[#FFFDFB] border border-neutral-200 p-5 flex flex-col justify-between gap-4 hover:border-neutral-400 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange">
                                                <span class="text-[11px] font-bold uppercase tracking-wider text-cbn-orange-ink">Comunicação oficial</span>
                                                <span>
                                                    <strong class="block font-brand-serif text-xl leading-tight text-cbn-black">Comunicados da diretoria</strong>
                                                    <span class="block text-sm text-neutral-500 mt-1">Convocações, editais e avisos oficiais da convenção.</span>
                                                </span>
                                                <span class="text-sm font-semibold text-cbn-orange-ink">Ver comunicados &rarr;</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        <li>
                            <a
                                href="{{ route('projetos-sociais.index') }}"
                                class="inline-flex items-center px-3 py-2 rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange {{ $activeKey === 'projetos-sociais' ? 'text-cbn-black font-semibold' : 'hover:text-cbn-black hover:bg-neutral-100' }}"
                                @if ($activeKey === 'projetos-sociais') aria-current="page" @endif
                            >
                                <span class="{{ $activeKey === 'projetos-sociais' ? 'border-b-2 border-cbn-black' : '' }}">Projetos Sociais</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                {{-- Ações --}}
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="openSearch()"
                        aria-label="Buscar no portal"
                        aria-controls="cbn-busca"
                        class="w-10 h-10 grid place-items-center rounded-full text-neutral-700 hover:text-cbn-black hover:bg-neutral-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" /></svg>
                    </button>

                    <x-button variant="primary" size="sm" href="{{ route('igrejas') }}" class="hidden sm:inline-flex !py-2.5 !px-4 !text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        Encontre uma igreja
                    </x-button>

                    <button
                        type="button"
                        id="cbn-mobile-menu-toggle"
                        @click="mobileOpen = true"
                        :aria-expanded="mobileOpen.toString()"
                        aria-expanded="false"
                        aria-controls="cbn-mobile-menu"
                        aria-label="Abrir menu de navegação"
                        class="lg:hidden w-10 h-10 grid place-items-center rounded-full text-cbn-black hover:bg-neutral-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Busca: um campo, com a seção escolhida (cada listagem já filtra por ?q=) --}}
    <div
        id="cbn-busca"
        x-show="searchOpen"
        x-cloak
        x-transition.opacity
        @click.self="searchOpen = false"
        role="dialog"
        aria-modal="true"
        aria-label="Buscar no portal"
        class="fixed inset-0 z-[70] bg-cbn-black/60 backdrop-blur-sm px-4"
    >
        <div
            class="max-w-2xl mx-auto mt-20 sm:mt-28 rounded-2xl bg-white shadow-2xl p-5 sm:p-6"
            x-data="{ scope: 'noticias.index', actions: @js(collect($searchScopes)->keys()->mapWithKeys(fn ($routeName) => [$routeName => route($routeName)])) }"
        >
            <form id="cbn-busca-form" method="GET" :action="actions[scope]" action="{{ route('noticias.index') }}">
                <div class="flex items-center gap-3 border-b border-neutral-200 pb-4">
                    <svg class="w-6 h-6 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" /></svg>
                    <label for="busca-portal" class="sr-only">Termo de busca</label>
                    <input x-ref="searchInput" id="busca-portal" type="search" name="q" required placeholder="O que você procura?" class="flex-1 min-w-0 text-lg text-cbn-black placeholder-neutral-400 border-0 p-0 focus:ring-0 focus:outline-none" />
                    <button type="button" @click="searchOpen = false" class="text-xs font-semibold text-neutral-500 border border-neutral-300 rounded px-2 py-1 hover:bg-neutral-50">Esc</button>
                </div>
            </form>

            {{-- A seção escolhida só define o destino do formulário; fica fora dele para não ir na URL --}}
            <fieldset class="mt-4">
                    <legend class="text-xs font-bold uppercase tracking-wider text-neutral-500">Buscar em</legend>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($searchScopes as $routeName => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="busca_secao" value="{{ $routeName }}" x-model="scope" class="peer sr-only" @checked($loop->first) />
                                <span class="inline-block px-3.5 py-1.5 rounded-full border border-neutral-300 text-sm font-medium text-neutral-700 peer-checked:bg-cbn-black peer-checked:text-white peer-checked:border-cbn-black peer-focus-visible:ring-2 peer-focus-visible:ring-cbn-orange">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
            </fieldset>

            <div class="mt-5 flex justify-end">
                <x-button variant="primary" size="md" type="submit" form="cbn-busca-form">Buscar</x-button>
            </div>
        </div>
    </div>

    {{-- Menu mobile: gaveta lateral com grupos em acordeão --}}
    <div
        id="cbn-mobile-menu"
        x-show="mobileOpen"
        x-cloak
        class="fixed inset-0 z-[70] lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Menu de navegação"
    >
        <div class="absolute inset-0 bg-cbn-black/60" @click="mobileOpen = false" x-show="mobileOpen" x-transition.opacity></div>

        <aside
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 w-full max-w-sm bg-white flex flex-col shadow-2xl"
        >
            <div class="h-16 px-5 flex items-center justify-between border-b border-neutral-200 shrink-0">
                <img src="{{ asset('images/brand/cbn-go.png') }}" alt="CBN | GO" width="120" height="49" class="h-9 w-auto" />
                <button type="button" @click="mobileOpen = false" aria-label="Fechar menu" class="w-10 h-10 grid place-items-center rounded-full hover:bg-neutral-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-5 py-2 text-cbn-black" aria-label="Navegação mobile">
                <a href="{{ route('home') }}" class="block py-4 font-semibold text-lg border-b border-neutral-200 {{ $activeKey === 'home' ? 'text-cbn-orange-ink' : '' }}" @if ($activeKey === 'home') aria-current="page" @endif>Início</a>

                @foreach ($groups as $groupKey => $group)
                    <div class="border-b border-neutral-200">
                        <button
                            type="button"
                            @click="accordion = accordion === '{{ $groupKey }}' ? null : '{{ $groupKey }}'"
                            :aria-expanded="(accordion === '{{ $groupKey }}').toString()"
                            aria-controls="gaveta-{{ $groupKey }}"
                            class="w-full flex items-center justify-between py-4 font-semibold text-lg text-left"
                        >
                            {{ $group['label'] }}
                            <svg class="w-5 h-5 transition-transform" :class="accordion === '{{ $groupKey }}' && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                        <ul id="gaveta-{{ $groupKey }}" x-show="accordion === '{{ $groupKey }}'" x-cloak class="pb-4 space-y-1">
                            @foreach ($group['links'] as $linkKey => $link)
                                <li>
                                    <a
                                        href="{{ $link['href'] }}"
                                        class="block rounded-md px-3 py-2.5 {{ $activeKey === $linkKey ? 'bg-[#FFFDFB] font-semibold text-cbn-black' : 'text-neutral-700 hover:bg-[#FFFDFB]' }}"
                                        @if ($activeKey === $linkKey) aria-current="page" @endif
                                    >{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <a href="{{ route('projetos-sociais.index') }}" class="block py-4 font-semibold text-lg border-b border-neutral-200 {{ $activeKey === 'projetos-sociais' ? 'text-cbn-orange-ink' : '' }}" @if ($activeKey === 'projetos-sociais') aria-current="page" @endif>Projetos Sociais</a>
            </nav>

            <div class="p-5 border-t border-neutral-200 space-y-3 bg-[#FFFDFB] shrink-0">
                <x-button variant="primary" size="md" href="{{ route('igrejas') }}" class="w-full">Encontre uma igreja</x-button>
                <x-button variant="outline-dark" size="md" href="{{ $churchAreaUrl }}" target="_blank" rel="noopener noreferrer" class="w-full">Área das Igrejas</x-button>
            </div>
        </aside>
    </div>
</div>
