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
                <a href="mailto:{{ config('cbngo.contact.email') }}" class="hidden md:inline-flex items-center gap-1.5 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ config('cbngo.contact.email') }}
                </a>
                <a href="{{ route('contato') }}" class="hidden sm:inline hover:text-white transition-colors">Contato</a>
                <span class="hidden lg:block w-px h-3.5 bg-white/20" aria-hidden="true"></span>
                <div class="hidden lg:flex items-center gap-3">
                    @if (config('cbngo.social.instagram'))
                        <a href="{{ config('cbngo.social.instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram da CBN Goiás" class="hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zm0 4.8a5 5 0 100 10 5 5 0 000-10zm0 8.3a3.3 3.3 0 110-6.6 3.3 3.3 0 010 6.6zm5.2-9.7a1.2 1.2 0 100 2.4 1.2 1.2 0 000-2.4z"/></svg>
                        </a>
                    @endif
                    @if (config('cbngo.social.facebook'))
                        <a href="{{ config('cbngo.social.facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook da CBN Goiás" class="hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                        </a>
                    @endif
                    @if (config('cbngo.social.youtube'))
                        <a href="{{ config('cbngo.social.youtube') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube da CBN Goiás" class="hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 00.5 6.2 31 31 0 000 12a31 31 0 00.5 5.8 3 3 0 002.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 002.1-2.1A31 31 0 0024 12a31 31 0 00-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg>
                        </a>
                    @endif
                </div>
                <span class="hidden sm:block w-px h-3.5 bg-white/20" aria-hidden="true"></span>
                <a href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 font-semibold text-white hover:text-cbn-gold transition-colors">
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
