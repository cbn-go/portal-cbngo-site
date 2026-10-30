@extends('layouts.app')

@section('title', 'Versões do Estatuto')
@section('meta_description', 'Histórico de versões do Estatuto da Convenção Batista Nacional do Estado de Goiás: versão em vigor e versões anteriores.')

@section('content')
<div class="bg-[#FFFDFB]">
    <section class="border-b border-neutral-200 bg-white">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 py-10 lg:py-14">
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex items-center gap-2 text-xs sm:text-sm text-neutral-500 flex-wrap">
                    <li><a href="{{ route('home') }}" class="hover:text-cbn-orange-ink">Início</a></li>
                    <li aria-hidden="true">&rsaquo;</li>
                    <li><a href="{{ route('estatuto.show') }}" class="hover:text-cbn-orange-ink">Estatuto</a></li>
                    <li aria-hidden="true">&rsaquo;</li>
                    <li class="text-neutral-800 font-medium" aria-current="page">Versões</li>
                </ol>
            </nav>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cbn-orange-ink mb-2">Documento oficial</p>
            <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl text-cbn-black">Versões do Estatuto</h1>
            <p class="mt-3 text-neutral-600">A versão em vigor e as versões anteriores aprovadas pela Assembleia Geral, mantidas para consulta.</p>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-6 lg:px-8 py-10 lg:py-14">
        <ol class="space-y-4" data-testid="statute-versions">
            @foreach ($versions as $version)
                <li class="rounded-xl border bg-white p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $version->isCurrent() ? 'border-cbn-black ring-1 ring-cbn-black' : 'border-neutral-200' }}">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider {{ $version->isCurrent() ? 'text-emerald-800' : 'text-neutral-500' }}">
                            {{ $version->isCurrent() ? 'Em vigor' : 'Versão anterior' }}
                        </p>
                        <h2 class="font-brand-serif font-bold text-xl text-cbn-black mt-1">
                            Aprovado em <time datetime="{{ $version->approved_at->toDateString() }}">{{ $version->approved_at->translatedFormat('d \d\e F \d\e Y') }}</time>
                        </h2>
                        <p class="text-sm text-neutral-600 mt-1">{{ $version->approval_body }} · {{ $version->approved_place }}</p>
                    </div>
                    <a
                        href="{{ $version->isCurrent() ? route('estatuto.show') : route('estatuto.version', $version->versionKey()) }}"
                        class="inline-flex items-center justify-center gap-2 border-2 border-cbn-black hover:bg-neutral-50 font-semibold text-sm px-5 py-2.5 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange shrink-0"
                    >
                        Ler esta versão
                    </a>
                </li>
            @endforeach
        </ol>
    </div>
</div>
@endsection
