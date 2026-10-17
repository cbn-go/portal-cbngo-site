@extends('layouts.app')

@section('title', 'Guia e Diretório de Igrejas em Goiás — CBN Goiás')

@section('content')
<div
    x-data="{
        selectedChurch: null,
        mapExpanded: true,
        selectChurchFromMap(slug) {
            this.selectedChurch = slug;
            const card = document.getElementById('igreja-' + slug);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
        focusChurchOnMap(slug) {
            this.selectedChurch = slug;
            this.mapExpanded = true;
            const mapEl = document.getElementById('mapa-goias');
            if (mapEl) {
                mapEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }"
    class="bg-[#FFFDFB]"
>
    {{-- Cabeçalho Institucional da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange">Comunhão & Presença no Estado</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Guia e Diretório de Igrejas
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    Encontre uma congregação batista nacional filiada à CBN-GO perto de você. Consulte endereços, pastores titulares, horários de cultos e rotas em todos os municípios goianos.
                </p>
            </div>
        </div>
    </section>

    {{-- Barra de Busca Rápida (Nome ou Bairro) e Dropdown de Cidades Goianas (Sub-issue 6.1) --}}
    <section class="border-b border-neutral-200/80 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <form method="GET" action="{{ route('igrejas') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                {{-- Busca rápida por nome da igreja ou bairro --}}
                <div class="relative flex-1">
                    <label for="busca-igreja" class="sr-only">Buscar por nome da igreja ou bairro</label>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-neutral-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        id="busca-igreja"
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Buscar por nome da igreja ou bairro/setor..."
                        class="w-full rounded border border-neutral-300 bg-white pl-10 pr-4 py-2.5 text-sm text-neutral-800 placeholder-neutral-400 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    />
                </div>

                {{-- Dropdown de Cidades Goianas Cadastradas --}}
                <div class="w-full lg:w-72">
                    <label for="filtro-cidade" class="sr-only">Filtrar por cidade goiana</label>
                    <select
                        id="filtro-cidade"
                        name="cidade"
                        class="w-full rounded border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-800 focus:border-cbn-orange focus:outline-none focus:ring-1 focus:ring-cbn-orange transition"
                    >
                        <option value="">Todas as cidades de Goiás</option>
                        @foreach ($availableCities as $cityOption)
                            <option value="{{ $cityOption }}" {{ $cidade === $cityOption ? 'selected' : '' }}>{{ $cityOption }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Botões de Ação --}}
                <div class="flex items-center gap-2 shrink-0">
                    <x-button variant="primary" size="md" type="submit" class="w-full lg:w-auto">
                        Filtrar
                    </x-button>

                    @if ($q !== '' || $cidade !== '')
                        <a
                            href="{{ route('igrejas') }}"
                            class="inline-flex items-center justify-center px-3.5 py-2.5 text-sm font-medium text-neutral-600 hover:text-cbn-orange border border-neutral-200 rounded hover:bg-neutral-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            Limpar filtros
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- Mapa Coroplético Municipal de Goiás Sincronizado com os Filtros (Sub-issue 6.3) --}}
    <x-goias-map
        :presence-by-city="$presenceByCity"
        :total-municipalities="$totalMunicipalities ?? 246"
        :total-count="$churches->total()"
        :active-query="$q"
        :active-city="$cidade"
    />

    {{-- Listagem de Cards Detalhados das Igrejas (Sub-issue 6.2) --}}
    <section id="lista-igrejas" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-10 sm:py-14">
        @if ($churches->count() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-8">
                <div>
                    <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                        Congregações Filiadas
                        @if ($cidade !== '')
                            <span>em {{ $cidade }}</span>
                        @endif
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        Exibindo {{ $churches->count() }} de {{ $churches->total() }} {{ $churches->total() === 1 ? 'congregação encontrada' : 'congregações encontradas' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($churches as $church)
                    <x-church-card :church="$church" />
                @endforeach
            </div>

            {{-- Paginação --}}
            @if ($churches->hasPages())
                <div class="mt-12">
                    <x-pagination
                        :current-page="$churches->currentPage()"
                        :total-pages="$churches->lastPage()"
                        :prev-url="$churches->previousPageUrl()"
                        :next-url="$churches->nextPageUrl()"
                    />
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-16 px-4 bg-white rounded-lg border border-neutral-200/80 max-w-xl mx-auto space-y-4">
                <div class="w-14 h-14 mx-auto rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                    Nenhuma congregação encontrada
                </h2>
                <p class="font-brand-sans text-neutral-600 text-sm max-w-md mx-auto leading-relaxed">
                    Não localizamos igrejas para os critérios selecionados. Tente buscar por outro bairro, nome de igreja ou limpar os filtros para visualizar todo o estado de Goiás.
                </p>
                <div class="pt-2">
                    <x-button variant="primary" size="md" href="{{ route('igrejas') }}">
                        Limpar filtros
                    </x-button>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
