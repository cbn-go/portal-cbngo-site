@extends('layouts.app')


@if ($urgentNotice)
    @section('banner')
        <x-urgent-banner
            :message="$urgentNotice->message"
            :link="$urgentNotice->link"
            :link-text="$urgentNotice->link_text ?? 'Saiba mais'"
            :type="$urgentNotice->type ?? 'warning'"
        />
    @endsection
@endif

@section('content')
@if ($banners->isNotEmpty())
{{-- Hero com as artes dos banners cadastrados no painel (Issues #23 e #24); a arte não tem título visível --}}
<h1 class="sr-only">{{ config('cbngo.organization.name') }}</h1>
<x-hero-banner :banners="$banners" />
@else
{{-- Hero institucional (layout Stitch — fundo claro), fallback sem banners vigentes --}}
<section class="relative min-h-[560px] lg:min-h-[640px] flex items-center justify-center overflow-hidden border-b border-neutral-200">
    @php($heroImage = 'https://images.unsplash.com/photo-1438032005730-c779502df39b?auto=format&fit=crop&q=70&w=')
    <img
        src="{{ $heroImage }}1280"
        srcset="{{ $heroImage }}640 640w, {{ $heroImage }}1280 1280w, {{ $heroImage }}1920 1920w"
        sizes="100vw"
        width="1920"
        height="1280"
        alt="Comunidade reunida em solidariedade e cuidado"
        fetchpriority="high"
        decoding="async"
        class="absolute inset-0 w-full h-full object-cover object-center scale-105"
    />
    <div class="absolute inset-0 bg-gradient-to-r from-[#FFFDFB] via-[#FFFDFB]/95 to-[#FFFDFB]/80"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-[#FFFDFB]/75 via-transparent to-[#FFFDFB]"></div>

    <div class="relative max-w-5xl mx-auto px-6 lg:px-12 py-20 lg:py-28 text-center flex flex-col items-center">
        <div class="inline-flex items-center gap-2 mb-6 px-4 py-1.5 rounded-full bg-white border border-neutral-200/90 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-cbn-red" aria-hidden="true"></span>
            <span class="text-[11px] lg:text-xs font-semibold tracking-widest uppercase text-neutral-800">
                Convenção Batista Nacional do Estado de Goiás
            </span>
        </div>

        <h1 class="font-brand-serif font-extrabold text-3xl sm:text-5xl lg:text-6xl text-cbn-black tracking-tight leading-[1.15] max-w-4xl text-balance">
            Uma Convenção que Cuida, Fortalece e Multiplica
        </h1>

        <p class="mt-6 font-brand-sans text-base sm:text-lg lg:text-xl text-neutral-700 leading-relaxed max-w-2xl text-balance">
            Transformamos fé em ação concreta: acolhemos famílias, fortalecemos comunidades e geramos oportunidades de dignidade em todo o Goiás.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
            <x-button variant="primary" size="lg" href="{{ route('quem-somos') }}" class="w-full sm:w-auto">
                Conheça a CBN-GO
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </x-button>
            <x-button variant="outline-dark" size="lg" href="{{ route('igrejas') }}" class="w-full sm:w-auto">
                Encontre uma Igreja
            </x-button>
        </div>

        <div class="mt-14 inline-flex items-center gap-2 text-xs text-neutral-500 font-medium">
            <span class="w-8 h-px bg-neutral-300" aria-hidden="true"></span>
            <span>Cuidado · Esperança · Transformação social</span>
            <span class="w-8 h-px bg-neutral-300" aria-hidden="true"></span>
        </div>
    </div>
</section>
@endif

{{-- Últimas Notícias --}}
<section class="py-20 lg:py-24 bg-white border-b border-neutral-200" id="noticias">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-cbn-orange-ink">Impacto em movimento</span>
                </div>
                <h2 class="font-brand-serif font-bold text-2xl lg:text-3xl text-cbn-black tracking-tight">
                    Últimas Notícias e Comunicados
                </h2>
                <p class="text-neutral-600 mt-2 text-sm md:text-base max-w-xl">
                    Histórias reais de cuidado, solidariedade e transformação nas comunidades goianas
                </p>
            </div>
            <a
                href="{{ route('noticias.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-cbn-black hover:text-cbn-orange-ink group transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <span>Ver todas as notícias</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        @if ($latestNews->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($latestNews as $news)
                    <x-news-card
                        :title="$news->title"
                        :slug="$news->slug"
                        :category="$news->category"
                        :date="$news->published_at"
                        :image="$news->featured_image"
                        :excerpt="$news->excerpt"
                    />
                @endforeach
            </div>
        @else
            <div class="bg-[#FFFDFB] border border-dashed border-neutral-300 rounded-lg p-12 text-center text-neutral-500">
                <p class="font-bold text-cbn-black font-brand-serif text-lg">Nenhuma notícia publicada no momento.</p>
                <p class="text-sm text-neutral-500 mt-1">Em breve, novas histórias de impacto e cuidado nas comunidades.</p>
            </div>
        @endif
    </div>
</section>

{{-- Artigos Pastorais --}}
<section class="py-20 lg:py-24 bg-[#FAF9F7] border-b border-neutral-200" id="artigos">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-cbn-orange-ink mb-2">
                Fé que gera cuidado
            </span>
            <h2 class="font-brand-serif font-bold text-3xl lg:text-4xl text-cbn-black tracking-tight">
                Artigos e Reflexões Pastorais
            </h2>
            <div class="w-12 h-0.5 bg-cbn-orange mx-auto mt-4 mb-3" aria-hidden="true"></div>
            <p class="text-neutral-600 text-sm md:text-base">
                Reflexões que conectam fé, dignidade humana e responsabilidade social — para quem cuida de pessoas e transforma realidades.
            </p>
            <a
                href="{{ route('artigos.index') }}"
                class="inline-flex items-center gap-1.5 mt-4 text-sm font-semibold text-cbn-black hover:text-cbn-orange-ink transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                Ver todos os artigos
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        @if ($latestArticles->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($latestArticles as $article)
                    <x-article-card
                        :title="$article->title"
                        :slug="$article->slug"
                        :author-name="$article->author?->name ?? 'CBN Goiás'"
                        :author-slug="$article->author?->slug"
                        :author-avatar="$article->author?->avatar"
                        :category="$article->category ?? null"
                        :reading-time="$article->reading_time"
                        :excerpt="$article->excerpt"
                        :published-at="$article->published_at"
                    />
                @endforeach
            </div>
        @else
            <div class="bg-white border border-dashed border-neutral-300 rounded-lg p-12 text-center text-neutral-500">
                <p class="font-bold text-cbn-black font-brand-serif text-lg">Nenhum artigo publicado no momento.</p>
                <p class="text-sm text-neutral-500 mt-1">Em breve, novos textos sobre cuidado, justiça e transformação social.</p>
            </div>
        @endif
    </div>
</section>

{{-- Projetos Sociais (Issue #27): só aparece quando há projetos publicados --}}
@if ($featuredProjects->isNotEmpty())
<section class="py-20 lg:py-24 bg-white border-b border-neutral-200" id="projetos-sociais">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-cbn-orange-ink">Missão integral</span>
                </div>
                <h2 class="font-brand-serif font-bold text-2xl lg:text-3xl text-cbn-black tracking-tight">
                    Projetos Sociais
                </h2>
                <p class="text-neutral-600 mt-2 text-sm md:text-base max-w-xl">
                    Ações das igrejas da CBN Goiás em cuidado social, cultura, educação e formação de líderes. Conheça e ajude.
                </p>
            </div>
            <a
                href="{{ route('projetos-sociais.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-cbn-black hover:text-cbn-orange-ink group transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <span>Ver todos os projetos</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($featuredProjects as $project)
                <x-project-card :project="$project" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Destaques institucionais: SETEBAN + Assembleia / Eventos --}}
<section class="py-20 lg:py-24 bg-white border-b border-neutral-200" id="institucional">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <div class="bg-[#FAF9F7] border border-neutral-200 rounded-lg p-8 lg:p-10 flex flex-col justify-between hover:border-neutral-300 transition-all duration-200">
                <div>
                    <div class="inline-flex items-center gap-2 mb-4">
                        <span class="p-1.5 rounded bg-cbn-orange/10 text-cbn-orange-ink-dark">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider text-cbn-black">Formação que transforma vidas</span>
                    </div>
                    <h3 class="font-brand-serif font-bold text-2xl lg:text-3xl text-cbn-black tracking-tight">
                        SETEBAN-GO
                    </h3>
                    <p class="text-neutral-700 text-sm lg:text-base mt-3 leading-relaxed">
                        Seminário Teológico Batista Nacional: forma líderes preparados para servir pessoas, fortalecer famílias e gerar impacto social duradouro nas comunidades.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-neutral-200 text-xs font-medium text-neutral-800 rounded shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                            Bacharelado em Teologia
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-neutral-200 text-xs font-medium text-neutral-800 rounded shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-cbn-orange-mid" aria-hidden="true"></span>
                            Liderança e cuidado pastoral
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-neutral-200 text-xs font-medium text-neutral-800 rounded shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-cbn-gold" aria-hidden="true"></span>
                            Ação comunitária
                        </span>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-neutral-200/70 flex items-center justify-between gap-4">
                    <x-button variant="secondary" size="sm" href="https://setebango.com.br" target="_blank" rel="noopener noreferrer">
                        Conheça o SETEBAN-GO
                    </x-button>
                    <span class="text-xs font-medium text-cbn-orange-ink-dark bg-cbn-orange/10 px-3 py-1 rounded-full hidden sm:inline">Matrículas abertas</span>
                </div>
            </div>

            <div id="eventos" class="scroll-mt-24 bg-[#FAF9F7] border border-neutral-200 rounded-lg p-8 lg:p-10 flex flex-col justify-between hover:border-neutral-300 transition-all duration-200">
                <div>
                    <div class="inline-flex items-center gap-2 mb-4">
                        <span class="p-1.5 rounded bg-cbn-orange/10 text-cbn-orange-ink-dark">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider text-cbn-black">Encontros que geram impacto · Eventos Anuais</span>
                    </div>
                    <h3 class="font-brand-serif font-bold text-2xl lg:text-3xl text-cbn-black tracking-tight">
                        Assembleia Geral & Eventos Anuais
                    </h3>
                    <p class="text-neutral-700 text-sm lg:text-base mt-3 leading-relaxed">
                        Momentos em que igrejas se unem para decidir, celebrar e ampliar o alcance do cuidado social em Goiás — da capital ao interior.
                    </p>
                    <div class="mt-6 bg-white border border-neutral-200 rounded-lg p-4 space-y-3 shadow-sm">
                        <div class="flex items-center justify-between text-xs pb-2 border-b border-neutral-100 gap-4">
                            <span class="text-neutral-500 font-medium">Agenda</span>
                            <span class="font-bold text-cbn-black text-right">Calendário oficial 2026</span>
                        </div>
                        <div class="flex items-center justify-between text-xs gap-4">
                            <span class="text-neutral-500 font-medium">Foco</span>
                            <span class="inline-flex items-center gap-1 font-semibold text-cbn-orange-ink">
                                <span class="w-1.5 h-1.5 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                                Comunhão com propósito social
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-neutral-200/70 flex items-center justify-between gap-4">
                    <x-button variant="primary" size="sm" href="{{ route('noticias.index') }}">
                        Ver Eventos Anuais
                    </x-button>
                    <span class="text-xs text-neutral-500 font-medium hidden sm:inline">Unidade & Serviço</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Busca de igrejas --}}
<section class="py-14 bg-white border-b border-neutral-200" id="igrejas">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="border border-neutral-200 rounded-lg p-6 sm:p-8 bg-[#FBFBFA] flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-cbn-black text-white flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-brand-serif font-bold text-lg text-cbn-black">Encontre uma comunidade de cuidado perto de você</h4>
                    <p class="text-xs sm:text-sm text-neutral-600">Igrejas e pontos de apoio em Goiânia, Anápolis, Rio Verde e no interior — espaços de acolhimento, esperança e serviço.</p>
                </div>
            </div>
            <x-button variant="outline-dark" size="sm" href="{{ route('igrejas') }}" class="w-full md:w-auto shrink-0">
                Guia de Igrejas Filiadas
            </x-button>
        </div>
    </div>
</section>
@endsection
