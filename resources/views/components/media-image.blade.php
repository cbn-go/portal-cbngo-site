@props([
    'path' => null,
    'alt' => '',
    'variant' => 'card', // card | cover | thumb
    'aspect' => null,
    'loading' => 'lazy',
    'fetchpriority' => null,
    'sizes' => null,
    'fallbackVariant' => 'card',
    'fallbackInitials' => null,
])

@php
    $resolvedUrl = \App\Support\Media::url($path);
    $resolvedSrcset = \App\Support\Media::srcset($path);

    // Definição inteligente de sizes de acordo com a variante quando não explicitado
    if ($sizes === null) {
        $sizes = match ($variant) {
            'cover' => '(max-width: 1024px) 100vw, 1200px',
            'thumb' => '100px',
            default => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px',
        };
    }
@endphp

@if ($resolvedUrl !== null)
    <img
        src="{{ $resolvedUrl }}"
        @if ($resolvedSrcset) srcset="{{ $resolvedSrcset }}" @endif
        sizes="{{ $sizes }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
        {{ $attributes->merge(['class' => 'object-cover w-full h-full']) }}
    />
@else
    <x-media-placeholder
        :variant="$fallbackVariant ?: $variant"
        :initials="$fallbackInitials"
        {{ $attributes }}
    />
@endif
