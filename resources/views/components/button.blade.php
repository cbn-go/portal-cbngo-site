@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center rounded-lg transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs font-semibold gap-1.5',
        'lg' => 'px-6 py-3.5 text-base font-bold gap-2.5',
        default => 'px-4 py-2.5 text-sm font-semibold gap-2',
    };

    $variantClasses = match ($variant) {
        'gold' => 'bg-cbn-gold text-cbn-black hover:bg-cbn-gold-light focus-visible:ring-cbn-gold shadow-sm hover:shadow',
        'secondary' => 'bg-cbn-black text-white hover:bg-cbn-black/90 focus-visible:ring-cbn-black',
        'outline' => 'border-2 border-cbn-orange text-cbn-orange hover:bg-cbn-orange hover:text-white focus-visible:ring-cbn-orange',
        'outline-gold' => 'border-2 border-cbn-gold text-cbn-gold-dark hover:bg-cbn-gold hover:text-cbn-black focus-visible:ring-cbn-gold',
        'ghost' => 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-slate-300',
        default => 'bg-cbn-orange text-white hover:bg-cbn-orange-mid focus-visible:ring-cbn-orange shadow-sm hover:shadow',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
