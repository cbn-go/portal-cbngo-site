@extends('layouts.app')

@section('title', $project->title)
@section('meta_description', $project->summary)
@section('og_type', 'article')
@section('og_image', $project->cover_image_url ?? '')
@section('published_time', $project->published_at?->toIso8601String() ?? '')

@push('structured-data')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::socialProject($project)) !!}</script>
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::breadcrumbs([
        ['name' => 'Início', 'url' => route('home')],
        ['name' => 'Projetos Sociais', 'url' => route('projetos-sociais.index')],
        ['name' => $project->title, 'url' => route('projetos-sociais.show', $project->slug)],
    ])) !!}</script>
@endpush

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
                    <a href="{{ route('projetos-sociais.index') }}" class="hover:text-cbn-orange-ink transition">Projetos Sociais</a>
                </li>
                <li aria-hidden="true">&rsaquo;</li>
                <li class="text-neutral-800 font-medium truncate max-w-xs sm:max-w-sm" aria-current="page">
                    {{ $project->title }}
                </li>
            </ol>
        </nav>

        {{-- Cabeçalho do Projeto --}}
        <header class="space-y-4 mb-8">
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('projetos-sociais.index', ['categoria' => $project->category->slug()]) }}"
                    class="text-xs font-bold tracking-wider uppercase px-3 py-1 rounded shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange {{ $project->category->badgeClasses() }}"
                >
                    {{ $project->category->value }}
                </a>
                @if ($project->is_ongoing)
                    <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                        Em andamento
                    </span>
                @else
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-600 bg-neutral-100 border border-neutral-200 px-2.5 py-1 rounded-full">
                        Encerrado
                    </span>
                @endif
            </div>

            <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black leading-tight tracking-tight">
                {{ $project->title }}
            </h1>

            <p class="font-brand-sans text-lg sm:text-xl text-neutral-600 leading-relaxed">
                {{ $project->summary }}
            </p>

            <div class="pt-4 border-t border-neutral-200/80 flex justify-end">
                <x-share-buttons :title="$project->title" copy-label="Copiar link do projeto" />
            </div>
        </header>

        {{-- Capa --}}
        <figure class="mb-10 rounded-lg overflow-hidden border border-neutral-200/80 bg-neutral-100 shadow-sm">
            <x-media-image
                :path="$project->cover_image"
                :alt="$project->title"
                variant="cover"
                fallback-variant="cover"
                loading="eager"
                fetchpriority="high"
                class="w-full h-auto max-h-[500px]"
            />
        </figure>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Descrição e galeria --}}
            <div class="lg:col-span-2 min-w-0">
                {{-- Conteúdo HTML do RichEditor do painel, sanitizado novamente no portal (XSS Guard) --}}
                <div class="prose max-w-none text-neutral-800 text-base sm:text-lg leading-relaxed font-brand-sans space-y-5">
                    {!! \App\Support\Html::sanitize($project->description) !!}
                </div>

                <x-gallery-lightbox
                    :images="$project->gallery_urls"
                    :title="$project->title"
                    description="Registros do projeto nas comunidades."
                />
            </div>

            {{-- Ficha e "Como ajudar" --}}
            <aside class="space-y-6">
                <section aria-labelledby="ficha-projeto" class="bg-white border border-neutral-200 rounded-lg p-6">
                    <h2 id="ficha-projeto" class="font-brand-serif font-bold text-lg text-cbn-black mb-4">Sobre o projeto</h2>
                    <dl class="space-y-3 text-sm">
                        @if ($project->church)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Igreja</dt>
                                <dd class="mt-0.5">
                                    <a href="{{ $project->church_directory_url }}" class="font-semibold text-cbn-orange-ink hover:text-cbn-orange-mid underline underline-offset-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">
                                        {{ $project->church->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Cidade</dt>
                            <dd class="mt-0.5 text-neutral-800">{{ $project->city }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Período</dt>
                            <dd class="mt-0.5 text-neutral-800">{{ $project->period_label }}</dd>
                        </div>
                        @if (filled($project->target_audience))
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Público atendido</dt>
                                <dd class="mt-0.5 text-neutral-800">{{ $project->target_audience }}</dd>
                            </div>
                        @endif
                        @if ($project->beneficiaries_count)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Beneficiados</dt>
                                <dd class="mt-0.5 text-neutral-800">{{ number_format($project->beneficiaries_count, 0, ',', '.') }} pessoas</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Fundamento</dt>
                            <dd class="mt-0.5 text-neutral-800">{{ $project->category->lausanneReference() }}</dd>
                        </div>
                    </dl>
                </section>

                <section id="como-ajudar" aria-labelledby="titulo-como-ajudar" class="bg-cbn-black text-white rounded-lg p-6 space-y-4">
                    <h2 id="titulo-como-ajudar" class="font-brand-serif font-bold text-xl">Como ajudar</h2>

                    @if (filled($project->how_to_help))
                        <p class="text-sm text-white/85 leading-relaxed whitespace-pre-line">{{ $project->how_to_help }}</p>
                    @else
                        <p class="text-sm text-white/85 leading-relaxed">Entre em contato com a igreja responsável para saber como participar.</p>
                    @endif

                    @if (filled($project->contact_name))
                        <p class="text-sm"><span class="text-white/70">Contato:</span> <strong>{{ $project->contact_name }}</strong></p>
                    @endif

                    <div class="flex flex-col gap-2">
                        @if ($project->whatsapp_url)
                            <a
                                href="{{ $project->whatsapp_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#1E8E4E] hover:bg-[#177a42] px-4 py-3 text-sm font-bold text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-cbn-black"
                            >
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
                                Falar no WhatsApp
                                <span class="sr-only">(abre em nova aba)</span>
                            </a>
                        @endif
                        @if ($project->safe_contact_email)
                            <a
                                href="mailto:{{ $project->safe_contact_email }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 hover:bg-white/10 px-4 py-3 text-sm font-semibold text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            >
                                {{ $project->safe_contact_email }}
                            </a>
                        @endif
                    </div>
                </section>
            </aside>
        </div>

        {{-- Rodapé com Voltar e Compartilhar --}}
        <footer class="mt-14 pt-8 border-t border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a
                href="{{ route('projetos-sociais.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-cbn-orange-ink hover:text-cbn-orange-mid transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar para todos os projetos</span>
            </a>

            <x-share-buttons :title="$project->title" label="Compartilhar projeto:" whatsapp-only />
        </footer>
    </div>
</article>

{{-- Outros projetos da mesma categoria --}}
@if ($relatedProjects->isNotEmpty())
    <aside class="bg-neutral-50/70 border-t border-neutral-200/80 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">
                        Outros projetos de {{ $project->category->value }}
                    </h2>
                    <p class="font-brand-sans text-neutral-600 text-sm mt-1">
                        {{ $project->category->description() }}
                    </p>
                </div>
                <a
                    href="{{ route('projetos-sociais.index', ['categoria' => $project->category->slug()]) }}"
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-cbn-orange-ink hover:text-cbn-orange-mid transition"
                >
                    Ver todos &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($relatedProjects as $related)
                    <x-project-card :project="$related" />
                @endforeach
            </div>
        </div>
    </aside>
@endif
@endsection
