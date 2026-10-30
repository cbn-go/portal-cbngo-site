@props([
    'toc' => [],
])

{{-- Sumário do estatuto: capítulos e seções, com links para os eIds do Akoma Ntoso --}}
<nav aria-label="Sumário do estatuto" {{ $attributes->merge(['class' => 'akn-toc']) }}>
    <ol class="space-y-1">
        @foreach ($toc as $chapter)
            <li>
                <a href="#{{ $chapter['eId'] }}" data-toc-link="{{ $chapter['eId'] }}" class="akn-toc-chapter">
                    <span>{{ $chapter['num'] }}</span>{{ $chapter['heading'] }}
                </a>
                @if (count($chapter['sections']) > 0)
                    <ol>
                        @foreach ($chapter['sections'] as $section)
                            <li>
                                <a href="#{{ $section['eId'] }}" data-toc-link="{{ $section['eId'] }}">{{ str_replace('SEÇÃO', 'Seção', $section['num']) }} · {{ $section['heading'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
