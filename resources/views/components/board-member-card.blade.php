@props([
    'member',
    'heading' => 'h3',
])

@php
    $heading = in_array($heading, ['h3', 'h4'], true) ? $heading : 'h3';
@endphp

<article class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm hover:border-neutral-300 transition text-center flex flex-col items-center">
    <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow ring-1 ring-neutral-200 bg-neutral-50 shrink-0">
        <x-media-image
            :path="$member->photo"
            :alt="'Foto de ' . $member->getDisplayName()"
            variant="thumb"
            fallback-variant="avatar"
            :fallback-initials="$member->getInitials()"
            data-testid="board-member-initials"
            class="w-full h-full rounded-full"
        />
    </div>

    <{{ $heading }} class="mt-5 font-brand-serif font-bold text-lg text-cbn-black leading-snug">{{ $member->getDisplayName() }}</{{ $heading }}>
    <p class="mt-1 text-sm font-semibold text-cbn-orange-ink">{{ $member->role }}</p>

    @if (! empty($member->term_label))
        <p class="mt-3 text-[11px] uppercase tracking-wider text-neutral-500">{{ $member->term_label }}</p>
    @endif
</article>
