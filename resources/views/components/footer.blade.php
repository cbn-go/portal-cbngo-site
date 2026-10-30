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
                        @foreach ($institutional->socialLinks() as $network => $link)
                            <a
                                href="{{ $link['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $link['label'] }} da CBN Goiás"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-cbn-orange-ink hover:text-white flex items-center justify-center text-neutral-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                            >
                                <x-social-icon :network="$network" class="w-4 h-4" />
                            </a>
                        @endforeach
                        @if ($institutional->whatsappUrl() !== null)
                            <a
                                href="{{ $institutional->whatsappUrl() }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="WhatsApp da CBN Goiás"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-cbn-orange-ink hover:text-white flex items-center justify-center text-neutral-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cbn-orange"
                            >
                                <x-social-icon network="whatsapp" class="w-4 h-4" />
                            </a>
                        @endif
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
                    <li><a href="#amigo" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Associações Missionárias Goianas (AMIGO)</a></li>
                    <li><a href="#ormiban" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Ordem dos Pastores (ORMIBAN)</a></li>
                    <li><a href="#ufmbn" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">União Feminina (UFBN)</a></li>
                    <li><a href="#juban" class="text-neutral-400 hover:text-white transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded">Juventude Batista Nacional (JUBAN)</a></li>
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
                        <span>
                            @foreach ($institutional->addressLines() as $line)
                                {{ $line }}@if (! $loop->last)<br>@endif
                            @endforeach
                        </span>
                    </p>
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-cbn-orange shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $institutional->email() }}" class="hover:text-white focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded transition">{{ $institutional->email() }}</a>
                    </p>
                    @if ($institutional->phoneHref() !== null)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cbn-orange shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="{{ $institutional->phoneHref() }}" class="hover:text-white focus:outline-none focus-visible:ring-1 focus-visible:ring-cbn-orange rounded transition">{{ $institutional->phone() }}</a>
                        </p>
                    @endif
                    @if ($institutional->officeHours() !== null)
                        <p class="text-xs text-neutral-400 pt-1">
                            Atendimento: {{ $institutional->officeHours() }}
                        </p>
                    @endif
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
