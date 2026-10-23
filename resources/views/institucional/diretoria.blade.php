@extends('layouts.app')

@section('title', 'Diretoria — CBN Goiás')

@section('content')
<div class="bg-[#FFFDFB]">
    {{-- Cabeçalho Institucional da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange">Institucional</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Diretoria e Órgãos da Convenção
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    Conheça os servos eleitos em assembleia para conduzir a CBN-GO e acompanhar as igrejas em cada região do estado.
                </p>
            </div>
        </div>
    </section>

    {{-- Galeria da Diretoria Atual agrupada por órgão (Sub-issue 7.2) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-14 sm:py-20 space-y-16">
        @forelse ($groups as $key => $group)
            <section data-board-group="{{ $key }}" aria-labelledby="grupo-{{ $key }}" class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="inline-block w-6 h-0.5 bg-cbn-orange" aria-hidden="true"></span>
                    <h2 id="grupo-{{ $key }}" class="font-brand-serif font-bold text-2xl text-cbn-black">{{ $group['label'] }}</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($group['members'] as $member)
                        <x-board-member-card :member="$member" />
                    @endforeach
                </div>
            </section>
        @empty
            <div data-testid="board-empty" class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                A composição da diretoria atual será publicada em breve.
            </div>
        @endforelse
    </div>

    <section class="border-t border-neutral-200/80 bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-brand-serif font-bold text-xl text-cbn-black">Fale com a sede estadual</h2>
                <p class="text-sm text-neutral-600">Telefone, WhatsApp, e-mail e horário de expediente.</p>
            </div>
            <x-button variant="primary" href="{{ route('contato') }}">Ver contatos</x-button>
        </div>
    </section>
</div>
@endsection
