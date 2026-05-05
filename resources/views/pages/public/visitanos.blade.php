<x-layouts::public :title="__('Visítanos')">

    @php
    $galeria = [
        [
            'url'   => 'https://images.unsplash.com/photo-1616614992443-72324b4f83c6?w=800&auto=format&fit=crop&q=80',
            'alt'   => 'Florería',
            'autor' => 'Maryna Nikolaieva',
        ],
        [
            'url'   => 'https://images.unsplash.com/photo-1594843225243-0a7ed5242c5a?w=800&auto=format&fit=crop&q=80',
            'alt'   => 'Mesa con flores',
            'autor' => 'Camila Diotisalvi',
        ],
        [
            'url'   => 'https://images.unsplash.com/photo-1615488913817-095134dfeb54?w=800&auto=format&fit=crop&q=80',
            'alt'   => 'Flores en estante',
            'autor' => 'Christian Mackie',
        ],
        [
            'url'   => 'https://images.unsplash.com/photo-1619707046314-e76ae25d5ab3?w=800&auto=format&fit=crop&q=80',
            'alt'   => 'Ramo blanco y morado',
            'autor' => 'Sonia Fotograf',
        ],
        [
            'url'   => 'https://images.unsplash.com/photo-1706796204047-42799881e989?w=800&auto=format&fit=crop&q=80',
            'alt'   => 'Flores rosas y blancas',
            'autor' => 'Valentina D',
        ],
        [
            'url'   => 'https://plus.unsplash.com/premium_photo-1672848397531-64eaebfae754?w=800&auto=format&fit=crop&q=80',
            'alt'   => 'Mujer en florería',
            'autor' => 'Yunus Tuğ',
        ],
    ];
    @endphp


    {{-- ═══════════════════════════════════════════
         HERO — Full screen con info de contacto
    ════════════════════════════════════════════ --}}
    <section class="relative h-screen overflow-hidden">

        <img src="{{ asset('images/tienda.jpg') }}"
             alt="1310 Studio — Nuestra tienda"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>

        {{-- Contenido del hero --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8 gap-6">
            <p class="text-[11px] tracking-[0.3em] uppercase text-white/60">Encuéntranos</p>
            <h1 class="font-serif text-6xl md:text-8xl text-white font-light leading-none">Visítanos</h1>
            <p class="text-white/70 font-light text-sm max-w-sm leading-relaxed mt-2">
                Te esperamos en la Colonia Roma Norte.<br>
                Un espacio diseñado para que te enamores de nuestras flores.
            </p>

            {{-- Info rápida --}}
            <div class="flex flex-col sm:flex-row items-center gap-6 mt-4 text-white/80 text-xs tracking-[0.1em]">
                <div class="flex items-center gap-2">
                    <flux:icon name="map-pin" class="w-4 h-4 text-primary" />
                    <span>Orizaba 78, Roma Norte · CDMX</span>
                </div>
                <div class="hidden sm:block w-px h-4 bg-white/30"></div>
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="w-4 h-4 text-primary" />
                    <span>Lun–Vie 9–19 · Sáb 9–15</span>
                </div>
                <div class="hidden sm:block w-px h-4 bg-white/30"></div>
                <div class="flex items-center gap-2">
                    <flux:icon name="phone" class="w-4 h-4 text-primary" />
                    <span>+52 55 1234 5678</span>
                </div>
            </div>

            {{-- CTA WhatsApp --}}
            <a href="https://wa.me/5212345678?text={{ urlencode('Hola, quiero visitarlos. ¿Tienen disponibilidad hoy?') }}"
               target="_blank"
               class="mt-4 inline-block bg-primary text-on-primary px-12 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-500">
                Escribirnos por WhatsApp
            </a>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 text-white/50">
            <span class="text-[9px] tracking-[0.2em] uppercase">Scroll</span>
            <div class="w-px h-10 bg-white/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1/2 bg-white/60 animate-bounce"></div>
            </div>
        </div>

    </section>


    {{-- ═══════════════════════════════════════════
         CARRUSEL GALERÍA — Infinite scroll + lightbox
    ════════════════════════════════════════════ --}}
    <section class="py-0 overflow-hidden bg-surface"
             x-data="{
                 lightbox: false,
                 activa: 0,
                 total: {{ count($galeria) }},
                 open(i) { this.activa = i; this.lightbox = true; },
                 next() { this.activa = (this.activa + 1) % this.total; },
                 prev() { this.activa = (this.activa - 1 + this.total) % this.total; },
             }"
             @keydown.escape.window="lightbox = false"
             @keydown.arrow-right.window="if(lightbox) next()"
             @keydown.arrow-left.window="if(lightbox) prev()">

        {{-- Carrusel --}}
        <div class="w-full overflow-hidden">
            <div class="flex w-max gap-1"
                 style="animation: scroll-visitanos 30s linear infinite;"
                 @mouseenter="$el.style.animationPlayState='paused'"
                 @mouseleave="$el.style.animationPlayState='running'">

                @foreach ([$galeria, $galeria] as $gi => $grupo)
                    @foreach ($grupo as $i => $foto)
                        <button @click="open({{ $i }})"
                                class="flex-shrink-0 overflow-hidden cursor-zoom-in group"
                                style="width: 280px; height: 280px;">
                            <img src="{{ $foto['url'] }}"
                                 alt="{{ $foto['alt'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        </button>
                    @endforeach
                @endforeach

            </div>
        </div>

        {{-- Lightbox --}}
        <div x-show="lightbox"
             x-transition:enter="transition duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[300] bg-black/95 flex items-center justify-center"
             @click.self="lightbox = false"
             style="display: none;">

            {{-- Cerrar --}}
            <button @click="lightbox = false"
                    class="absolute top-6 right-6 text-white/60 hover:text-white transition-colors z-10">
                <flux:icon name="x-mark" class="w-8 h-8" />
            </button>

            {{-- Contador --}}
            <div class="absolute top-6 left-1/2 -translate-x-1/2 text-white/40 text-[10px] tracking-[0.2em] uppercase">
                <span x-text="activa + 1"></span> / {{ count($galeria) }}
            </div>

            {{-- Imagen --}}
            @foreach ($galeria as $i => $foto)
                <div x-show="activa === {{ $i }}" class="max-w-4xl w-full px-16">
                    <img src="{{ $foto['url'] }}"
                         alt="{{ $foto['alt'] }}"
                         class="w-full max-h-[80vh] object-contain select-none">
                    <p class="text-white/30 text-[9px] tracking-[0.15em] uppercase text-center mt-3">
                        Foto: {{ $foto['autor'] }} — Unsplash
                    </p>
                </div>
            @endforeach

            {{-- Flechas --}}
            <button @click.stop="prev()"
                    class="absolute left-6 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition-colors">
                <flux:icon name="chevron-left" class="w-10 h-10" />
            </button>
            <button @click.stop="next()"
                    class="absolute right-6 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition-colors">
                <flux:icon name="chevron-right" class="w-10 h-10" />
            </button>

        </div>

    </section>


    {{-- ═══════════════════════════════════════════
         INFO — Horarios y contacto
    ════════════════════════════════════════════ --}}
    <section class="py-24 px-8 max-w-[1440px] mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16">

            <div class="space-y-3">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Dirección</p>
                <p class="font-serif text-2xl text-on-surface">Orizaba 78</p>
                <p class="text-on-surface-variant font-light text-sm">Colonia Roma Norte, Cuauhtémoc</p>
                <p class="text-on-surface-variant font-light text-sm">Ciudad de México, CDMX 06700</p>
            </div>

            <div class="space-y-3">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Horarios</p>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm border-b border-outline-variant py-2">
                        <span class="text-on-surface-variant font-light">Lunes – Viernes</span>
                        <span class="text-on-surface">9:00 – 19:00</span>
                    </div>
                    <div class="flex justify-between text-sm border-b border-outline-variant py-2">
                        <span class="text-on-surface-variant font-light">Sábado</span>
                        <span class="text-on-surface">9:00 – 15:00</span>
                    </div>
                    <div class="flex justify-between text-sm py-2">
                        <span class="text-on-surface-variant font-light">Domingo</span>
                        <span class="text-on-surface-variant italic">Cerrado</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Contacto</p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <flux:icon name="phone" class="w-4 h-4 text-outline shrink-0" />
                        <span class="text-on-surface-variant font-light text-sm">+52 55 1234 5678</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <flux:icon name="envelope" class="w-4 h-4 text-outline shrink-0" />
                        <span class="text-on-surface-variant font-light text-sm">hola@1310studio.mx</span>
                    </div>
                </div>
                <div class="pt-2">
                    <a href="https://wa.me/5212345678"
                       target="_blank"
                       class="inline-block bg-primary text-on-primary px-8 py-3 text-xs tracking-[0.2em] uppercase hover:opacity-90 transition-all duration-300">
                        WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         MAPA — Full width
    ════════════════════════════════════════════ --}}
    <div class="w-full h-[500px]">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.2!2d-99.1611!3d19.4195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff1c1cd2cc19%3A0x5d8fe30f5a4c8e75!2sOrizaba%2078%2C%20Roma%20Nte.%2C%20Cuauht%C3%A9moc%2C%2006700%20Ciudad%20de%20M%C3%A9xico%2C%20CDMX!5e0!3m2!1ses!2smx!4v1"
            width="100%"
            height="100%"
            style="border:0; display:block;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    {{-- Créditos --}}
    <div class="py-3 px-8 text-center bg-surface-container-low">
        <p class="text-[9px] tracking-[0.1em] uppercase text-outline/50">
            Fotografías de muestra: Maryna Nikolaieva, Camila Diotisalvi, Christian Mackie, Sonia Fotograf, Valentina D, Yunus Tuğ — Unsplash
        </p>
    </div>

    <style>
        @keyframes scroll-visitanos {
            0%   { transform: translateX(0); }
            100% { transform: translateX(calc(-281px * {{ count($galeria) }}))); }
        }
    </style>

</x-layouts::public>