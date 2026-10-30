@extends('layouts.app')

@section('title', 'Contato')
@section('meta_description', 'Endereço, telefone, WhatsApp, e-mail e horário de atendimento da sede da Convenção Batista Nacional do Estado de Goiás.')

@section('content')
<div class="bg-[#FFFDFB]">
    {{-- Cabeçalho Institucional da Página --}}
    <section class="border-b border-neutral-200/80 bg-neutral-50/50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Sede Estadual</span>
                </div>
                <h1 class="font-brand-serif font-bold text-3xl sm:text-4xl lg:text-5xl text-cbn-black tracking-tight">
                    Contato
                </h1>
                <p class="font-brand-sans text-neutral-600 text-base sm:text-lg leading-relaxed">
                    Canais oficiais da secretaria da CBN-GO para dúvidas, requerimentos e atendimento às igrejas filiadas.
                </p>
            </div>
        </div>
    </section>

    {{-- Dados oficiais de contato da sede estadual (Sub-issue 7.3) --}}
    <section class="py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm space-y-3 md:col-span-2 lg:col-span-1">
                <h2 class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Endereço</h2>
                <address class="not-italic text-neutral-800 leading-relaxed">
                    @foreach ($info->addressLines() as $line)
                        {{ $line }}@if (! $loop->last)<br>@endif
                    @endforeach
                </address>
                <a
                    href="{{ $info->mapsUrl() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-cbn-orange-ink hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded-sm"
                >
                    Como chegar (Google Maps) &rarr;
                </a>
            </div>

            <div class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm space-y-3">
                @if ($info->phoneHref() !== null)
                    <h2 class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Telefone</h2>
                    <a href="{{ $info->phoneHref() }}" class="block text-lg font-semibold text-cbn-black hover:text-cbn-orange-ink transition">{{ $info->phone() }}</a>
                @endif
                @if ($info->whatsappUrl() !== null)
                    <h2 class="pt-2 text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">WhatsApp</h2>
                    <a href="{{ $info->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="block text-lg font-semibold text-cbn-black hover:text-cbn-orange-ink transition">{{ $info->whatsapp() }}</a>
                @endif
            </div>

            <div class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm space-y-3">
                <h2 class="text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">E-mail</h2>
                <a href="mailto:{{ $info->email() }}" class="block text-lg font-semibold text-cbn-black hover:text-cbn-orange-ink transition break-all">{{ $info->email() }}</a>
                @if ($info->officeHours() !== null)
                    <h2 class="pt-2 text-xs uppercase font-bold tracking-wider text-cbn-orange-ink">Expediente</h2>
                    <p class="text-neutral-800">{{ $info->officeHours() }}</p>
                @endif
            </div>
        </div>

        @if ($info->socialLinks() !== [])
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 mt-10">
                <h2 class="font-brand-serif font-bold text-xl text-cbn-black mb-4">Redes sociais</h2>
                <ul class="flex flex-wrap gap-3">
                    @foreach ($info->socialLinks() as $network => $link)
                        <li>
                            <a
                                href="{{ $link['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-4 py-1.5 text-sm text-neutral-700 hover:border-cbn-orange hover:text-cbn-orange-ink transition"
                            >
                                <x-social-icon :network="$network" class="w-4 h-4" />
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
</div>
@endsection
