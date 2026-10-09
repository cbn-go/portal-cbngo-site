@props([
    'message',
    'link' => null,
    'linkText' => 'Saiba mais',
    'type' => 'warning',
    'dismissible' => true,
])

@php
    $typeClasses = match ($type) {
        'critical' => 'bg-rose-900 border-rose-700 text-rose-100',
        'info' => 'bg-cbn-navy border-cbn-navy-light text-slate-100',
        default => 'bg-gradient-to-r from-amber-600 via-amber-700 to-amber-800 border-amber-500 text-white',
    };
@endphp

<div
    role="alert"
    x-data="{ show: true }"
    x-show="show"
    id="cbn-urgent-banner"
    {{ $attributes->merge(['class' => "relative py-2.5 px-4 sm:px-6 text-sm font-medium border-b shadow-sm transition-all {$typeClasses}"]) }}
>
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 overflow-hidden">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white shrink-0 animate-pulse">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </span>
            <p class="truncate text-sm">
                <strong class="font-bold mr-1">AVISO OFICIAL:</strong>
                {{ $message }}
            </p>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            @if ($link)
                <a
                    href="{{ $link }}"
                    class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-white underline underline-offset-4 hover:text-cbn-gold-light transition"
                >
                    {{ $linkText }}
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @endif

            @if ($dismissible)
                <button
                    type="button"
                    aria-label="Fechar aviso"
                    onclick="this.closest('#cbn-urgent-banner').remove()"
                    class="p-1 rounded-md text-white/80 hover:text-white hover:bg-white/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
