@props([
    'mapChurches',
    'totalCount' => 0,
])

@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Church> $mapChurches */
    $citiesOnMap = $mapChurches->pluck('city')->unique()->values();
@endphp

<section id="mapa-goias" class="border-b border-neutral-200/80 bg-[#FAF9F7] py-8 sm:py-10" aria-label="Mapa de presença da CBN em Goiás">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
        <div class="bg-white border border-neutral-200/90 rounded-2xl shadow-sm overflow-hidden">
            {{-- Cabeçalho do Mapa com toggle mobile --}}
            <div class="px-5 py-4 sm:px-7 sm:py-5 border-b border-neutral-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-cbn-orange">Presença Estadual CBN-GO</span>
                    </div>
                    <h2 class="font-brand-serif font-bold text-xl sm:text-2xl text-cbn-black mt-0.5">
                        Mapa de Congregações em Goiás
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600 mt-0.5">
                        Clique em um marcador no mapa para visualizar os dados da igreja e destacar seu card na listagem abaixo.
                    </p>
                </div>

                <div class="flex items-center gap-3 self-start sm:self-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#FAF9F7] border border-neutral-200 text-xs font-semibold text-neutral-800">
                        <span class="text-cbn-orange font-bold">{{ $mapChurches->count() }}</span>
                        <span>{{ $mapChurches->count() === 1 ? 'igreja no mapa' : 'igrejas no mapa' }}</span>
                        <span class="text-neutral-300" aria-hidden="true">|</span>
                        <span class="text-cbn-black font-bold">{{ $citiesOnMap->count() }}</span>
                        <span>{{ $citiesOnMap->count() === 1 ? 'município' : 'municípios' }}</span>
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

            {{-- Corpo Interativo do Mapa (Responsivo em Mobile e Desktop) --}}
            <div x-show="mapExpanded" class="grid grid-cols-1 lg:grid-cols-12">
                {{-- Canvas Vetorial do Estado de Goiás com Marcadores --}}
                <div class="lg:col-span-7 xl:col-span-8 relative bg-gradient-to-br from-[#FFFDFB] via-[#FAF7F2] to-[#F4EFE6] p-4 sm:p-6 flex items-center justify-center min-h-[340px] sm:min-h-[420px] border-b lg:border-b-0 lg:border-r border-neutral-200/80">
                    <div class="relative w-full max-w-[500px] aspect-[4/4]">
                        {{-- Silhueta Vetorial Estilizada do Estado de Goiás --}}
                        <svg
                            viewBox="0 0 400 400"
                            class="w-full h-full drop-shadow-sm select-none"
                            role="img"
                            aria-label="Contorno geográfico do Estado de Goiás"
                        >
                            <defs>
                                <linearGradient id="goiasMapGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#FFF9F2" />
                                    <stop offset="100%" stop-color="#F6E8D8" />
                                </linearGradient>
                            </defs>

                            {{-- Grade geográfica sutil --}}
                            <g stroke="#E5DEC9" stroke-width="0.75" stroke-dasharray="3 3">
                                <line x1="80" y1="20" x2="80" y2="380" />
                                <line x1="160" y1="20" x2="160" y2="380" />
                                <line x1="240" y1="20" x2="240" y2="380" />
                                <line x1="320" y1="20" x2="320" y2="380" />
                                <line x1="20" y1="80" x2="380" y2="80" />
                                <line x1="20" y1="160" x2="380" y2="160" />
                                <line x1="20" y1="240" x2="380" y2="240" />
                                <line x1="20" y1="320" x2="380" y2="320" />
                            </g>

                            {{-- Polígono representativo das fronteiras de Goiás --}}
                            <path
                                d="M 195 24
                                   L 248 42
                                   L 285 38
                                   L 342 68
                                   L 356 125
                                   L 340 175
                                   L 352 225
                                   L 318 282
                                   L 278 338
                                   L 215 362
                                   L 145 372
                                   L 82 346
                                   L 42 298
                                   L 54 236
                                   L 88 182
                                   L 112 118
                                   L 148 58
                                   Z"
                                fill="url(#goiasMapGrad)"
                                stroke="#EFA162"
                                stroke-width="2.5"
                                stroke-linejoin="round"
                            />

                            {{-- Rótulos de Macrorregiões Goianas --}}
                            <text x="210" y="85" text-anchor="middle" class="fill-neutral-400 text-[9px] font-semibold uppercase tracking-widest">Norte Goiano</text>
                            <text x="300" y="165" text-anchor="middle" class="fill-neutral-400 text-[9px] font-semibold uppercase tracking-widest">Leste / Entorno</text>
                            <text x="215" y="218" text-anchor="middle" class="fill-neutral-500 text-[10px] font-bold uppercase tracking-widest">Centro Goiano</text>
                            <text x="118" y="290" text-anchor="middle" class="fill-neutral-400 text-[9px] font-semibold uppercase tracking-widest">Sudoeste</text>
                            <text x="235" y="325" text-anchor="middle" class="fill-neutral-400 text-[9px] font-semibold uppercase tracking-widest">Sul Goiano</text>
                        </svg>

                        {{-- Marcadores Interativos das Congregações com Coordenadas Válidas --}}
                        @foreach ($mapChurches as $mapChurch)
                            @php
                                $pos = $mapChurch->getMapPosition();
                            @endphp
                            @if ($pos !== null)
                                <button
                                    type="button"
                                    data-church-marker="{{ $mapChurch->slug }}"
                                    data-church-city="{{ $mapChurch->city }}"
                                    style="left: {{ $pos['x'] }}%; top: {{ $pos['y'] }}%;"
                                    @click="selectChurchFromMap('{{ $mapChurch->slug }}')"
                                    :class="selectedChurch === '{{ $mapChurch->slug }}' ? 'z-30 scale-125 bg-cbn-black text-white ring-4 ring-cbn-orange/40' : 'z-10 bg-cbn-orange text-white hover:scale-110 hover:bg-cbn-black'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-8 h-8 rounded-full shadow-md flex items-center justify-center transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-black group"
                                    title="{{ $mapChurch->name }} ({{ $mapChurch->city }})"
                                    aria-label="Selecionar {{ $mapChurch->name }} em {{ $mapChurch->city }}"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Painel Lateral de Seleção Rápida e Detalhe do Marcador Ativo --}}
                <div class="lg:col-span-5 xl:col-span-4 p-5 sm:p-6 flex flex-col justify-between bg-white">
                    @if ($mapChurches->isNotEmpty())
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-500">
                                    Congregações Mapeadas ({{ $mapChurches->count() }})
                                </h3>
                                <span class="text-xs text-neutral-400">Clique para localizar</span>
                            </div>

                            <div class="max-h-[280px] overflow-y-auto pr-1 space-y-2">
                                @foreach ($mapChurches as $mapChurch)
                                    <div
                                        :class="selectedChurch === '{{ $mapChurch->slug }}' ? 'border-cbn-orange bg-[#FFFDF9]' : 'border-neutral-200/80 hover:border-neutral-300 bg-[#FAF9F7]/60'"
                                        class="rounded-lg border p-3 transition-all"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <button
                                                type="button"
                                                @click="selectChurchFromMap('{{ $mapChurch->slug }}')"
                                                class="text-left flex-1 focus:outline-none"
                                            >
                                                <span class="block text-xs font-semibold text-cbn-orange">{{ $mapChurch->city }} · {{ $mapChurch->neighborhood }}</span>
                                                <span class="block font-brand-serif font-bold text-sm text-cbn-black mt-0.5">{{ $mapChurch->name }}</span>
                                                <span class="block text-xs text-neutral-600 mt-0.5">{{ $mapChurch->pastor_name }}</span>
                                            </button>

                                            <a
                                                href="#igreja-{{ $mapChurch->slug }}"
                                                @click="selectedChurch = '{{ $mapChurch->slug }}'"
                                                class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold text-cbn-black bg-white border border-neutral-200 hover:border-cbn-orange hover:text-cbn-orange transition"
                                            >
                                                <span>Ver card</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="my-auto text-center py-8 px-4 space-y-2">
                            <p class="font-brand-serif font-bold text-base text-cbn-black">
                                Nenhuma coordenada mapeada para este filtro
                            </p>
                            <p class="text-xs text-neutral-500 leading-relaxed">
                                As congregações sem coordenadas geográficas permanecem acessíveis normalmente na listagem detalhada abaixo.
                            </p>
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-neutral-100 flex items-center justify-between text-xs text-neutral-500">
                        <span>Convenção Batista Nacional · Goiás</span>
                        <a href="#lista-igrejas" class="font-semibold text-cbn-orange hover:underline">Ir para a listagem ↓</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
