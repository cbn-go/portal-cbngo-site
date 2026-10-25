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

{{-- Últimas Notícias: layout editorial (manchete + lista de chamadas) --}}
<section class="py-16 lg:py-20 bg-white border-b border-neutral-200" id="noticias">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex items-end justify-between gap-6 mb-10 border-b-2 border-cbn-black pb-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-cbn-orange-ink mb-1">Informativo</p>
                <h2 class="font-brand-serif font-bold text-3xl lg:text-4xl text-cbn-black tracking-tight">
                    Últimas Notícias e Comunicados
                </h2>
            </div>
            <a
                href="{{ route('noticias.index') }}"
                class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold text-cbn-black hover:text-cbn-orange-ink group transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <span>Ver todas <span class="hidden sm:inline">as notícias</span></span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        @if ($latestNews->isNotEmpty())
            @php($leadNews = $latestNews->first())
            <div class="grid lg:grid-cols-12 gap-10">
                {{-- Manchete --}}
                <article class="group {{ $latestNews->count() > 1 ? 'lg:col-span-7' : 'lg:col-span-12' }}">
                    <a href="{{ route('noticias.show', $leadNews->slug) }}" tabindex="-1" aria-hidden="true" class="relative block aspect-[16/10] overflow-hidden rounded-lg bg-neutral-100">
                        <x-media-image
                            :path="$leadNews->featured_image"
                            :alt="$leadNews->title"
                            variant="cover"
                            fallback-variant="card"
                            sizes="(max-width: 1024px) 100vw, 720px"
                            class="group-hover:scale-[1.02] transition-transform duration-500"
                        />
                        <span class="absolute top-4 left-4 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded shadow-sm {{ $leadNews->is_official ? 'bg-cbn-orange-ink text-white' : 'bg-white/95 text-cbn-black border border-neutral-200' }}">
                            {{ $leadNews->is_official ? 'Comunicado oficial' : $leadNews->category }}
                        </span>
                    </a>
                    <p class="mt-5 text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                        @if ($leadNews->published_at)
                            <time datetime="{{ $leadNews->published_at->toISOString() }}">{{ $leadNews->published_at->translatedFormat('d \d\e F \d\e Y') }}</time>
                        @endif
                        · {{ $leadNews->publisherName() }}
                    </p>
                    <h3 class="mt-2 font-brand-serif font-bold text-2xl lg:text-[2rem] leading-tight text-cbn-black group-hover:text-cbn-orange-ink transition-colors">
                        <a href="{{ route('noticias.show', $leadNews->slug) }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">{{ $leadNews->title }}</a>
                    </h3>
                    @if ($leadNews->excerpt)
                        <p class="mt-3 text-neutral-600 text-base lg:text-lg leading-relaxed line-clamp-3">{{ $leadNews->excerpt }}</p>
                    @endif
                </article>

                {{-- Lista de chamadas --}}
                @if ($latestNews->count() > 1)
                    <div class="lg:col-span-5 flex flex-col">
                        <ol class="divide-y divide-neutral-200 flex-1">
                            @foreach ($latestNews->slice(1) as $item)
                                <li class="py-5 first:pt-0 flex gap-4 group">
                                    <span class="font-brand-serif text-3xl font-bold text-cbn-orange/40 w-9 shrink-0 leading-none" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-cbn-teal">{{ $item->is_official ? 'Comunicado oficial' : $item->category }}</p>
                                        <h3 class="font-brand-serif font-bold text-lg leading-snug text-cbn-black group-hover:text-cbn-orange-ink transition-colors line-clamp-3">
                                            <a href="{{ route('noticias.show', $item->slug) }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">{{ $item->title }}</a>
                                        </h3>
                                        <p class="text-xs text-neutral-500 mt-1">
                                            {{ $item->publisherName() }}@if ($item->publisherCity()) · {{ $item->publisherCity() }}@endif
                                            @if ($item->published_at) · <time datetime="{{ $item->published_at->toISOString() }}">{{ $item->published_at->diffForHumans() }}</time>@endif
                                        </p>
                                    </div>
                                    <a href="{{ route('noticias.show', $item->slug) }}" tabindex="-1" aria-hidden="true" class="w-20 h-20 rounded overflow-hidden bg-neutral-100 shrink-0">
                                        <x-media-image :path="$item->featured_image" alt="" variant="thumb" sizes="80px" />
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                        <x-button variant="outline-dark" size="md" href="{{ route('noticias.index') }}" class="mt-4 w-full">
                            Mais notícias das igrejas
                        </x-button>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-[#FFFDFB] border border-dashed border-neutral-300 rounded-lg p-12 text-center text-neutral-500">
                <p class="font-bold text-cbn-black font-brand-serif text-lg">Nenhuma notícia publicada no momento.</p>
                <p class="text-sm text-neutral-500 mt-1">Volte em breve para acompanhar as ações das igrejas.</p>
            </div>
        @endif
    </div>
</section>

{{-- Projetos Sociais (Issue #27): faixa de impacto + mosaico; só aparece quando há projetos publicados --}}
@if ($featuredProjects->isNotEmpty())
<section class="relative overflow-hidden py-16 lg:py-24 bg-cbn-black text-white" id="projetos-sociais">
    <div aria-hidden="true" class="pointer-events-none absolute -right-32 -top-32 w-[520px] h-[520px] rounded-full bg-cbn-orange/10 blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-12 gap-10 items-end mb-12">
            <div class="lg:col-span-6">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-cbn-gold mb-2">Missão integral</p>
                <h2 class="font-brand-serif font-bold text-3xl lg:text-5xl leading-tight">Fé que vira cuidado nas comunidades goianas</h2>
                <p class="mt-4 text-white/75 text-base lg:text-lg">Projetos sociais das igrejas em quatro frentes inspiradas no Pacto de Lausanne. Conheça, apoie e participe.</p>
            </div>
            @if ($projectStats)
                <dl class="lg:col-span-6 grid grid-cols-3 gap-4" data-testid="project-stats">
                    <div class="border-l-2 border-cbn-orange pl-4">
                        <dt class="text-[11px] sm:text-xs text-white/60 uppercase tracking-wider">Projetos em andamento</dt>
                        <dd class="font-brand-serif text-3xl lg:text-5xl font-bold mt-1">{{ number_format($projectStats['projects'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="border-l-2 border-cbn-gold pl-4">
                        <dt class="text-[11px] sm:text-xs text-white/60 uppercase tracking-wider">Igrejas envolvidas</dt>
                        <dd class="font-brand-serif text-3xl lg:text-5xl font-bold mt-1">{{ number_format($projectStats['churches'], 0, ',', '.') }}</dd>
                    </div>
                    @if ($projectStats['beneficiaries'] > 0)
                        <div class="border-l-2 border-cbn-teal pl-4">
                            <dt class="text-[11px] sm:text-xs text-white/60 uppercase tracking-wider">Pessoas atendidas</dt>
                            <dd class="font-brand-serif text-3xl lg:text-5xl font-bold mt-1">{{ number_format($projectStats['beneficiaries'], 0, ',', '.') }}</dd>
                        </div>
                    @endif
                </dl>
            @endif
        </div>

        {{-- Categorias levam à listagem já filtrada --}}
        <nav aria-label="Projetos por categoria" class="mb-8">
            <ul class="flex flex-wrap gap-2">
                @foreach (\App\Enums\ProjectCategory::cases() as $category)
                    <li>
                        <a
                            href="{{ route('projetos-sociais.index', ['categoria' => $category->slug()]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border border-white/25 hover:bg-white/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            <span class="w-2 h-2 rounded-full {{ $category->badgeClasses() }}" aria-hidden="true"></span>
                            {{ $category->value }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Mosaico: o primeiro projeto em destaque, os demais empilhados --}}
        @php($mosaicCount = $featuredProjects->count())
        <div class="grid gap-5 {{ match ($mosaicCount) { 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-2 lg:grid-cols-3 lg:grid-rows-2 lg:h-[560px]', default => '' } }}">
            @foreach ($featuredProjects as $project)
                @php($isLead = $loop->first)
                @php($tileClass = match (true) { $mosaicCount === 1 => 'min-h-[380px] lg:min-h-[460px]', $mosaicCount === 2 => 'min-h-[360px] lg:min-h-[440px]', $isLead => 'md:col-span-2 lg:col-span-2 lg:row-span-2 min-h-[340px]', default => 'min-h-[260px]' })
                <a
                    href="{{ route('projetos-sociais.show', $project->slug) }}"
                    data-testid="project-tile"
                    class="group relative block rounded-xl overflow-hidden bg-neutral-800 focus:outline-none focus-visible:ring-4 focus-visible:ring-cbn-orange {{ $tileClass }}"
                >
                    <x-media-image
                        :path="$project->cover_image"
                        :alt="$project->title"
                        :variant="$isLead ? 'cover' : 'card'"
                        fallback-variant="card"
                        :sizes="$isLead ? '(max-width: 1024px) 100vw, 800px' : '(max-width: 768px) 100vw, 400px'"
                        class="absolute inset-0 group-hover:scale-[1.03] transition-transform duration-700"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent" aria-hidden="true"></div>
                    <div class="absolute inset-x-0 bottom-0 {{ $isLead ? 'p-6 lg:p-8' : 'p-5' }}">
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded {{ $project->category->badgeClasses() }}">{{ $project->category->value }}</span>
                            @if ($isLead && $project->is_ongoing)
                                <span class="bg-white/15 backdrop-blur text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">Em andamento</span>
                            @endif
                        </div>
                        <h3 class="font-brand-serif font-bold leading-tight {{ $isLead ? 'text-2xl lg:text-4xl' : 'text-xl' }}">{{ $project->title }}</h3>
                        @if ($isLead)
                            <p class="mt-2 text-white/80 max-w-xl line-clamp-2">{{ $project->summary }}</p>
                        @endif
                        <p class="mt-2 text-sm text-white/70">
                            @if ($project->church){{ $project->church->name }} · @endif{{ $project->city }}
                            @if ($isLead && $project->beneficiaries_count)
                                · <strong class="text-white">{{ number_format($project->beneficiaries_count, 0, ',', '.') }} pessoas atendidas</strong>
                            @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-white/10 pt-8">
            <p class="text-white/75 text-center sm:text-left">
                Sua igreja tem um projeto social?
                <a href="https://admin.cbngo.com.br" target="_blank" rel="noopener noreferrer" class="font-semibold text-white underline underline-offset-2 hover:text-cbn-gold">Cadastre na Área das Igrejas<span class="sr-only"> (abre em nova aba)</span></a>.
            </p>
            <a
                href="{{ route('projetos-sociais.index') }}"
                class="inline-flex items-center gap-2 bg-cbn-orange hover:bg-cbn-orange-mid text-white font-bold text-sm px-6 py-3 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-cbn-black"
            >
                Ver todos os projetos
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- Artigos Pastorais: citação em destaque + lista textual --}}
<section class="py-16 lg:py-24 bg-[#FAF6F0] border-b border-neutral-200" id="artigos">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        @if ($latestArticles->isNotEmpty())
            @php($quoteArticle = $latestArticles->first(fn ($article) => filled($article->excerpt)) ?? $latestArticles->first())
            @php($otherArticles = $latestArticles->reject(fn ($article) => $article->is($quoteArticle))->take(3))
            <div class="grid lg:grid-cols-12 gap-12">
                <figure class="lg:col-span-7">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-cbn-orange-ink mb-2">Fé que gera cuidado</p>
                    <h2 class="font-brand-serif font-bold text-2xl lg:text-3xl text-cbn-black tracking-tight mb-6">Artigos e Reflexões Pastorais</h2>
                    <svg class="w-12 h-12 text-cbn-orange/30 mb-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/></svg>
                    <blockquote class="font-brand-serif text-2xl lg:text-[2.25rem] leading-snug text-cbn-black">
                        <p class="line-clamp-5">{{ $quoteArticle->excerpt ?: $quoteArticle->title }}</p>
                    </blockquote>
                    <figcaption class="mt-8 flex flex-wrap items-center gap-4">
                        @php($quoteAuthor = $quoteArticle->author)
                        <span class="w-14 h-14 rounded-full overflow-hidden ring-4 ring-white bg-cbn-black shrink-0">
                            <x-media-image
                                :path="$quoteAuthor?->avatar"
                                alt=""
                                variant="thumb"
                                sizes="56px"
                                fallback-variant="avatar"
                                :fallback-initials="$quoteAuthor ? collect(explode(' ', $quoteAuthor->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') : 'CB'"
                                class="w-full h-full text-lg"
                            />
                        </span>
                        <span class="min-w-0">
                            @if ($quoteAuthor)
                                <a href="{{ route('autores.show', $quoteAuthor->slug) }}" class="block font-bold text-cbn-black hover:text-cbn-orange-ink">{{ $quoteAuthor->name }}</a>
                                <span class="block text-sm text-neutral-500">{{ $quoteAuthor->role_title }}</span>
                            @else
                                <span class="block font-bold text-cbn-black">CBN Goiás</span>
                            @endif
                        </span>
                        <a href="{{ route('artigos.show', $quoteArticle->slug) }}" class="sm:ml-auto inline-flex items-center gap-2 text-sm font-bold text-cbn-orange-ink hover:text-cbn-orange-mid focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">
                            Ler “{{ $quoteArticle->title }}” &rarr;
                        </a>
                    </figcaption>
                </figure>

                <div class="lg:col-span-5 lg:border-l lg:border-neutral-300 lg:pl-12">
                    <h3 class="font-brand-serif font-bold text-2xl text-cbn-black mb-6">Artigos recentes</h3>
                    @if ($otherArticles->isNotEmpty())
                        <ul class="space-y-6">
                            @foreach ($otherArticles as $article)
                                <li class="group">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">
                                        @if ($article->category){{ $article->category }} · @endif{{ $article->reading_time }} min de leitura
                                    </p>
                                    <h4 class="font-brand-serif font-bold text-lg leading-snug text-cbn-black group-hover:text-cbn-orange-ink transition-colors">
                                        <a href="{{ route('artigos.show', $article->slug) }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">{{ $article->title }}</a>
                                    </h4>
                                    <p class="text-sm text-neutral-600 mt-1">{{ $article->author?->name ?? 'CBN Goiás' }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-neutral-600">Em breve, novos textos sobre cuidado, justiça e transformação social.</p>
                    @endif
                    <a href="{{ route('artigos.index') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-cbn-black border-b-2 border-cbn-black pb-0.5 hover:text-cbn-orange-ink hover:border-cbn-orange-ink transition-colors">
                        Ver todos os artigos &rarr;
                    </a>
                </div>
            </div>
        @else
            <div class="text-center max-w-2xl mx-auto mb-10">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-cbn-orange-ink mb-2">Fé que gera cuidado</p>
                <h2 class="font-brand-serif font-bold text-3xl lg:text-4xl text-cbn-black tracking-tight">Artigos e Reflexões Pastorais</h2>
            </div>
            <div class="bg-white border border-dashed border-neutral-300 rounded-lg p-12 text-center text-neutral-500">
                <p class="font-bold text-cbn-black font-brand-serif text-lg">Nenhum artigo publicado no momento.</p>
                <p class="text-sm text-neutral-500 mt-1">Em breve, novos textos sobre cuidado, justiça e transformação social.</p>
            </div>
        @endif
    </div>
</section>

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
