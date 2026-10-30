@extends('layouts.app')

@php
    /** @var \App\Models\Statute $statute */
    /** @var \App\Support\Akn\RenderedStatute $document */
    $pageUrl = $isCurrent ? route('estatuto.show') : route('estatuto.version', $statute->versionKey());
    $chapterCount = count($document->toc);
@endphp

@section('title', $isCurrent ? 'Estatuto' : 'Estatuto — versão de '.$statute->approved_at->translatedFormat('d/m/Y'))
@section('meta_description', 'Estatuto da Convenção Batista Nacional do Estado de Goiás, aprovado pela '.$statute->approval_body.' em '.$statute->approved_place.', '.$statute->approved_at->translatedFormat('d \d\e F \d\e Y').'. Texto integral com '.$document->articleCount.' artigos.')
@if (! $isCurrent)
    @section('robots', 'noindex, follow')
@endif

@push('structured-data')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::legislation($statute, $document, $pageUrl)) !!}</script>
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::breadcrumbs([
        ['name' => 'Início', 'url' => route('home')],
        ['name' => 'Estatuto', 'url' => route('estatuto.show')],
    ])) !!}</script>
@endpush

@section('content')
<div
    class="akn-document bg-[#FFFDFB] pb-20"
    x-data="{
        active: null,
        copied: false,
        init() {
            const links = [...this.$el.querySelectorAll('[data-toc-link]')];
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => { if (entry.isIntersecting) this.active = entry.target.id; });
            }, { rootMargin: '-30% 0px -60% 0px' });
            this.$el.querySelectorAll('[data-toc]').forEach((el) => observer.observe(el));
            this.$watch('active', (id) => links.forEach((a) => a.classList.toggle('is-active', a.dataset.tocLink === id)));
        },
        copyLink(button) {
            const id = button.dataset.eid;
            const url = {{ Js::from($pageUrl) }} + '#' + id;
            // A cópia não bloqueia o retorno visual (o navegador pode pedir permissão para a área de transferência).
            navigator.clipboard?.writeText(url).catch(() => {});
            history.replaceState(null, '', '#' + id);
            this.$el.querySelectorAll('.akn-article.is-target').forEach((el) => el.classList.remove('is-target'));
            button.closest('.akn-article')?.classList.add('is-target');
            this.copied = true;
            setTimeout(() => this.copied = false, 2200);
        },
    }"
    @click="if ($event.target.closest('.akn-permalink')) copyLink($event.target.closest('.akn-permalink'))"
>
    @unless ($isCurrent)
        <div class="bg-amber-50 border-b border-amber-200 print:border-amber-400" role="note">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 py-3 text-sm text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <p><strong>Versão não vigente.</strong> Este texto foi aprovado em {{ $statute->approved_at->translatedFormat('d \d\e F \d\e Y') }} e foi substituído por uma versão mais recente.</p>
                <a href="{{ route('estatuto.show') }}" class="font-semibold underline underline-offset-2 hover:text-amber-950 print:hidden">Ler o estatuto em vigor</a>
            </div>
        </div>
    @endunless

    {{-- Cabeçalho do documento --}}
    <section class="border-b border-neutral-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-10 lg:py-14">
            <nav aria-label="Breadcrumb" class="mb-6 print:hidden">
                <ol class="flex items-center gap-2 text-xs sm:text-sm text-neutral-500 flex-wrap">
                    <li><a href="{{ route('home') }}" class="hover:text-cbn-orange-ink">Início</a></li>
                    <li aria-hidden="true">&rsaquo;</li>
                    <li><a href="{{ route('quem-somos') }}" class="hover:text-cbn-orange-ink">A Convenção</a></li>
                    <li aria-hidden="true">&rsaquo;</li>
                    <li class="text-neutral-800 font-medium" aria-current="page">Estatuto</li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-cbn-orange-ink mb-2">
                        Documento oficial · {{ $isCurrent ? 'Em vigor' : 'Versão histórica' }}
                    </p>
                    <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black leading-tight">{{ $statute->title }}</h1>
                    <dl class="mt-6 grid sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-neutral-500 font-semibold">Aprovação</dt>
                            <dd class="text-neutral-800">{{ $statute->approval_body }} · {{ $statute->approved_place }}, {{ $statute->approved_at->translatedFormat('d \d\e F \d\e Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-neutral-500 font-semibold">{{ $isCurrent ? 'Em vigor desde' : 'Vigência a partir de' }}</dt>
                            <dd class="text-neutral-800"><time datetime="{{ $statute->effective_at->toDateString() }}">{{ $statute->effective_at->translatedFormat('d \d\e F \d\e Y') }}</time></dd>
                        </div>
                        @if (filled($statute->minutes_reference))
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-neutral-500 font-semibold">Ata</dt>
                                <dd class="text-neutral-800">{{ $statute->minutes_reference }}</dd>
                            </div>
                        @endif
                        @if (filled($statute->registry_info))
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-neutral-500 font-semibold">Registro</dt>
                                <dd class="text-neutral-800">{{ $statute->registry_info }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-3 print:hidden">
                    @if ($isCurrent)
                        <a
                            href="{{ route('estatuto.xml') }}"
                            class="inline-flex items-center justify-center gap-2 bg-cbn-black hover:bg-cbn-black/90 text-white font-semibold text-sm px-5 py-3 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange focus-visible:ring-offset-2"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Baixar XML
                        </a>
                    @endif
                    <div class="flex gap-3">
                        <button type="button" onclick="window.print()" class="flex-1 inline-flex items-center justify-center border-2 border-cbn-black hover:bg-neutral-50 font-semibold text-sm px-4 py-2.5 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange">
                            Imprimir
                        </button>
                        @if ($hasHistory)
                            <a href="{{ route('estatuto.versions') }}" class="flex-1 inline-flex items-center justify-center border-2 border-cbn-black hover:bg-neutral-50 font-semibold text-sm px-4 py-2.5 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange">
                                Versões anteriores
                            </a>
                        @endif
                    </div>
                    <p class="text-xs text-neutral-500 leading-relaxed">
                        O PDF registrado em cartório, com as assinaturas, pode ser solicitado à
                        <a href="{{ route('contato') }}" class="underline underline-offset-2 hover:text-cbn-orange-ink">secretaria</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-8 lg:pt-12 grid lg:grid-cols-12 gap-10">
        {{-- Sumário: recolhível no celular, fixo na lateral no desktop --}}
        <aside class="lg:col-span-3 print:hidden">
            <details class="lg:hidden rounded-xl border border-neutral-200 bg-white">
                <summary class="cursor-pointer select-none px-4 py-3 font-semibold text-cbn-black flex items-center justify-between">
                    Sumário <span class="text-xs text-neutral-500 font-normal">{{ $chapterCount }} {{ $chapterCount === 1 ? 'capítulo' : 'capítulos' }}</span>
                </summary>
                <x-statute-toc :toc="$document->toc" class="px-2 pb-3" />
            </details>
            <div class="hidden lg:block sticky top-28">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-neutral-500 mb-3 px-3">Sumário</p>
                <x-statute-toc :toc="$document->toc" class="max-h-[calc(100vh-9rem)] overflow-y-auto pr-1" />
            </div>
        </aside>

        {{-- Texto integral gerado do Akoma Ntoso (ids = eIds) --}}
        <div class="lg:col-span-9 max-w-3xl" data-testid="statute-body">
            {!! $document->html !!}

            @if (filled($document->conclusions))
                <p class="mt-12 pt-6 border-t border-neutral-200 text-right italic text-neutral-600">{{ $document->conclusions }}</p>
            @endif
        </div>
    </div>

    <div x-show="copied" x-cloak x-transition.opacity role="status" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-cbn-black text-white text-sm font-semibold px-4 py-2.5 rounded-full shadow-lg print:hidden">
        Link do artigo copiado
    </div>
</div>
@endsection
