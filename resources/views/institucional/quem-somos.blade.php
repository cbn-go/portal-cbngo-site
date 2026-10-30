@extends('layouts.app')

@section('title', 'Quem Somos')
@section('meta_description', 'História, missão, Declaração Doutrinária, Pacto de Fé e documentos oficiais da Convenção Batista Nacional do Estado de Goiás.')

@section('content')
<div class="bg-[#FFFDFB]">
    {{-- Cabeçalho Institucional da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Institucional</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Quem Somos
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    A Convenção Batista Nacional do Estado de Goiás une igrejas batistas nacionais em comunhão, cooperação e missão, para que o evangelho alcance cada município goiano.
                </p>
            </div>

            <nav class="mt-8 flex flex-wrap gap-2 text-sm" aria-label="Seções da página">
                <a href="#historia" class="rounded-full border border-neutral-300 bg-white px-4 py-1.5 text-neutral-700 hover:border-cbn-orange hover:text-cbn-orange-ink transition">História</a>
                <a href="#missao" class="rounded-full border border-neutral-300 bg-white px-4 py-1.5 text-neutral-700 hover:border-cbn-orange hover:text-cbn-orange-ink transition">Missão e visão</a>
                <a href="#declaracao-doutrinaria" class="rounded-full border border-neutral-300 bg-white px-4 py-1.5 text-neutral-700 hover:border-cbn-orange hover:text-cbn-orange-ink transition">Declaração Doutrinária</a>
                <a href="#pacto" class="rounded-full border border-neutral-300 bg-white px-4 py-1.5 text-neutral-700 hover:border-cbn-orange hover:text-cbn-orange-ink transition">Pacto de Fé</a>
                <a href="#documentos" class="rounded-full border border-neutral-300 bg-white px-4 py-1.5 text-neutral-700 hover:border-cbn-orange hover:text-cbn-orange-ink transition">Estatuto e Regimento</a>
            </nav>
        </div>
    </section>

    {{-- História da CBN em Goiás (Sub-issue 7.1) --}}
    <section id="historia" class="scroll-mt-28 py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="space-y-3">
                <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Desde 1967</span>
                <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">Nossa história</h2>
                <p class="text-sm text-neutral-500">A trajetória pioneira dos batistas nacionais e sua presença em Goiás.</p>
            </div>
            <div class="lg:col-span-2">
                @include('institucional.partials.historia')
            </div>
        </div>
    </section>

    {{-- Missão e Visão --}}
    <section id="missao" class="scroll-mt-28 border-y border-neutral-200/80 bg-white py-14 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-xl bg-neutral-50 border border-neutral-200/80 p-7 space-y-2">
                <h2 class="font-brand-serif font-bold text-xl text-cbn-black">Missão</h2>
                <p class="text-neutral-600 leading-relaxed">
                    Promover a comunhão entre as igrejas por meio de ações fraternas e, por seus órgãos, instituições e departamentos, capacitar as igrejas filiadas para levar as Boas Novas “em Jerusalém, na Judéia e Samaria, e até aos confins da terra” (Atos 1.8).
                </p>
            </div>
            <div class="rounded-xl bg-neutral-50 border border-neutral-200/80 p-7 space-y-2">
                <h2 class="font-brand-serif font-bold text-xl text-cbn-black">Visão</h2>
                <p class="text-neutral-600 leading-relaxed">
                    Ser uma igreja centrada em Cristo e guiada pela Palavra, que frutifica, reflete Cristo no cuidado mútuo e expande o Reino de Deus.
                </p>
            </div>
        </div>
    </section>

    {{-- Declaração Doutrinária (Sub-issue 7.1) --}}
    <section id="declaracao-doutrinaria" class="scroll-mt-28 py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 space-y-8">
            <div class="max-w-3xl space-y-3">
                <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">No que cremos</span>
                <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">Declaração Doutrinária</h2>
                <p class="text-neutral-600 leading-relaxed">
                    Os fundamentos de fé compartilhados pelas Igrejas Batistas Nacionais.
                </p>
            </div>
            @include('institucional.partials.doutrina')
        </div>
    </section>

    {{-- Pacto de Fé (Sub-issue 7.1) --}}
    <section id="pacto" class="scroll-mt-28 bg-neutral-950 text-neutral-200 py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="space-y-3">
                <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange">Pacto de Fé</span>
                <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-white">Pacto Renovado</h2>
                <p class="text-sm text-neutral-400">Firmado pelos batistas nacionais reunidos em Luziânia-GO.</p>
            </div>
            <ol class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 text-sm leading-relaxed">
                <li><strong class="text-white">Sonho:</strong> uma igreja saudável, centrada em Cristo.</li>
                <li><strong class="text-white">Visão:</strong> a unidade do Corpo de Cristo.</li>
                <li><strong class="text-white">Hoje:</strong> reconhecer a crise e voltar-se a Deus em arrependimento (2 Crônicas 7.14).</li>
                <li><strong class="text-white">Perdão:</strong> reconhecimento, confissão, arrependimento e perdão mútuo.</li>
                <li><strong class="text-white">Nossa conduta:</strong> respeito, reconciliação, santidade e fidelidade.</li>
                <li><strong class="text-white">Administrativo:</strong> transparência e responsabilidade com os recursos da obra.</li>
                <li><strong class="text-white">Futuro:</strong> planejamento estratégico e grupos de trabalho para os próximos passos.</li>
            </ol>
        </div>
    </section>

    {{-- Estatuto, Regimento Interno e documentos oficiais (Sub-issue 7.1) --}}
    <section id="documentos" class="scroll-mt-28 py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 space-y-8">
            <div class="max-w-3xl space-y-3">
                <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Transparência</span>
                <h2 class="font-brand-serif font-bold text-2xl sm:text-3xl text-cbn-black">Documentos oficiais</h2>
                <p class="text-neutral-600 leading-relaxed">Estatuto, Regimento Interno e demais documentos disponíveis para consulta e download.</p>
            </div>

            @if ($statute)
                <a
                    href="{{ route('estatuto.show') }}"
                    data-testid="statute-card"
                    class="group flex flex-col sm:flex-row sm:items-center justify-between gap-5 rounded-xl bg-cbn-black text-white p-6 sm:p-8 hover:bg-cbn-black/95 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange focus-visible:ring-offset-2"
                >
                    <span class="flex items-start gap-4">
                        <span class="w-12 h-12 rounded-lg bg-white/10 text-cbn-gold flex items-center justify-center shrink-0" aria-hidden="true">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.04A8.97 8.97 0 006 3.75c-1.05 0-2.06.18-3 .51v14.25A8.99 8.99 0 016 18c2.3 0 4.41.87 6 2.29m0-14.25a8.97 8.97 0 016-2.29c1.05 0 2.06.18 3 .51v14.25A8.99 8.99 0 0018 18a8.97 8.97 0 00-6 2.29m0-14.25v14.25"/></svg>
                        </span>
                        <span>
                            <span class="block text-[11px] uppercase font-bold tracking-wider text-cbn-gold">Em vigor</span>
                            <span class="block font-brand-serif font-bold text-xl sm:text-2xl">{{ $statute->title }}</span>
                            <span class="block text-sm text-white/70 mt-1">Aprovado pela {{ $statute->approval_body }} em {{ $statute->approved_place }}, {{ $statute->approved_at->translatedFormat('d \d\e F \d\e Y') }}.</span>
                        </span>
                    </span>
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-cbn-gold group-hover:text-white shrink-0">
                        Ler o estatuto
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </span>
                </a>
            @endif

            @if ($documents->isEmpty() && ! $statute)
                <div data-testid="documents-empty" class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                    Nenhum documento oficial publicado no momento.
                </div>
            @elseif ($documents->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($documents as $document)
                        <x-document-card :document="$document" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Chamada para a Diretoria --}}
    <section class="border-t border-neutral-200/80 bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-brand-serif font-bold text-xl text-cbn-black">Conheça quem lidera a convenção</h2>
                <p class="text-sm text-neutral-600">Diretoria atual, conselho fiscal e coordenadores de regiões.</p>
            </div>
            <x-button variant="primary" href="{{ route('diretoria') }}">Ver a diretoria</x-button>
        </div>
    </section>
</div>
@endsection
