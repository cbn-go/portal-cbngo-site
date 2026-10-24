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

    {{-- Barra institucional superior (Stitch) --}}
    <div class="w-full bg-neutral-950 text-white/80 text-xs py-2.5 px-4 sm:px-6 border-b border-black/20">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 font-medium text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    Convenção Batista Nacional — Seção Estadual de Goiás
                </span>
                <span class="hidden md:inline text-white/40" aria-hidden="true">|</span>
                <span class="hidden md:inline text-white/70">Fé em ação · Impacto nas comunidades</span>
            </div>
            <div class="flex items-center gap-6 text-xs text-white/80">
                <span class="hidden sm:inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Goiânia, Goiás
                </span>
                <a href="mailto:{{ config('cbngo.contact.email') }}" class="inline-flex items-center gap-1.5 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ config('cbngo.contact.email') }}
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
