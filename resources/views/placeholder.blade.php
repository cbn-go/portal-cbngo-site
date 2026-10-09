@extends('layouts.app')

@section('title', $title ?? 'Em Breve')

@section('content')
<section class="py-20 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto text-center space-y-6">
    <div class="inline-flex items-center gap-2">
        <x-badge variant="gold" :dot="true">
            Fase 3: Portal Institucional CBN-GO
        </x-badge>
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-brand-serif">
        {{ $title ?? 'Módulo em Desenvolvimento' }}
    </h1>

    @if (!empty($slug))
        <p class="text-xs font-mono text-slate-400 bg-slate-100 py-1 px-3 rounded-full inline-block">
            Item: {{ $slug }}
        </p>
    @endif

    <p class="text-base sm:text-lg text-slate-600 font-brand-sans max-w-2xl mx-auto leading-relaxed">
        Esta seção do portal público da Convenção Batista Nacional do Estado de Goiás está em fase final de homologação.
        O conteúdo oficial e completo estará disponível em breve.
    </p>

    <div class="pt-4">
        <x-button variant="primary" size="md" href="{{ route('home') }}">
            &larr; Voltar para a Página Inicial
        </x-button>
    </div>
</section>
@endsection
