{{-- RASCUNHO (Issue #7): síntese provisória; substituir pelo texto oficial da Declaração Doutrinária da CBN após revisão da secretaria. --}}
@php
    $articles = [
        ['title' => 'As Escrituras Sagradas', 'text' => 'A Bíblia é a Palavra de Deus inspirada, regra suprema de fé e prática para a vida do crente e da igreja.'],
        ['title' => 'Deus Trino', 'text' => 'Cremos em um só Deus, eternamente existente em três pessoas: Pai, Filho e Espírito Santo.'],
        ['title' => 'Jesus Cristo', 'text' => 'Jesus Cristo é o Filho de Deus, verdadeiro Deus e verdadeiro homem, que morreu pelos nossos pecados, ressuscitou e voltará em glória.'],
        ['title' => 'O Espírito Santo', 'text' => 'O Espírito Santo convence do pecado, regenera, santifica e capacita a igreja com dons para a edificação do Corpo e o cumprimento da missão.'],
        ['title' => 'A Salvação', 'text' => 'A salvação é dom gratuito de Deus, recebida pela graça mediante a fé em Jesus Cristo, e não por obras.'],
        ['title' => 'A Igreja', 'text' => 'A igreja local é a comunidade de pessoas regeneradas e batizadas, autônoma, que se reúne para adoração, comunhão, ensino e missão.'],
        ['title' => 'As Ordenanças', 'text' => 'O batismo por imersão de pessoas convertidas e a Ceia do Senhor são ordenanças memoriais instituídas por Cristo.'],
        ['title' => 'Sacerdócio dos Crentes', 'text' => 'Todo crente tem livre acesso a Deus por meio de Cristo e é chamado a servir com seus dons no Reino.'],
    ];
@endphp

<ol class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    @foreach ($articles as $index => $article)
        <li class="rounded-xl border border-neutral-200/90 bg-white p-5 shadow-sm">
            <span class="text-xs font-bold text-cbn-orange-ink tracking-wider">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
            <h3 class="mt-1 font-brand-serif font-bold text-base text-cbn-black">{{ $article['title'] }}</h3>
            <p class="mt-2 text-sm text-neutral-600 leading-relaxed">{{ $article['text'] }}</p>
        </li>
    @endforeach
</ol>
