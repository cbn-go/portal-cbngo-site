<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CBN Goiás') }} - @yield('title', 'Convenção Batista Nacional do Estado de Goiás')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Assets / Tailwind via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white">
    <header class="bg-[#0D2240] text-white border-b border-amber-600/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-lg bg-amber-500/20 border border-amber-400/30 flex items-center justify-center font-bold text-amber-400 text-lg">
                    CBN
                </div>
                <div>
                    <span class="block text-lg font-bold tracking-tight text-white group-hover:text-amber-300 transition">CBN-GO</span>
                    <span class="block text-xs text-slate-300">Convenção Batista Nacional do Estado de Goiás</span>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}" class="text-amber-400 hover:text-amber-300 transition">Início</a>
                <a href="#sobre" class="text-slate-300 hover:text-white transition">Quem Somos</a>
                <a href="#igrejas" class="text-slate-300 hover:text-white transition">Igrejas</a>
                <a href="#noticias" class="text-slate-300 hover:text-white transition">Notícias</a>
                <a href="#contato" class="text-slate-300 hover:text-white transition">Contato</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-[#09172c] text-slate-400 border-t border-slate-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p class="font-semibold text-slate-200">Convenção Batista Nacional do Estado de Goiás (CBN-GO)</p>
            <p class="mt-1 text-xs text-slate-500">&copy; {{ date('Y') }} Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
