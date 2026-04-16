<x-layouts::public :title="__('Soluciones Empresariales')">

    {{-- HERO --}}
    <section class="relative bg-zinc-900 text-white py-32 overflow-hidden">
        <img src="https://placehold.co/1600x600/1c1917/ffffff?text=Soluciones+Empresariales"
             alt="Empresas"
             class="absolute inset-0 w-full h-full object-cover opacity-25">
        <div class="relative z-10 text-center px-6">
            <p class="text-sm uppercase tracking-widest text-zinc-400 mb-4">Para empresas</p>
            <h1 class="text-5xl md:text-6xl font-light mb-6">
                Flores que <span class="italic">fortalecen</span> tu marca
            </h1>
            <flux:text class="text-zinc-300 max-w-2xl mx-auto text-lg">
                Diseñamos propuestas florales a medida para empresas, hoteles, restaurantes y
                eventos corporativos. Crea ambientes memorables con nuestra asesoría especializada.
            </flux:text>
        </div>
    </section>

    {{-- SERVICIOS B2B --}}
    <section class="py-20 bg-white dark:bg-zinc-900">
        <flux:container>
            <div class="text-center mb-14">
                <flux:heading size="xl" level="2">Nuestros servicios</flux:heading>
                <flux:text class="mt-3 text-zinc-500">Soluciones pensadas para el mundo empresarial</flux:text>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ([
                    ['Suscripción floral', 'Renueva los arreglos de tu espacio de forma semanal o quincenal. Mantén tu oficina, lobby o restaurante siempre fresco y elegante.', 'arrow-path'],
                    ['Eventos corporativos', 'Diseñamos la propuesta floral completa para tus eventos: cenas de empresa, lanzamientos, convenciones y más.', 'building-office'],
                    ['Regalos institucionales', 'Arreglos personalizados con tu branding para reconocimientos, agradecimientos y fechas especiales con clientes o colaboradores.', 'gift'],
                    ['Bodas y celebraciones', 'Acompañamos cada etapa del proceso: desde la planeación hasta la instalación el día del evento.', 'heart'],
                    ['Decoración de espacios', 'Transformamos lobbies, salones, terrazas y showrooms con instalaciones florales de alto impacto.', 'home'],
                    ['Asesoría personalizada', 'Reunión sin costo para entender tu marca, tu espacio y diseñar la propuesta ideal para ti.', 'chat-bubble-left-right'],
                ] as $servicio)
                    <div class="p-6 rounded-2xl border border-zinc-100 dark:border-zinc-800 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center mb-5">
                            <flux:icon name="{{ $servicio[2] }}" class="text-pink-500 w-6 h-6" />
                        </div>
                        <flux:heading level="3" class="mb-3">{{ $servicio[0] }}</flux:heading>
                        <flux:text class="text-zinc-500 leading-relaxed">{{ $servicio[1] }}</flux:text>
                    </div>
                @endforeach
            </div>
        </flux:container>
    </section>

    {{-- POR QUÉ NOSOTROS --}}
    <section class="py-20 bg-zinc-50 dark:bg-zinc-800">
        <flux:container>
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://placehold.co/600x700/fce7f3/9d174d?text=Evento+1"
                         alt="Evento" class="rounded-2xl w-full h-64 object-cover">
                    <img src="https://placehold.co/600x700/f0fdf4/166534?text=Evento+2"
                         alt="Evento" class="rounded-2xl w-full h-64 object-cover mt-8">
                </div>
                <div>
                    <p class="text-sm uppercase tracking-widest text-zinc-400 mb-4">¿Por qué elegirnos?</p>
                    <flux:heading size="xl" level="2" class="mb-6">
                        Experiencia floral al servicio de tu negocio
                    </flux:heading>
                    <div class="space-y-5">
                        @foreach ([
                            ['Más de 10 años de experiencia', 'Hemos trabajado con hoteles boutique, restaurantes de autor y empresas Fortune 500 en México.'],
                            ['Equipo dedicado', 'Tendrás un asesor asignado que conoce tu marca y da seguimiento personalizado a cada entrega.'],
                            ['Logística puntual', 'Contamos con sistema de entrega propio en CDMX y área metropolitana para garantizar puntualidad.'],
                            ['Facturación', 'Emitimos facturas para personas morales con todos los requisitos fiscales.'],
                        ] as $punto)
                            <div class="flex items-start gap-4">
                                <flux:icon name="check-circle" class="text-pink-500 mt-0.5 shrink-0 w-5 h-5" />
                                <div>
                                    <flux:heading level="4" class="mb-1">{{ $punto[0] }}</flux:heading>
                                    <flux:text class="text-zinc-500">{{ $punto[1] }}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </flux:container>
    </section>

    {{-- CTA CONTACTO --}}
    <section class="py-24 bg-zinc-900 text-white text-center">
        <flux:container>
            <flux:heading size="xl" level="2" class="mb-4 text-white">
                ¿Listo para transformar tu espacio?
            </flux:heading>
            <flux:text class="text-zinc-400 max-w-xl mx-auto mb-10">
                Contáctanos y agenda una asesoría sin costo. Analizamos tu espacio y te presentamos
                una propuesta a la medida de tu empresa.
            </flux:text>
            <div class="flex flex-wrap gap-4 justify-center">
                <flux:button variant="primary">
                    Solicitar cotización
                </flux:button>
                <flux:button variant="ghost" href="{{ route('estudio') }}" wire:navigate
                    class="text-white border-zinc-600 hover:bg-zinc-800">
                    Conocer el estudio
                </flux:button>
            </div>
        </flux:container>
    </section>

</x-layouts::public>