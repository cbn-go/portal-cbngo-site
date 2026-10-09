@extends('layouts.app')

@section('title', 'Página Inicial')

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
{{-- 3.2 HERO SECTION DE ALTO IMPACTO --}}
<section class="relative bg-gradient-to-b from-cbn-navy via-cbn-navy to-cbn-navy-dark text-white py-20 lg:py-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
    {{-- Detalhes decorativos sutis nobres --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-cbn-gold blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-cbn-navy-light blur-3xl"></div>
    </div>

    <div class="relative max-w-5xl mx-auto text-center space-y-6">
        <div class="inline-flex items-center gap-2">
            <x-badge variant="gold" :dot="true">
                Convenção Batista Nacional de Goiás
            </x-badge>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white font-brand-serif leading-tight">
            Convenção Batista Nacional do Estado de Goiás
        </h1>

        <p class="text-xl sm:text-2xl text-cbn-gold font-brand-serif italic max-w-3xl mx-auto">
            "Uma Convenção que Cuida, Fortalece e Multiplica"
        </p>

        <p class="text-base sm:text-lg text-slate-300 max-w-3xl mx-auto font-brand-sans leading-relaxed">
            Comunhão, proclamação do evangelho bíblico e fortalecimento mútuo de congregações e líderes batistas nacionais em território goiano.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <x-button variant="gold" size="lg" href="/quem-somos">
                Conheça a CBN-GO
            </x-button>
            <x-button variant="outline" size="lg" href="/igrejas" class="text-white border-white/40 hover:bg-white/10 hover:border-white focus-visible:ring-white">
                Encontre uma Igreja
            </x-button>
        </div>
    </div>
</section>

{{-- 3.5 BANNERS INSTITUCIONAIS DE DESTAQUE (SETEBAN-GO & EVENTOS ANUAIS) --}}
<section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto -mt-8 relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Destaque SETEBAN-GO --}}
        <div class="bg-white rounded-2xl p-8 shadow-md border border-slate-200/80 hover:border-cbn-gold/50 transition-all duration-200 flex flex-col justify-between group">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <x-badge variant="gold" size="sm">
                        Educação Teológica
                    </x-badge>
                    <span class="text-xs font-bold text-cbn-navy uppercase tracking-wider font-brand-serif">
                        SETEBAN-GO
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 font-brand-serif group-hover:text-cbn-navy transition">
                    Seminário Teológico Batista Nacional
                </h3>
                <p class="text-slate-600 text-sm font-brand-sans leading-relaxed">
                    Formação teológica pastoral sólida com compromisso bíblico e fidelidade à Palavra de Deus. Cursos de teologia ministerial, liderança eclesiástica e capacitação contínua para obreiros em Goiás.
                </p>
            </div>
            <div class="pt-6 mt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">Inscrições e vestibular abertos</span>
                <x-button variant="gold" size="sm" href="https://setebango.com.br" target="_blank" rel="noopener noreferrer">
                    Conhecer o SETEBAN-GO &rarr;
                </x-button>
            </div>
        </div>

        {{-- Destaque Eventos Anuais & Assembleia Geral --}}
        <div class="bg-gradient-to-br from-cbn-navy to-cbn-navy-dark text-white rounded-2xl p-8 shadow-md border border-cbn-navy-light/40 flex flex-col justify-between group">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <x-badge variant="navy" size="sm" class="bg-white/10 text-cbn-gold border border-cbn-gold/30">
                        Comunhão & Unidade
                    </x-badge>
                    <span class="text-xs font-bold text-cbn-gold uppercase tracking-wider font-brand-serif">
                        CBN-GO
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-white font-brand-serif group-hover:text-cbn-gold-light transition">
                    Assembleia Geral & Eventos Anuais
                </h3>
                <p class="text-slate-300 text-sm font-brand-sans leading-relaxed">
                    Participe dos nossos encontros estaduais, retiros pastorais, conferências missionárias da JAMI e eventos estaduais da juventude batista (JUBANG). Fortaleça a comunhão do seu ministério.
                </p>
            </div>
            <div class="pt-6 mt-4 border-t border-white/10 flex items-center justify-between">
                <span class="text-xs text-slate-300 font-medium">Calendário oficial 2026</span>
                <x-button variant="secondary" size="sm" href="#eventos">
                    Ver Eventos Anuais &rarr;
                </x-button>
            </div>
        </div>
    </div>
</section>

{{-- 3.3 SEÇÃO DE ÚLTIMAS NOTÍCIAS --}}
<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 pb-4 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <x-badge variant="gold" :dot="true" size="sm">
                    Informativo Oficial
                </x-badge>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 font-brand-serif tracking-tight">
                Últimas Notícias e Comunicados
            </h2>
            <p class="text-slate-600 text-sm mt-1 font-brand-sans">
                Acompanhe as notícias mais recentes da convenção e os testemunhos das congregações goianas.
            </p>
        </div>
        <a
            href="/noticias"
            class="inline-flex items-center gap-1.5 text-sm font-bold text-cbn-navy hover:text-cbn-gold-dark transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded self-start sm:self-auto"
        >
            <span>Ver todas as notícias</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>

    @if ($latestNews->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($latestNews as $news)
                <x-news-card
                    :title="$news->title"
                    :slug="$news->slug"
                    :category="$news->category"
                    :date="$news->published_at"
                    :image="$news->image_url"
                    :excerpt="$news->excerpt"
                />
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-12 text-center text-slate-500 shadow-sm">
            <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <p class="font-bold text-slate-800 font-brand-serif text-lg">Nenhuma notícia publicada no momento.</p>
            <p class="text-sm text-slate-500 mt-1 font-brand-sans">Novos comunicados oficiais serão disponibilizados em breve.</p>
        </div>
    @endif
</section>

{{-- 3.4 SEÇÃO DE ARTIGOS E REFLEXÕES PASTORAIS --}}
<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto bg-slate-100/60 rounded-3xl my-10 border border-slate-200/60">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 pb-4 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <x-badge variant="navy" :dot="true" size="sm">
                    Edificação & Doutrina
                </x-badge>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 font-brand-serif tracking-tight">
                Artigos e Reflexões Pastorais
            </h2>
            <p class="text-slate-600 text-sm mt-1 font-brand-sans">
                Reflexões bíblicas, conselhos de liderança e estudos doutrinários produzidos por articulistas credenciados.
            </p>
        </div>
        <a
            href="/artigos"
            class="inline-flex items-center gap-1.5 text-sm font-bold text-cbn-navy hover:text-cbn-gold-dark transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-gold rounded self-start sm:self-auto"
        >
            <span>Ver todos os artigos</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>

    @if ($latestArticles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($latestArticles as $article)
                <x-article-card
                    :title="$article->title"
                    :slug="$article->slug"
                    :author-name="$article->author->name ?? 'Articulista CBN-GO'"
                    :author-avatar="$article->author->avatar_url ?? null"
                    :reading-time="$article->reading_time"
                    :excerpt="$article->excerpt"
                    :published-at="$article->published_at"
                />
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-12 text-center text-slate-500 shadow-sm">
            <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <p class="font-bold text-slate-800 font-brand-serif text-lg">Nenhum artigo publicado no momento.</p>
            <p class="text-sm text-slate-500 mt-1 font-brand-sans">Reflexões pastorais serão publicadas em breve pelos nossos articulistas.</p>
        </div>
    @endif
</section>
@endsection
