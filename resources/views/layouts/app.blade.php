<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CBN Goiás') }} - @yield('title', 'Convenção Batista Nacional do Estado de Goiás')</title>

    <!-- Google Fonts Fallbacks -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Assets / Tailwind via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-800 antialiased selection:bg-cbn-gold selection:text-cbn-navy-dark">
    <!-- Skip Link Acessível (WCAG AA) -->
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-cbn-gold focus:text-cbn-navy-dark focus:font-bold focus:rounded-md focus:shadow-lg focus:outline-none"
    >
        Pular para o conteúdo principal
    </a>

    <!-- Barra de Avisos Urgentes (Opcional por página ou global) -->
    @hasSection('banner')
        @yield('banner')
    @endif

    <!-- Navbar Oficial -->
    <x-navbar />

    <!-- Conteúdo Principal -->
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <!-- Rodapé Oficial -->
    <x-footer />
</body>
</html>
