@props([
    'images' => [],
    'title' => '',
    'heading' => 'Galeria de Fotos',
    'description' => 'Registros fotográficos do evento e ação ministerial.',
])

{{-- Galeria de fotos com lightbox (Alpine.js), usada em notícias e projetos sociais --}}
@if (count($images) > 0)
<section
    x-data="{
        isOpen: false,
        currentIndex: 0,
        images: {{ Js::from(array_values($images)) }},
        open(idx) {
            this.currentIndex = idx;
            this.isOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        close() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },
        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        }
    }"
    @keydown.escape.window="close()"
    @keydown.arrow-right.window="if (isOpen) next()"
    @keydown.arrow-left.window="if (isOpen) prev()"
    {{ $attributes->merge(['class' => 'mt-14 pt-10 border-t border-neutral-200']) }}
>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-brand-serif font-bold text-2xl text-cbn-black">
                {{ $heading }}
            </h2>
            <p class="text-xs sm:text-sm text-neutral-500 font-brand-sans">
                {{ $description }}
            </p>
        </div>
        <span class="text-xs text-neutral-500 font-medium">
            {{ count($images) }} {{ count($images) === 1 ? 'foto' : 'fotos' }}
        </span>
    </div>

    {{-- Grid de Miniaturas --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach (array_values($images) as $index => $photoUrl)
            <button
                type="button"
                @click="open({{ $index }})"
                class="group relative aspect-square rounded-lg overflow-hidden bg-neutral-100 border border-neutral-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange transition"
                aria-label="Ver foto {{ $index + 1 }} em tamanho ampliado"
            >
                <x-media-image
                    :path="$photoUrl"
                    :alt="'Foto ' . ($index + 1) . ' - ' . $title"
                    variant="card"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                    </svg>
                </div>
            </button>
        @endforeach
    </div>

    {{-- Modal Lightbox --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-label="Visualizador de fotos"
    >
        {{-- Fechar com clique fora ou botão --}}
        <button
            type="button"
            @click="close()"
            class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
            aria-label="Fechar galeria (Esc)"
        >
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Botão Anterior --}}
        <button
            type="button"
            x-show="images.length > 1"
            @click.stop="prev()"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full hover:bg-white/10 transition z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
            aria-label="Foto anterior"
        >
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        {{-- Imagem em exibição --}}
        <div class="relative max-w-5xl max-h-[85vh] flex flex-col items-center" @click.outside="close()">
            <img
                :src="images[currentIndex]"
                :alt="'Foto ' + (currentIndex + 1)"
                class="max-w-full max-h-[80vh] object-contain rounded shadow-2xl"
            />
            <div class="mt-3 text-center text-xs text-white/70 font-mono">
                <span x-text="currentIndex + 1"></span> de <span x-text="images.length"></span>
            </div>
        </div>

        {{-- Botão Próxima --}}
        <button
            type="button"
            x-show="images.length > 1"
            @click.stop="next()"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full hover:bg-white/10 transition z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
            aria-label="Próxima foto"
        >
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</section>
@endif
