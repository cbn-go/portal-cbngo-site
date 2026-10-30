@props([
    'document',
])

@php
    /** @var \App\Models\Document $document */
    $safeUrl = $document->getSafeFileUrl();
@endphp

<article
    id="documento-{{ $document->slug }}"
    class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm hover:border-neutral-300 transition flex flex-col justify-between gap-5"
>
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-lg bg-cbn-orange/10 border border-cbn-orange/20 text-cbn-orange-ink-dark flex items-center justify-center shrink-0" aria-hidden="true">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="min-w-0 space-y-1.5">
            <span class="inline-block text-[11px] uppercase font-bold tracking-wider text-neutral-500">{{ $document->file_format }}</span>
            <h3 class="font-brand-serif font-bold text-lg text-cbn-black leading-snug">{{ $document->title }}</h3>
            @if (! empty($document->description))
                <p class="text-sm text-neutral-600 leading-relaxed">{{ $document->description }}</p>
            @endif
        </div>
    </div>

    @if ($safeUrl !== null)
        <a
            href="{{ $safeUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center gap-2 rounded border border-cbn-orange text-cbn-orange-ink hover:bg-cbn-orange-ink hover:text-white px-4 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Consultar / baixar
            <span class="sr-only">{{ $document->title }} (abre em nova aba)</span>
        </a>
    @else
        <span class="inline-flex items-center justify-center rounded border border-neutral-200 bg-neutral-50 px-4 py-2 text-sm text-neutral-500">
            Arquivo indisponível
        </span>
    @endif
</article>
