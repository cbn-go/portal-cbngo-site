@props([
    'church',
])

@php
    /** @var \App\Models\Church $church */
    $safeSocials = $church->getSafeSocialLinks();
    $safeEmail = $church->getSafeEmail();
    $serviceTimes = is_array($church->service_times) ? $church->service_times : [];
    $hasCoords = $church->hasValidCoordinates();
@endphp

<article
    id="igreja-{{ $church->slug }}"
    :class="selectedChurch === '{{ $church->slug }}' ? 'ring-2 ring-cbn-orange border-cbn-orange shadow-md bg-[#FFFDF9]' : 'border-neutral-200/90 bg-white hover:border-neutral-300 shadow-sm'"
    class="scroll-mt-28 rounded-xl border p-6 sm:p-7 flex flex-col justify-between transition-all duration-200"
>
    <div class="space-y-5">
        {{-- Topo do Card: Logotipo, Cidade e Nome da Congregação --}}
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 border border-neutral-200 bg-neutral-50" data-testid="church-logo-fallback">
                <x-media-image
                    :path="$church->logo"
                    :alt="'Logotipo ' . $church->name"
                    variant="thumb"
                    fallback-variant="logo"
                    class="w-full h-full object-cover"
                />
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-800">
                        <svg class="w-3 h-3 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $church->city }} · {{ $church->state }}
                    </span>

                    <a
                        href="{{ route('igrejas', ['cidade' => $church->city]) }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-cbn-orange-ink hover:text-cbn-black transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded px-1.5 py-0.5"
                    >
                        <span>Filtrar município</span>
                    </a>
                </div>

                <h3 class="font-brand-serif font-bold text-xl text-cbn-black leading-snug">
                    {{ $church->name }}
                </h3>

                <p class="mt-1 text-xs sm:text-sm text-neutral-600 flex items-center gap-1.5">
                    <span class="font-semibold text-neutral-800">Pastor Titular:</span>
                    <span>{{ $church->pastor_name }}</span>
                </p>
            </div>
        </div>

        {{-- Endereço Completo e Rotas (Google Maps / Waze) --}}
        <div class="rounded-lg bg-[#FAF9F7] border border-neutral-200/80 p-3.5 space-y-2.5">
            <div class="flex items-start gap-2.5 text-xs sm:text-sm text-neutral-700">
                <svg class="w-4 h-4 text-cbn-orange shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div class="space-y-0.5">
                    <p class="font-medium text-cbn-black">{{ $church->getStreetAddress() }}</p>
                    <p class="text-neutral-600">
                        {{ $church->neighborhood }} — {{ $church->city }}/{{ $church->state }}
                        @if (! empty($church->zip_code))
                            <span class="text-neutral-500">· CEP {{ $church->zip_code }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-1">
                <a
                    href="{{ $church->getGoogleMapsUrl() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded border border-neutral-300 bg-white text-xs font-semibold text-neutral-800 hover:border-cbn-orange hover:text-cbn-orange-ink transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                >
                    <svg class="w-3.5 h-3.5 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Google Maps</span>
                </a>

                <a
                    href="{{ $church->getWazeUrl() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded border border-neutral-300 bg-white text-xs font-semibold text-neutral-800 hover:border-cbn-orange hover:text-cbn-orange-ink transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                >
                    <svg class="w-3.5 h-3.5 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>Waze</span>
                </a>
            </div>
        </div>

        {{-- Horários Regulares de Cultos e Reuniões de Oração --}}
        @if (count($serviceTimes) > 0)
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Cultos & Reuniões de Oração</span>
                </h4>
                <ul class="divide-y divide-neutral-100 border border-neutral-200/70 rounded-lg bg-white">
                    @foreach ($serviceTimes as $schedule)
                        <li class="px-3 py-2 text-xs flex items-center justify-between gap-2">
                            <span class="font-medium text-neutral-800">
                                {{ $schedule['label'] ?? 'Culto Regular' }}
                            </span>
                            <span class="shrink-0 font-semibold text-cbn-black bg-neutral-100 px-2 py-0.5 rounded">
                                {{ $schedule['day'] ?? '' }} · {{ $schedule['time'] ?? '' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Rodapé do Card: Telefones, E-mail Institucional e Redes Sociais --}}
    <div class="mt-5 pt-4 border-t border-neutral-200/80 space-y-3">
        <div class="space-y-1.5 text-xs text-neutral-600">
            @if (! empty($church->phone))
                <p class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-cbn-orange shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="font-medium text-neutral-800">{{ $church->phone }}</span>
                </p>
            @endif

            @if (! empty($church->cellphone))
                <p class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-cbn-orange shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-neutral-800">{{ $church->cellphone }}</span>
                </p>
            @endif

            @if ($safeEmail !== null)
                <p class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-cbn-orange shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <a
                        href="mailto:{{ $safeEmail }}"
                        class="text-neutral-700 hover:text-cbn-orange-ink underline decoration-neutral-300 hover:decoration-cbn-orange transition truncate"
                    >
                        {{ $safeEmail }}
                    </a>
                </p>
            @endif
        </div>

        @if (count($safeSocials) > 0)
            <div class="flex flex-wrap items-center gap-2 pt-1">
                @foreach ($safeSocials as $network => $url)
                    <a
                        href="{{ $url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-neutral-100 hover:bg-cbn-orange-ink hover:text-white text-neutral-700 text-xs font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                    >
                        <span>{{ ucfirst($network) }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</article>
