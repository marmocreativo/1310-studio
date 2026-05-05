<x-layouts::public :title="__('Eventos')">

    @php
    $fotos = [
        [
            'url'    => 'https://images.unsplash.com/photo-1521543832500-49e69fb2bea2?w=1200&auto=format&fit=crop&q=80',
            'alt'    => 'Ramo de novia',
            'autor'  => 'Thomas AE',
        ],
        [
            'url'    => 'https://images.unsplash.com/photo-1632528011905-54e2464961f4?w=1200&auto=format&fit=crop&q=80',
            'alt'    => 'Mesa con flores y copas',
            'autor'  => 'José León',
        ],
        [
            'url'    => 'https://images.unsplash.com/photo-1769812343362-39484bff6db4?w=1200&auto=format&fit=crop&q=80',
            'alt'    => 'Mesa larga con flores blancas',
            'autor'  => 'Jonathan Borba',
        ],
        [
            'url'    => 'https://images.unsplash.com/photo-1680131273996-529cc8bbdad6?w=1200&auto=format&fit=crop&q=80',
            'alt'    => 'Mesa de banquete con jarrón floral',
            'autor'  => 'Stephanie Klepacki',
        ],
        [
            'url'    => 'https://images.unsplash.com/photo-1639986098217-17112e22f1ed?w=1200&auto=format&fit=crop&q=80',
            'alt'    => 'Arco floral en playa',
            'autor'  => 'Nadiia Ganzhyi',
        ],
    ];
    @endphp


    {{-- ═══════════════════════════════════════════
         HERO — Full width editorial
    ════════════════════════════════════════════ --}}
    <section class="relative h-[70vh] overflow-hidden">
        <img src="{{ $fotos[2]['url'] }}"
             alt="Eventos 1310 Studio"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
            <p class="text-[11px] tracking-[0.3em] uppercase text-white/70 mb-4">Diseño floral para</p>
            <h1 class="font-serif text-6xl md:text-8xl text-white font-light leading-none">Eventos</h1>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         INTRO — Copy editorial
    ════════════════════════════════════════════ --}}
    <section class="py-24 px-8 max-w-[1440px] mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div class="space-y-6">
                <h2 class="font-serif text-4xl text-on-surface leading-tight">
                    Cada evento merece<br><em>una historia floral.</em>
                </h2>
                <p class="text-on-surface-variant font-light leading-relaxed max-w-md">
                    Diseñamos experiencias florales para bodas, recepciones corporativas,
                    cumpleaños y celebraciones íntimas. Cada pieza es concebida desde cero
                    para reflejar la personalidad del evento.
                </p>
                <p class="text-on-surface-variant font-light leading-relaxed max-w-md">
                    Trabajamos contigo desde la conceptualización hasta el montaje,
                    cuidando cada detalle para que el resultado supere tu visión.
                </p>
            </div>
            <div class="aspect-[4/5] overflow-hidden bg-stone-100">
                <img src="{{ $fotos[0]['url'] }}"
                     alt="{{ $fotos[0]['alt'] }}"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000">
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         SERVICIOS — Grid asimétrico
    ════════════════════════════════════════════ --}}
    <section class="bg-surface-container-low py-24">
        <div class="px-8 max-w-[1440px] mx-auto">

            <div class="mb-16 space-y-3">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Lo que ofrecemos</p>
                <h2 class="font-serif text-4xl text-on-surface">Servicios florales</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Tarjeta grande --}}
                <div class="md:col-span-2 relative overflow-hidden bg-stone-200" style="aspect-ratio: 16/9;">
                    <img src="{{ $fotos[1]['url'] }}"
                         alt="{{ $fotos[1]['alt'] }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-8">
                        <div>
                            <h3 class="font-serif italic text-white text-2xl mb-2">Bodas & Ceremonias</h3>
                            <p class="text-white/70 text-sm font-light max-w-sm leading-relaxed">
                                Ramos de novia, arcos florales, centros de mesa y decoración completa del venue.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta pequeña --}}
                <div class="relative overflow-hidden bg-stone-200" style="aspect-ratio: 4/5;">
                    <img src="{{ $fotos[4]['url'] }}"
                         alt="{{ $fotos[4]['alt'] }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-6">
                        <div>
                            <h3 class="font-serif italic text-white text-xl mb-2">Arcos & Estructuras</h3>
                            <p class="text-white/70 text-xs font-light leading-relaxed">
                                Instalaciones florales de gran formato para ceremonias y sesiones fotográficas.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta pequeña 2 --}}
                <div class="relative overflow-hidden bg-stone-200" style="aspect-ratio: 4/5;">
                    <img src="{{ $fotos[3]['url'] }}"
                         alt="{{ $fotos[3]['alt'] }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-6">
                        <div>
                            <h3 class="font-serif italic text-white text-xl mb-2">Banquetes & Cenas</h3>
                            <p class="text-white/70 text-xs font-light leading-relaxed">
                                Centros de mesa, corredores florales y decoración de mesas de gran formato.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta grande 2 --}}
                <div class="md:col-span-2 relative overflow-hidden bg-stone-200" style="aspect-ratio: 16/9;">
                    <img src="{{ $fotos[2]['url'] }}"
                         alt="{{ $fotos[2]['alt'] }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-8">
                        <div>
                            <h3 class="font-serif italic text-white text-2xl mb-2">Eventos Corporativos</h3>
                            <p class="text-white/70 text-sm font-light max-w-sm leading-relaxed">
                                Instalaciones para lanzamientos, cenas de empresa y espacios de trabajo.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         PROCESO — Pasos
    ════════════════════════════════════════════ --}}
    <section class="py-24 px-8 max-w-[1440px] mx-auto">

        <div class="mb-16 text-center space-y-3">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Cómo trabajamos</p>
            <h2 class="font-serif text-4xl text-on-surface">El proceso</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach([
                ['01', 'Consulta', 'Nos reunimos para entender tu visión, el tipo de evento, paleta de color y presupuesto.'],
                ['02', 'Propuesta', 'Diseñamos una propuesta personalizada con referencias visuales y detalle de flores.'],
                ['03', 'Confirmación', 'Aprobamos el diseño, agendamos fechas y aseguramos la disponibilidad de flores.'],
                ['04', 'Montaje', 'Llegamos antes del evento para instalar cada pieza con precisión y cuidado.'],
            ] as $paso)
                <div class="text-center space-y-4">
                    <p class="font-serif text-5xl text-outline-variant">{{ $paso[0] }}</p>
                    <div class="w-8 h-px bg-primary mx-auto"></div>
                    <h3 class="font-serif text-xl text-on-surface">{{ $paso[1] }}</h3>
                    <p class="text-sm text-on-surface-variant font-light leading-relaxed">{{ $paso[2] }}</p>
                </div>
            @endforeach
        </div>

    </section>


    {{-- ═══════════════════════════════════════════
         CTA — Contacto
    ════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden">
        <img src="{{ $fotos[0]['url'] }}"
             alt="Contacto eventos"
             class="w-full h-[50vh] object-cover object-top">
        <div class="absolute inset-0 bg-black/55 flex flex-col items-center justify-center text-center px-8 gap-6">
            <p class="text-[11px] tracking-[0.3em] uppercase text-white/60">¿Tienes un evento en mente?</p>
            <h2 class="font-serif text-4xl md:text-5xl text-white font-light">Hablemos de tu proyecto</h2>
            <a href="https://wa.me/5212345678?text={{ urlencode('Hola, me interesa el servicio de flores para eventos.') }}"
               target="_blank"
               class="inline-block bg-white text-on-surface px-12 py-4 text-xs tracking-[0.3em] uppercase hover:bg-primary hover:text-on-primary transition-all duration-500 mt-2">
                Contactar por WhatsApp
            </a>
        </div>
    </section>


    {{-- Créditos Unsplash (buena práctica) --}}
    <div class="py-3 px-8 bg-surface-container-low text-center">
        <p class="text-[9px] tracking-[0.1em] uppercase text-outline/50">
            Fotografías: Thomas AE, José León, Jonathan Borba, Stephanie Klepacki, Nadiia Ganzhyi — Unsplash
        </p>
    </div>

</x-layouts::public>