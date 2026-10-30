@php
    $contact = config('cbngo.contact');
    $social = config('cbngo.social');
    $whatsappUrl = \App\Support\ContactLinks::whatsapp((string) $contact['whatsapp']);
@endphp

<footer class="w-full bg-neutral-950 text-neutral-300 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-8 pb-12 border-b border-neutral-800/80">
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="inline-block focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange rounded">
                    <img
                        src="{{ asset('images/brand/cbn-go-on-dark.png') }}"
                        alt="CBN | GO — Convenção Batista Nacional Goiás"
                        width="160"
                        height="62"
                        class="h-11 w-auto"
                    />
                </a>
                <p class="text-sm leading-relaxed text-neutral-400 max-w-sm font-brand-sans">
                    Convenção Batista Nacional do Estado de Goiás. Unimos igrejas para cuidar de pessoas, fortalecer famílias e gerar transformação social com dignidade e esperança.
                </p>

                <div class="pt-2 space-y-2">
                    <span class="block text-xs font-semibold text-neutral-300 tracking-wider uppercase">
                        Canais & Redes Sociais
                    </span>
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ $social['instagram'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Instagram da CBN Goiás"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-cbn-orange-ink hover:text-white flex items-center justify-center text-neutral-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Instagram</span>
                        </a>
                        <a
                            href="{{ $social['facebook'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Facebook da CBN Goiás"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-cbn-orange-ink hover:text-white flex items-center justify-center text-neutral-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Facebook</span>
                        </a>
                        <a
                            href="{{ $social['youtube'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Canal do YouTube da CBN Goiás"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-cbn-orange-ink hover:text-white flex items-center justify-center text-neutral-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 9v6l5.194-3Z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">YouTube</span>
                        </a>
                        <a
                            href="{{ $whatsappUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="WhatsApp da CBN Goiás"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-cbn-orange-ink hover:text-white flex items-center justify-center text-neutral-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06s-1.05-.39-2-1.23c-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.48c-.16 0-.43.06-.66.31-.22.25-.86.84-.86 2.06 0 1.21.88 2.39 1.01 2.56.12.17 1.74 2.66 4.22 3.73.59.25 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.17-.48-.29"/>
                            </svg>
                            <span class="sr-only">WhatsApp</span>
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <span class="inline-block w-2.5 h-0.5 bg-cbn-orange" aria-hidden="true"></span>
                    <span class="text-xs uppercase font-medium tracking-wider text-neutral-400">Goiânia — Goiás — Brasil</span>
                </div>
            </div>

            <div>
                <h2 class="font-semibold text-white text-sm uppercase tracking-wider mb-4 font-brand-serif">
                    Institucional
                </h2>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Página Inicial</a></li>
                    <li><a href="{{ route('quem-somos') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Quem Somos</a></li>
                    <li><a href="{{ route('diretoria') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Diretoria Executiva</a></li>
                    <li><a href="{{ route('estatuto.show') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Estatuto</a></li>
                    <li><a href="{{ route('igrejas') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Igrejas Filiadas</a></li>
                    <li><a href="{{ route('projetos-sociais.index') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Projetos Sociais</a></li>
                    <li><a href="{{ route('noticias.index') }}" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Notícias e Comunicados</a></li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-white text-sm uppercase tracking-wider mb-4 font-brand-serif">
                    Departamentos
                </h2>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="https://setebango.com.br" target="_blank" rel="noopener noreferrer" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">
                            SETEBAN-GO
                        </a>
                    </li>
                    <li><a href="#jami" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Secretaria de Missões (SEM-GO)</a></li>
                    <li><a href="#ormiban" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Ordem dos Pastores (ORMIBAN)</a></li>
                    <li><a href="#ufmbn" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">União Feminina (UFBN)</a></li>
                    <li><a href="#jubang" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Juventude Conectada (JUBAN)</a></li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-white text-sm uppercase tracking-wider mb-4 font-brand-serif">
                    Sede Administrativa
                </h2>
                <address class="not-italic text-sm text-neutral-400 space-y-3 leading-relaxed">
                    <p class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-cbn-orange shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $contact['address'] }}</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-cbn-orange shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $contact['email'] }}" class="hover:text-white focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded transition">{{ $contact['email'] }}</a>
                    </p>
                    <p class="text-xs text-neutral-400 pt-1">
                        Atendimento: {{ $contact['office_hours'] }}
                    </p>
                    <a href="{{ route('contato') }}" class="inline-block text-xs font-semibold text-cbn-orange hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">
                        Todos os canais de contato &rarr;
                    </a>
                </address>
            </div>
        </div>

        <div class="mt-8 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-neutral-400">
            <p>
                &copy; {{ date('Y') }} Convenção Batista Nacional do Estado de Goiás. Todos os direitos reservados.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                <a href="{{ route('quem-somos') }}" class="hover:text-white transition-colors">Institucional</a>
                <span class="text-neutral-700" aria-hidden="true">·</span>
                <a href="https://setebango.com.br" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">SETEBAN-GO</a>
                <span class="text-neutral-700" aria-hidden="true">·</span>
                <a href="{{ route('igrejas') }}" class="hover:text-white transition-colors">Igrejas Filiadas</a>
                <span class="text-neutral-700" aria-hidden="true">·</span>
                <a href="{{ route('contato') }}" class="hover:text-white transition-colors">Contato</a>
            </div>
        </div>
    </div>
</footer>
