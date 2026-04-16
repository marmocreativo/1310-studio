<x-layouts::public :title="__('Nuestro Estudio')">

    {{-- HERO ESTUDIO --}}
    <section class="relative bg-zinc-900 text-white py-32 overflow-hidden">
        <img src="https://placehold.co/1600x600/1c1917/ffffff?text=Nuestro+Estudio"
             alt="Estudio"
             class="absolute inset-0 w-full h-full object-cover opacity-30">
        <div class="relative z-10 text-center px-6">
            <p class="text-sm uppercase tracking-widest text-zinc-400 mb-4">Desde 2015</p>
            <h1 class="text-5xl md:text-6xl font-light">Nuestro <span class="italic">Estudio</span></h1>
        </div>
    </section>

    {{-- HISTORIA --}}
    <section class="py-20 bg-white dark:bg-zinc-900">
        <flux:container>
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-sm uppercase tracking-widest text-zinc-400 mb-4">Quiénes somos</p>
                    <flux:heading size="xl" level="2" class="mb-6">
                        Un espacio donde las flores cobran vida
                    </flux:heading>
                    <flux:text class="text-zinc-500 leading-relaxed mb-4">
                        Somos un estudio floral independiente fundado en la Colonia Roma de la Ciudad de México.
                        Nació de la pasión por las flores naturales y el diseño contemporáneo, combinando
                        técnicas tradicionales con una estética fresca y moderna.
                    </flux:text>
                    <flux:text class="text-zinc-500 leading-relaxed mb-4">
                        Cada arreglo que creamos es único. Trabajamos con flores de temporada seleccionadas
                        directamente del mercado Jamaica para garantizar frescura y calidad en cada pieza.
                    </flux:text>
                    <flux:text class="text-zinc-500 leading-relaxed">
                        Nuestro equipo de diseñadores florales está listo para ayudarte a encontrar la
                        propuesta perfecta, ya sea para una ocasión especial o simplemente para alegrar tu espacio.
                    </flux:text>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://placehold.co/600x700/f9a8d4/831843?text=Estudio"
                         alt="Estudio" class="rounded-2xl object-cover w-full h-64">
                    <img src="https://placehold.co/600x700/dcfce7/14532d?text=Taller"
                         alt="Taller" class="rounded-2xl object-cover w-full h-64 mt-8">
                </div>
            </div>
        </flux:container>
    </section>

    {{-- VALORES --}}
    <section class="py-20 bg-zinc-50 dark:bg-zinc-800">
        <flux:container>
            <div class="text-center mb-14">
                <flux:heading size="xl" level="2">Lo que nos define</flux:heading>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ([
                    ['Frescura', 'Seleccionamos flores de temporada cada mañana en el Mercado Jamaica para garantizar que cada arreglo llegue en su mejor estado.'],
                    ['Diseño único', 'No repetimos arreglos. Cada pieza se crea pensando en la persona, el momento y el espacio donde vivirá.'],
                    ['Compromiso', 'Creemos en el comercio justo con los productores locales y en el uso responsable de materiales sostenibles.'],
                ] as $valor)
                    <div class="text-center px-4">
                        <div class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 mx-auto mb-5 flex items-center justify-center">
                            <flux:icon name="sparkles" class="text-pink-500 w-6 h-6" />
                        </div>
                        <flux:heading level="3" class="mb-3">{{ $valor[0] }}</flux:heading>
                        <flux:text class="text-zinc-500 leading-relaxed">{{ $valor[1] }}</flux:text>
                    </div>
                @endforeach
            </div>
        </flux:container>
    </section>

    {{-- VISÍTANOS --}}
    <section class="py-20 bg-white dark:bg-zinc-900">
        <flux:container>
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-sm uppercase tracking-widest text-zinc-400 mb-4">Encuéntranos</p>
                    <flux:heading size="xl" level="2" class="mb-6">Visita nuestro estudio</flux:heading>
                    <flux:text class="text-zinc-500 mb-8">
                        Te esperamos con gusto. Puedes venir a explorar nuestra colección, hacer un pedido
                        personalizado o simplemente inspirarte con las flores del día.
                    </flux:text>

                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <flux:icon name="map-pin" class="text-pink-500 mt-0.5 shrink-0" />
                            <div>
                                <flux:heading level="4" class="mb-1">Dirección</flux:heading>
                                <flux:text class="text-zinc-500">Orizaba 78, Colonia Roma Norte<br>Cuauhtémoc, CDMX, C.P. 06700</flux:text>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <flux:icon name="clock" class="text-pink-500 mt-0.5 shrink-0" />
                            <div>
                                <flux:heading level="4" class="mb-1">Horario</flux:heading>
                                <flux:text class="text-zinc-500">Lunes a Viernes: 9:00 — 19:00 hrs<br>Sábado: 9:00 — 15:00 hrs<br>Domingo: cerrado</flux:text>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <flux:icon name="phone" class="text-pink-500 mt-0.5 shrink-0" />
                            <div>
                                <flux:heading level="4" class="mb-1">Teléfono</flux:heading>
                                <flux:text class="text-zinc-500">+52 55 1234 5678</flux:text>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <flux:icon name="envelope" class="text-pink-500 mt-0.5 shrink-0" />
                            <div>
                                <flux:heading level="4" class="mb-1">Correo</flux:heading>
                                <flux:text class="text-zinc-500">hola@1310studio.mx</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mapa placeholder --}}
                <div class="rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-700 h-96 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <img src="https://placehold.co/800x600/e4e4e7/71717a?text=Mapa+proximamente"
                         alt="Mapa" class="w-full h-full object-cover">
                </div>
            </div>
        </flux:container>
    </section>

</x-layouts::public>