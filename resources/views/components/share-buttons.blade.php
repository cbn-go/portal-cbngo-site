@props([
    'title',
    'url' => null,
    'label' => 'Compartilhar:',
    'copyLabel' => 'Copiar link da página',
    'whatsappOnly' => false, // versão compacta do rodapé das páginas
])

@php
    $shareUrl = $url ?? url()->current();
    $buttonClasses = 'p-2 rounded bg-neutral-100 text-neutral-700 hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    @if ($label)
        <span class="{{ $whatsappOnly ? 'text-xs text-neutral-500 font-medium' : 'text-xs uppercase font-bold tracking-wider text-neutral-500 mr-1 hidden sm:inline' }}">{{ $label }}</span>
    @endif

    {{-- WhatsApp --}}
    <a
        href="https://api.whatsapp.com/send?text={{ rawurlencode($title . ' — ' . $shareUrl) }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Compartilhar no WhatsApp"
        class="{{ $buttonClasses }} hover:bg-[#25D366]"
    >
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06s-1.05-.39-2-1.23c-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.48c-.16 0-.43.06-.66.31-.22.25-.86.84-.86 2.06 0 1.21.88 2.39 1.01 2.56.12.17 1.74 2.66 4.22 3.73.59.25 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.17-.48-.29"/>
        </svg>
    </a>

    @unless ($whatsappOnly)
        {{-- Facebook --}}
        <a
            href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Compartilhar no Facebook"
            class="{{ $buttonClasses }} hover:bg-[#1877F2]"
        >
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
            </svg>
        </a>

        {{-- Twitter / X --}}
        <a
            href="https://twitter.com/intent/tweet?text={{ rawurlencode($title) }}&url={{ rawurlencode($shareUrl) }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Compartilhar no Twitter / X"
            class="{{ $buttonClasses }} hover:bg-black"
        >
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </a>

        {{-- Copiar Link --}}
        <button
            type="button"
            x-data="{
                copied: false,
                async copyLink() {
                    const url = {{ Js::from($shareUrl) }};
                    if (navigator.clipboard && window.isSecureContext) {
                        try {
                            await navigator.clipboard.writeText(url);
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2500);
                            return;
                        } catch (e) {}
                    }
                    const el = document.createElement('textarea');
                    el.value = url;
                    el.setAttribute('readonly', '');
                    el.style.position = 'absolute';
                    el.style.left = '-9999px';
                    document.body.appendChild(el);
                    el.select();
                    try {
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2500);
                    } catch (err) {}
                    document.body.removeChild(el);
                }
            }"
            @click="copyLink()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded border border-neutral-300 text-neutral-700 hover:text-cbn-black hover:bg-neutral-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
            aria-label="{{ $copyLabel }}"
        >
            <svg x-show="!copied" class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span x-show="!copied">Copiar Link</span>
            <span x-show="copied" x-cloak class="text-emerald-700 font-bold">Copiado!</span>
        </button>
    @endunless
</div>
