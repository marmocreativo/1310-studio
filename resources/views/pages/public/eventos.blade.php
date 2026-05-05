<x-layouts::public :title="__('Eventos')">

    {{-- Hero --}}
    <section class="relative h-[60vh] overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-stone-900">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center px-8 max-w-3xl">
            <p class="text-[11px] tracking-[0.3em] uppercase text-white/70 mb-6">Para momentos que importan</p>
            <h1 class="font-serif text-5xl md:text-6xl text-white italic mb-6">
                Flores para tus eventos
            </h1>
            <p class="text-white/80 font-light text-lg leading-relaxed">
                Transformamos espacios en experiencias. Desde bodas íntimas hasta grandes banquetes corporativos.
            </p>
        </div>
    </section>

    {{-- Servicios --}}
    <section class="py-24 px-8 max-w-[1440px] mx-auto">

        <div class="text-center mb-20">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Lo que hacemos</p>
            <h2 class="font-serif text-4xl text-on-surface">Nuestros servicios para eventos</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            @foreach ([
                ['Bodas', 'heart', 'Creamos la atmósfera perfecta para el día más especial. Desde el bouquet de novia hasta la decoración completa del salón, cada detalle cuenta.'],
                ['Banquetes', 'building-office', 'Centros de mesa, arreglos de bienvenida y decoración de espacios para banquetes y cenas de gala que impresionan.'],
                ['Corporativos', 'briefcase', 'Flores para recepciones, lobbies, salas de juntas y eventos empresariales. Imagen y elegancia que refuerzan tu marca.'],
                ['Cumpleaños', 'gift', 'Arreglos personalizados para celebraciones especiales. Diseños únicos que sorprenden y emocionan.'],
                ['Bautizos y XV años', 'sparkles', 'Decoración floral para los momentos de vida más queridos. Tierna, elegante y memorable.'],
                ['Instalaciones', 'squares-plus', 'Arcos florales, paredes de flores y estructuras decorativas para photobooth y escenarios.'],
            ] as [$titulo, $icono, $desc])
                <div class="flex flex-col gap-4">
                    <div class="w-10 h-10 flex items-center justify-center border border-outline-variant">
                        <flux:icon name="{{ $icono }}" class="w-5 h-5 text-primary" />
                    </div>
                    <h3 class="font-serif text-xl text-on-surface">{{ $titulo }}</h3>
                    <p class="text-on-surface-variant font-light leading-relaxed text-sm">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

    </section>

    {{-- Por qué elegirnos --}}
    <section class="py-24 bg-surface-container-low border-y border-outline-variant">
        <div class="px-8 max-w-[1440px] mx-auto">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-6">Por qué 1310 Studio</p>
                    <h2 class="font-serif text-4xl text-on-surface mb-10 leading-tight">
                        Cada evento merece flores que cuenten su historia.
                    </h2>
                    <div class="space-y-6">
                        @foreach ([
                            ['Consulta personalizada', 'Nos reunimos contigo para entender tu visión, paleta de colores y presupuesto.'],
                            ['Flores de temporada', 'Trabajamos con flores frescas de la mejor calidad, seleccionadas según la época del año.'],
                            ['Montaje profesional', 'Nuestro equipo se encarga de la instalación y desmontaje el día del evento.'],
                            ['Garantía de frescura', 'Todos nuestros arreglos se preparan el mismo día o la víspera del evento.'],
                        ] as [$punto, $desc])
                            <div class="flex gap-4">
                                <div class="w-1 bg-primary flex-shrink-0 mt-1"></div>
                                <div>
                                    <p class="font-medium text-on-surface text-sm mb-1">{{ $punto }}</p>
                                    <p class="text-on-surface-variant font-light text-sm leading-relaxed">{{ $desc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="aspect-[4/3] bg-surface-container flex items-center justify-center">
                    <flux:icon name="photo" class="w-16 h-16 text-outline-variant" />
                </div>
            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24 px-8 max-w-[1440px] mx-auto text-center">
        <h2 class="font-serif text-4xl text-on-surface mb-6">¿Tienes un evento en mente?</h2>
        <p class="text-on-surface-variant font-light max-w-md mx-auto mb-10 leading-relaxed">
            Cuéntanos tu idea. Estaremos encantados de crear una propuesta personalizada para ti.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="https://wa.me/5212345678?text={{ urlencode('Hola, me gustaría cotizar decoración floral para un evento.') }}"
               target="_blank"
               class="inline-block bg-primary text-on-primary px-12 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300">
                Solicitar cotización
            </a>
            <a href="{{ route('visitanos') }}"
               class="inline-block border border-outline-variant text-on-surface px-12 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface transition-all duration-300">
                Visítanos
            </a>
        </div>
    </section>

</x-layouts::public>