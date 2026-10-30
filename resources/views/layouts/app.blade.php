<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Seções curtas (@section('x', $valor)) chegam escapadas; decodificamos para o <x-seo> escapar uma única vez.
        $seoSection = fn (string $name): string => trim(html_entity_decode($__env->yieldContent($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    @endphp
    <x-seo
        :title="$seoSection('title')"
        :description="$seoSection('meta_description')"
        :type="$seoSection('og_type')"
        :image="$seoSection('og_image')"
        :robots="$seoSection('robots')"
        :published-time="$seoSection('published_time')"
        :author="$seoSection('author_name')"
    />

    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::organization()) !!}</script>
    @stack('structured-data')

    {{-- Isotipo (chama + globo) extraído do arquivo oficial CBN_ESTADUAIS.ai. --}}
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/brand/cbn-go-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Pré-carrega as fontes da marca para evitar troca tardia de fonte (CLS). --}}
    <link rel="preload" href="{{ Vite::asset('resources/assets/fonts/humanist-521.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/assets/fonts/baker-signet.ttf') }}" as="font" type="font/ttf" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-cbn-black antialiased bg-[#FFFDFB] selection:bg-cbn-orange-mid/15 selection:text-cbn-black">
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-cbn-orange-ink focus:text-white focus:font-bold focus:rounded-md focus:shadow-lg focus:outline-none"
    >
        Pular para o conteúdo principal
    </a>

    {{-- Barra utilitária: identidade, contato, redes sociais e acesso das igrejas --}}
    <div class="w-full bg-cbn-black text-white/75 text-xs px-4 sm:px-6">
        <div class="max-w-7xl mx-auto lg:px-6 h-9 flex items-center justify-between gap-4">
            <span class="inline-flex items-center gap-1.5 font-medium text-white truncate">
                <span class="w-1.5 h-1.5 rounded-full bg-cbn-orange shrink-0" aria-hidden="true"></span>
                Convenção Batista Nacional — Seção Estadual de Goiás
            </span>
            <div class="flex items-center gap-5 shrink-0">
                <a href="mailto:{{ $institutional->email() }}" class="hidden md:inline-flex items-center gap-1.5 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $institutional->email() }}
                </a>
                <a href="{{ route('contato') }}" class="hidden sm:inline hover:text-white transition-colors">Contato</a>
                <span class="hidden lg:block w-px h-3.5 bg-white/20" aria-hidden="true"></span>
                <div class="hidden lg:flex items-center gap-3">
                    @foreach ($institutional->socialLinks() as $network => $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }} da CBN Goiás" class="hover:text-white transition-colors">
                            <x-social-icon :network="$network" class="w-4 h-4" />
                        </a>
                    @endforeach
                </div>
                <span class="hidden sm:block w-px h-3.5 bg-white/20" aria-hidden="true"></span>
                <a href="{{ config('cbngo.panel_url', 'https://painel.cbngo.org.br') }}" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 font-semibold text-white hover:text-cbn-gold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 11-6 0 3 3 0 016 0zM4.5 20.1a7.5 7.5 0 0115 0"/></svg>
                    Área das Igrejas<span class="sr-only"> (abre em nova aba)</span>
                </a>
            </div>
        </div>
    </div>

    @hasSection('banner')
        @yield('banner')
    @endif

    <x-navbar :active="$activeNav ?? null" />

    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <x-footer />
</body>
</html>
