@props([
    'variant' => 'navy',
    'size' => 'md',
    'dot' => false,
])

@php
    $baseClasses = 'inline-flex items-center font-medium rounded-full';

    $sizeClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-xs gap-1',
        default => 'px-2.5 py-1 text-xs gap-1.5',
    };

    $variantClasses = match ($variant) {
        'gold' => 'bg-cbn-gold text-cbn-navy-dark border border-cbn-gold-dark/30',
        'gold-subtle' => 'bg-cbn-gold/15 text-cbn-gold-dark border border-cbn-gold/30',
        'emerald' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'amber' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'rose' => 'bg-rose-50 text-rose-700 border border-rose-200',
        'slate' => 'bg-slate-100 text-slate-700 border border-slate-200',
        default => 'bg-cbn-navy/10 text-cbn-navy border border-cbn-navy/20',
    };

    $dotClasses = match ($variant) {
        'gold' => 'bg-cbn-navy-dark',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'slate' => 'bg-slate-500',
        default => 'bg-cbn-navy',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }} shrink-0" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
