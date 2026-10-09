@props([
    'currentPage' => 1,
    'totalPages' => 1,
    'prevUrl' => null,
    'nextUrl' => null,
])

<nav role="navigation" aria-label="Navegação da paginação" class="flex items-center justify-between border-t border-slate-200 px-4 sm:px-0 py-4">
    <div class="-mt-px flex w-0 flex-1">
        @if ($prevUrl)
            <a
                href="{{ $prevUrl }}"
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-slate-500 hover:border-cbn-navy hover:text-cbn-navy transition"
            >
                <svg class="mr-3 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a.75.75 0 01-.75.75H4.66l3.97 3.97a.75.75 0 11-1.06 1.06l-5.25-5.25a.75.75 0 010-1.06l5.25-5.25a.75.75 0 011.06 1.06L4.66 9.25h12.59A.75.75 0 0118 10z" clip-rule="evenodd" />
                </svg>
                Anterior
            </a>
        @else
            <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-slate-300 cursor-not-allowed">
                <svg class="mr-3 h-5 w-5 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a.75.75 0 01-.75.75H4.66l3.97 3.97a.75.75 0 11-1.06 1.06l-5.25-5.25a.75.75 0 010-1.06l5.25-5.25a.75.75 0 011.06 1.06L4.66 9.25h12.59A.75.75 0 0118 10z" clip-rule="evenodd" />
                </svg>
                Anterior
            </span>
        @endif
    </div>

    <div class="hidden md:-mt-px md:flex">
        @for ($i = 1; $i <= max(1, $totalPages); $i++)
            @if ($i == $currentPage)
                <span
                    aria-current="page"
                    class="inline-flex items-center border-t-2 border-cbn-gold px-4 pt-4 text-sm font-bold text-cbn-navy"
                >
                    {{ $i }}
                </span>
            @else
                <a
                    href="?page={{ $i }}"
                    class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition"
                >
                    {{ $i }}
                </a>
            @endif
        @endfor
    </div>

    <div class="-mt-px flex w-0 flex-1 justify-end">
        @if ($nextUrl)
            <a
                href="{{ $nextUrl }}"
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-slate-500 hover:border-cbn-navy hover:text-cbn-navy transition"
            >
                Próxima
                <svg class="ml-3 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M2 10a.75.75 0 01.75-.75h12.59l-3.97-3.97a.75.75 0 011.06-1.06l5.25 5.25a.75.75 0 010 1.06l-5.25 5.25a.75.75 0 11-1.06-1.06l3.97-3.97H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
                </svg>
            </a>
        @else
            <span class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-slate-300 cursor-not-allowed">
                Próxima
                <svg class="ml-3 h-5 w-5 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M2 10a.75.75 0 01.75-.75h12.59l-3.97-3.97a.75.75 0 011.06-1.06l5.25 5.25a.75.75 0 010 1.06l-5.25 5.25a.75.75 0 11-1.06-1.06l3.97-3.97H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </div>
</nav>
