@props([
    'member',
])

@php
    /** @var \App\Models\BoardMember $member */
    $photoUrl = $member->getSafePhotoUrl();
@endphp

<article class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm hover:border-neutral-300 transition text-center flex flex-col items-center">
    @if ($photoUrl !== null)
        <img
            src="{{ $photoUrl }}"
            alt="Foto de {{ $member->name }}"
            class="w-28 h-28 rounded-full object-cover border-4 border-white shadow ring-1 ring-neutral-200 bg-neutral-50"
            loading="lazy"
        />
    @else
        <div
            data-testid="board-member-initials"
            class="w-28 h-28 rounded-full bg-cbn-orange/10 border border-cbn-orange/20 text-cbn-orange font-brand-serif font-bold text-3xl flex items-center justify-center"
            aria-hidden="true"
        >{{ $member->getInitials() }}</div>
    @endif

    <h3 class="mt-5 font-brand-serif font-bold text-lg text-cbn-black leading-snug">{{ $member->name }}</h3>
    <p class="mt-1 text-sm font-semibold text-cbn-orange">{{ $member->role }}</p>

    @if (! empty($member->region))
        <p class="mt-2 inline-flex items-center gap-1 text-xs text-neutral-600">
            <svg class="w-3.5 h-3.5 text-cbn-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $member->region }}
        </p>
    @endif

    @if (! empty($member->term_label))
        <p class="mt-3 text-[11px] uppercase tracking-wider text-neutral-400">{{ $member->term_label }}</p>
    @endif
</article>
