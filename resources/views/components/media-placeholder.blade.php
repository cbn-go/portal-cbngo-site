@props([
    'variant' => 'card', // card | cover | avatar | logo
    'initials' => null,
    'label' => null,
])

@php
    $baseClasses = 'flex items-center justify-center text-white shrink-0 transition-transform duration-500';
@endphp

@if ($variant === 'cover')
    {{-- Proporção panorâmica 21:9 para topo de matéria / artigo --}}
    <div
        data-testid="media-placeholder"
        {{ $attributes->merge(['class' => 'w-full aspect-[21/9] bg-gradient-to-br from-cbn-black via-neutral-900 to-cbn-black ' . $baseClasses]) }}
    >
        <div class="flex flex-col items-center gap-3 p-6 text-center">
            <svg class="w-16 h-16 text-cbn-orange/60" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect width="48" height="48" rx="8" fill="currentColor" fill-opacity="0.1"/>
                <path d="M24 10V38M14 20H34" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span class="text-xs font-mono uppercase tracking-widest text-neutral-400">Convenção Batista Nacional de Goiás</span>
        </div>
    </div>
@elseif ($variant === 'avatar')
    {{-- Monograma circular para autores e membros da diretoria --}}
    <div
        data-testid="media-placeholder"
        {{ $attributes->merge(['class' => 'rounded-full bg-cbn-black text-white font-bold font-brand-serif shadow-sm ring-1 ring-neutral-200 ' . $baseClasses]) }}
        aria-hidden="true"
    >{{ strtoupper(trim((string) $initials)) ?: 'CB' }}</div>
@elseif ($variant === 'logo')
    {{-- Quadrado institucional para igrejas / convenção --}}
    <div
        data-testid="media-placeholder"
        {{ $attributes->merge(['class' => 'rounded-xl bg-cbn-orange/10 border border-cbn-orange/20 text-cbn-orange-ink-dark ' . $baseClasses]) }}
        aria-hidden="true"
    >
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v3m0 0l-7 5v10h14V11l-7-5zm-3 15v-5a3 3 0 016 0v5" />
        </svg>
    </div>
@else
    {{-- Padrão 'card': proporção 16:9 para grid de notícias e artigos --}}
    <div
        data-testid="media-placeholder"
        {{ $attributes->merge(['class' => 'w-full h-full aspect-video bg-gradient-to-br from-cbn-black via-neutral-800 to-cbn-black p-6 ' . $baseClasses]) }}
    >
        <div class="flex flex-col items-center gap-2 text-center">
            <svg class="w-14 h-14 text-cbn-orange/50" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect width="48" height="48" rx="8" fill="currentColor" fill-opacity="0.1"/>
                <path d="M24 10V38M14 20H34" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span class="text-[11px] font-mono uppercase tracking-wider text-neutral-400">Convenção Batista Nacional de Goiás</span>
        </div>
    </div>
@endif
