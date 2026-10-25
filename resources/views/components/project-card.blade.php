@props([
    'project',
])

@php
    $url = route('projetos-sociais.show', $project->slug);
@endphp

<article data-testid="project-card" {{ $attributes->merge(['class' => 'group flex flex-col bg-[#FFFDFB] border border-neutral-200/90 rounded overflow-hidden hover:border-neutral-400 hover:shadow-md transition-all duration-200']) }}>
    {{-- Link duplicado da capa fica fora da ordem de tabulação; o link acessível é o do título --}}
    <a href="{{ $url }}" tabindex="-1" aria-hidden="true" class="relative block aspect-video w-full overflow-hidden bg-neutral-100 shrink-0">
        <x-media-image
            :path="$project->cover_image"
            :alt="$project->title"
            variant="card"
            fallback-variant="card"
            loading="lazy"
            class="group-hover:scale-105 transition-transform duration-500"
        />

        <span class="absolute top-3 left-3 text-[11px] font-bold tracking-wider uppercase px-2.5 py-1 rounded shadow-sm {{ $project->category->badgeClasses() }}">
            {{ $project->category->value }}
        </span>
    </a>

    <div class="p-6 flex flex-col flex-1 justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2.5">
                @if ($project->is_ongoing)
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                        Em andamento
                    </span>
                @endif
                @if ($project->is_featured)
                    <span class="text-[11px] font-bold uppercase tracking-wider text-cbn-orange-ink">Destaque</span>
                @endif
            </div>

            <h3 class="font-brand-serif font-bold text-lg text-cbn-black leading-snug group-hover:text-cbn-orange-ink transition-colors line-clamp-2">
                <a href="{{ $url }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">
                    {{ $project->title }}
                </a>
            </h3>

            <p class="mt-2 text-xs font-medium text-neutral-500">
                @if ($project->church)
                    {{ $project->church->name }} ·
                @endif
                {{ $project->city }}
            </p>

            <p class="text-neutral-600 text-sm mt-3 leading-relaxed line-clamp-3">
                {{ $project->summary }}
            </p>
        </div>

        <div class="pt-5 mt-4 border-t border-neutral-100">
            <a
                href="{{ $url }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-cbn-orange-ink hover:text-cbn-orange-mid tracking-wide focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded"
            >
                <span>Conheça o projeto<span class="sr-only">: {{ $project->title }}</span></span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>
</article>
