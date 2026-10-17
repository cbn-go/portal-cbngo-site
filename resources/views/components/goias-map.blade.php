@props([
    'presenceByCity' => [],
    'totalMunicipalities' => 246,
    'totalCount' => 0,
    'activeQuery' => '',
    'activeCity' => '',
])

@php
    /** @var array<string, int> $presenceByCity */
    $citiesWithPresenceCount = count($presenceByCity);
    $citiesWithoutPresenceCount = max(0, $totalMunicipalities - $citiesWithPresenceCount);
@endphp

<section
    id="mapa-goias"
    data-mapa="municipios"
    data-geojson-url="/geo/goias-municipios.geojson"
    data-presence-by-city="{{ json_encode($presenceByCity, JSON_UNESCAPED_UNICODE) }}"
    data-filter-base-url="{{ route('igrejas') }}"
    data-active-query="{{ $activeQuery }}"
    data-active-city="{{ $activeCity }}"
    class="border-b border-neutral-200/80 bg-[#FAF9F7] py-8 sm:py-10"
    aria-label="Mapa municipal de presença da CBN em Goiás"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
        <div class="bg-white border border-neutral-200/90 rounded-2xl shadow-sm overflow-hidden">
            {{-- Cabeçalho do Mapa com contadores e toggle mobile --}}
            <div class="px-5 py-4 sm:px-7 sm:py-5 border-b border-neutral-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-cbn-orange">Presença Municipal CBN-GO</span>
                    </div>
                    <h2 class="font-brand-serif font-bold text-xl sm:text-2xl text-cbn-black mt-0.5">
                        Mapa Municipal de Congregações em Goiás
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600 mt-0.5">
                        Visualize os municípios goianos com e sem congregação filiada. Clique em um município com presença para filtrar a listagem.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 self-start sm:self-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#FAF9F7] border border-neutral-200 text-xs font-semibold text-neutral-800">
                        <span class="text-cbn-orange font-bold">{{ $citiesWithPresenceCount }} / {{ $totalMunicipalities }} municípios</span>
                        <span class="text-neutral-300" aria-hidden="true">|</span>
                        <span class="text-cbn-black font-bold">{{ $totalCount }}</span>
                        <span>{{ $totalCount === 1 ? 'igreja filtrada' : 'igrejas filtradas' }}</span>
                    </div>

                    <button
                        type="button"
                        @click="mapExpanded = !mapExpanded"
                        class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-300 bg-white text-xs font-semibold text-neutral-800 hover:border-cbn-orange transition"
                        :aria-expanded="mapExpanded.toString()"
                    >
                        <span x-text="mapExpanded ? 'Recolher mapa' : 'Expandir mapa'">Expandir mapa</span>
                    </button>
                </div>
            </div>

            {{-- Barra de Legenda Coroplética --}}
            <div class="px-5 py-3 sm:px-7 bg-[#FAF9F7]/70 border-b border-neutral-200/70 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-5">
                    <div class="inline-flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-sm bg-[#E56B20] border border-[#9A3412] shrink-0" aria-hidden="true"></span>
                        <span class="font-semibold text-neutral-800">Com congregação filiada</span>
                        <span class="text-neutral-500">({{ $citiesWithPresenceCount }})</span>
                    </div>

                    <div class="inline-flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-sm bg-[#E5E7EB] border border-[#9CA3AF] shrink-0" aria-hidden="true"></span>
                        <span class="font-medium text-neutral-700">Sem congregação filiada</span>
                        <span class="text-neutral-500">({{ $citiesWithoutPresenceCount }})</span>
                    </div>
                </div>

                @if ($activeCity !== '' || $activeQuery !== '')
                    <a
                        href="{{ route('igrejas') }}"
                        class="font-semibold text-cbn-orange hover:underline"
                    >
                        Ver todos os municípios sem filtro
                    </a>
                @endif
            </div>

            {{-- Corpo Interativo do Mapa (Responsivo em Mobile e Desktop) --}}
            <div x-show="mapExpanded" class="grid grid-cols-1 lg:grid-cols-12">
                {{-- Container Leaflet dos 246 Municípios de Goiás --}}
                <div class="lg:col-span-7 xl:col-span-8 p-4 sm:p-5 bg-[#FAF7F2] border-b lg:border-b-0 lg:border-r border-neutral-200/80">
                    <div
                        id="leaflet-mapa-goias"
                        class="w-full h-[360px] sm:h-[440px] rounded-xl border border-neutral-200/90 shadow-inner z-0"
                        role="region"
                        aria-label="Mapa coroplético dos 246 municípios de Goiás"
                    ></div>
                </div>

                {{-- Painel Lateral de Municípios com Presença no Filtro Atual --}}
                <div class="lg:col-span-5 xl:col-span-4 p-5 sm:p-6 flex flex-col justify-between bg-white">
                    @if ($citiesWithPresenceCount > 0)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Municípios com Presença ({{ $citiesWithPresenceCount }})
                                </h3>
                                <span class="text-xs text-neutral-400">Clique para filtrar cidade</span>
                            </div>

                            <div class="max-h-[340px] overflow-y-auto pr-1 space-y-2">
                                @foreach ($presenceByCity as $cityName => $churchCount)
                                    @php
                                        $isCurrentCity = mb_strtolower($activeCity) === mb_strtolower($cityName);
                                        $cityFilterParams = array_filter([
                                            'q' => $activeQuery !== '' ? $activeQuery : null,
                                            'cidade' => $cityName,
                                        ]);
                                    @endphp
                                    <a
                                        href="{{ route('igrejas', $cityFilterParams) }}"
                                        data-city-presence="{{ $cityName }}"
                                        data-city-count="{{ $churchCount }}"
                                        class="flex items-center justify-between gap-3 rounded-lg border p-3 transition-all {{ $isCurrentCity ? 'border-cbn-orange bg-[#FFFDF9] ring-1 ring-cbn-orange/40' : 'border-neutral-200/80 hover:border-cbn-orange bg-[#FAF9F7]/60' }}"
                                    >
                                        <div class="min-w-0">
                                            <span class="block font-brand-serif font-bold text-sm text-cbn-black truncate">
                                                {{ $cityName }}
                                            </span>
                                            <span class="block text-xs text-neutral-500">
                                                Estado de Goiás · Filtrar congregações
                                            </span>
                                        </div>

                                        <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $isCurrentCity ? 'bg-cbn-orange text-white' : 'bg-cbn-orange/15 text-cbn-orange' }}">
                                            {{ $churchCount }} {{ $churchCount === 1 ? 'igreja' : 'igrejas' }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="my-auto text-center py-8 px-4 space-y-2">
                            <p class="font-brand-serif font-bold text-base text-cbn-black">
                                Nenhum município com congregação neste filtro
                            </p>
                            <p class="text-xs text-neutral-500 leading-relaxed">
                                Todos os {{ $totalMunicipalities }} municípios goianos aparecem sem congregação filiada para os critérios pesquisados. Limpe os filtros para visualizar a presença estadual completa.
                            </p>
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-neutral-100 flex items-center justify-between text-xs text-neutral-500">
                        <span>Malha Municipal IBGE ({{ $totalMunicipalities }} municípios)</span>
                        <a href="#lista-igrejas" class="font-semibold text-cbn-orange hover:underline">Ir para a listagem ↓</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
