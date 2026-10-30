@extends('layouts.app')

@section('title', $church->getSeoTitle())
@section('meta_description', $church->getSeoDescription())
@section('og_image', \App\Support\Media::shareImage($church->logo) ?? '')

@push('structured-data')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::church($church)) !!}</script>
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::breadcrumbs([
        ['name' => 'Início', 'url' => route('home')],
        ['name' => 'Igrejas', 'url' => route('igrejas')],
        ['name' => $church->name, 'url' => route('igrejas.show', $church->slug)],
    ])) !!}</script>
@endpush

@php
    $safeSocials = $church->getSafeSocialLinks();
    $safeEmail = $church->getSafeEmail();
    $serviceTimes = array_values(array_filter(is_array($church->service_times) ? $church->service_times : [], 'is_array'));
    $phones = array_values(array_filter([$church->phone, $church->cellphone], fn ($phone) => filled($phone)));
@endphp

@section('content')
<article class="bg-[#FFFDFB] py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb acessível --}}
        <nav aria-label="Breadcrumb" class="mb-8">
            <ol class="flex items-center gap-2 text-xs sm:text-sm text-neutral-500 flex-wrap">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-cbn-orange-ink transition">Início</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li>
                    <a href="{{ route('igrejas') }}" class="hover:text-cbn-orange-ink transition">Igrejas</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li class="text-neutral-800 font-medium truncate max-w-xs sm:max-w-sm" aria-current="page">
                    {{ $church->name }}
                </li>
            </ol>
        </nav>

        {{-- Cabeçalho da Igreja --}}
        <header class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-6 pb-8 mb-8 border-b border-neutral-200/80">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden shrink-0 border border-neutral-200 bg-neutral-50">
                <x-media-image
                    :path="$church->logo"
                    :alt="'Logotipo ' . $church->name"
                    variant="thumb"
                    fallback-variant="logo"
                    loading="eager"
                    class="w-full h-full object-cover"
                />
            </div>

            <div class="min-w-0 space-y-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-800">
                    <svg class="w-3 h-3 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $church->city }} · {{ $church->state }}
                </span>

                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl text-cbn-black leading-tight tracking-tight">
                    {{ $church->name }}
                </h1>

                @if (filled($church->pastor_name))
                    <p class="text-sm sm:text-base text-neutral-600">
                        <span class="font-semibold text-neutral-800">Pastor Titular:</span>
                        {{ $church->pastor_name }}
                    </p>
                @endif
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
            {{-- Endereço e horários --}}
            <div class="lg:col-span-2 min-w-0 space-y-8">
                <section aria-labelledby="titulo-endereco" class="bg-white border border-neutral-200 rounded-lg p-6">
                    <h2 id="titulo-endereco" class="font-brand-serif font-bold text-xl text-cbn-black mb-4">Endereço</h2>
                    <address class="not-italic text-neutral-800 leading-relaxed">
                        @if ($church->getStreetAddress() !== '')
                            <span class="block font-medium text-cbn-black">{{ $church->getStreetAddress() }}</span>
                        @endif
                        <span class="block">
                            @if (filled($church->neighborhood)){{ $church->neighborhood }} — @endif{{ $church->city }}/{{ $church->state }}
                        </span>
                        @if (filled($church->zip_code))
                            <span class="block text-neutral-600">CEP {{ $church->zip_code }}</span>
                        @endif
                    </address>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a
                            href="{{ $church->getGoogleMapsUrl() }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-neutral-300 bg-white text-sm font-semibold text-neutral-800 hover:border-cbn-orange hover:text-cbn-orange-ink transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            <svg class="w-4 h-4 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span>Como chegar (Google Maps)</span>
                            <span class="sr-only">(abre em nova aba)</span>
                        </a>
                        <a
                            href="{{ $church->getWazeUrl() }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-neutral-300 bg-white text-sm font-semibold text-neutral-800 hover:border-cbn-orange hover:text-cbn-orange-ink transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            <svg class="w-4 h-4 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>Waze</span>
                            <span class="sr-only">(abre em nova aba)</span>
                        </a>
                    </div>
                </section>

                @if (count($serviceTimes) > 0)
                    <section aria-labelledby="titulo-cultos" class="bg-white border border-neutral-200 rounded-lg p-6">
                        <h2 id="titulo-cultos" class="font-brand-serif font-bold text-xl text-cbn-black mb-4">Cultos e reuniões</h2>
                        <ul class="divide-y divide-neutral-100">
                            @foreach ($serviceTimes as $schedule)
                                <li class="py-3 flex items-center justify-between gap-3 text-sm">
                                    <span class="font-medium text-neutral-800">{{ $schedule['label'] ?? 'Culto Regular' }}</span>
                                    <span class="shrink-0 font-semibold text-cbn-black bg-neutral-100 px-2.5 py-1 rounded">
                                        {{ $schedule['day'] ?? '' }} · {{ $schedule['time'] ?? '' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>

            {{-- Contatos --}}
            <aside class="space-y-6">
                @if (count($phones) > 0 || $safeEmail !== null || count($safeSocials) > 0)
                    <section aria-labelledby="titulo-contato" class="bg-white border border-neutral-200 rounded-lg p-6">
                        <h2 id="titulo-contato" class="font-brand-serif font-bold text-lg text-cbn-black mb-4">Contato</h2>
                        <ul class="space-y-2.5 text-sm">
                            @foreach ($phones as $phone)
                                <li>
                                    @if (\App\Support\ContactLinks::tel($phone))
                                        <a href="{{ \App\Support\ContactLinks::tel($phone) }}" class="font-medium text-neutral-800 hover:text-cbn-orange-ink underline decoration-neutral-300 hover:decoration-cbn-orange transition">{{ $phone }}</a>
                                    @else
                                        <span class="font-medium text-neutral-800">{{ $phone }}</span>
                                    @endif
                                </li>
                            @endforeach
                            @if ($safeEmail !== null)
                                <li class="min-w-0">
                                    <a href="mailto:{{ $safeEmail }}" class="text-neutral-700 hover:text-cbn-orange-ink underline decoration-neutral-300 hover:decoration-cbn-orange transition break-all">{{ $safeEmail }}</a>
                                </li>
                            @endif
                        </ul>

                        @if (count($safeSocials) > 0)
                            <div class="mt-4 pt-4 border-t border-neutral-200/80 flex flex-wrap gap-2">
                                @foreach ($safeSocials as $network => $url)
                                    <a
                                        href="{{ $url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-neutral-100 hover:bg-cbn-orange-ink hover:text-white text-neutral-700 text-xs font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                                    >
                                        {{ ucfirst($network) }}
                                        <span class="sr-only">(abre em nova aba)</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                <a
                    href="{{ route('igrejas', ['cidade' => $church->city]) }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-cbn-orange-ink hover:text-cbn-orange-mid transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
                >
                    Ver outras igrejas em {{ $church->city }} &rarr;
                </a>
            </aside>
        </div>
    </div>
</article>

@if ($projects->isNotEmpty())
    <section aria-labelledby="titulo-projetos-igreja" class="bg-neutral-50/70 border-t border-neutral-200/80 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <h2 id="titulo-projetos-igreja" class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black mb-8">
                Projetos sociais desta igreja
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        </div>
    </section>
@endif

@if ($news->isNotEmpty())
    <section aria-labelledby="titulo-noticias-igreja" class="bg-white border-t border-neutral-200/80 py-12 sm:py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="titulo-noticias-igreja" class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black mb-6">
                Notícias desta igreja
            </h2>
            <ul class="divide-y divide-neutral-200 border-y border-neutral-200">
                @foreach ($news as $item)
                    <li class="py-4">
                        <a href="{{ route('noticias.show', $item->slug) }}" class="group block focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-neutral-500">
                                {{ $item->published_at?->translatedFormat('d \d\e F \d\e Y') }}
                            </span>
                            <span class="block mt-1 font-brand-serif font-bold text-lg text-cbn-black group-hover:text-cbn-orange-ink transition">
                                {{ $item->title }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
@endsection
