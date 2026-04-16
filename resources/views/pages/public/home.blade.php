<x-layouts::public :title="__('Inicio')">

    {{-- HERO --}}
    <section class="relative bg-zinc-900 text-white overflow-hidden" style="min-height: 90vh;">
        <img src="https://placehold.co/1600x900/1a1a1a/ffffff?text=Flores+de+Temporada"
             alt="Hero"
             class="absolute inset-0 w-full h-full object-cover opacity-40">
        <div class="relative z-10 flex flex-col items-center justify-center text-center h-full px-6"
             style="min-height: 90vh;">
            <p class="text-sm uppercase tracking-widest text-zinc-300 mb-4">Estudio floral en Ciudad de México</p>
            <h1 class="text-5xl md:text-7xl font-light leading-tight mb-6">
                Flores que<br><span class="italic">cuentan historias</span>
            </h1>
            <p class="text-lg text-zinc-300 max-w-xl mb-10">
                Diseños únicos para momentos únicos. Desde ramos artesanales hasta instalaciones para eventos.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <flux:button href="{{ route('categorias.index') }}" variant="primary" wire:navigate>
                    Ver colección
                </flux:button>
                <flux:button href="{{ route('estudio') }}" variant="ghost" wire:navigate
                    class="text-white border-white hover:bg-white/10">
                    Nuestro estudio
                </flux:button>
            </div>
        </div>
    </section>

    {{-- CARRUSEL DE PRODUCTOS --}}
    <section class="py-20 bg-white dark:bg-zinc-900">
        <flux:container>
            <div class="text-center mb-12">
                <flux:heading size="xl" level="2">Productos destacados</flux:heading>
                <flux:text class="mt-3 text-zinc-500">Una selección de nuestros arreglos más populares</flux:text>
            </div>

            <div x-data="{ active: 0, items: 4 }" class="relative overflow-hidden">
                <div class="flex gap-6 transition-transform duration-500"
                     :style="`transform: translateX(calc(-${active * (100 / 3)}% - ${active * 8}px))`">
                    @foreach ([
                        ['Ramo Primaveral', '1200x900/f9a8d4/831843'],
                        ['Orquídea Blanca', '1200x900/e0f2fe/0c4a6e'],
                        ['Ramo Silvestre', '1200x900/dcfce7/14532d'],
                        ['Girasoles', '1200x900/fef9c3/713f12'],
                        ['Rosas Rojas', '1200x900/fee2e2/7f1d1d'],
                        ['Tulipanes', '1200x900/ede9fe/4c1d95'],
                    ] as $producto)
                        <div class="min-w-[calc(33.333%-1rem)] md:min-w-[calc(33.333%-1rem)] min-w-[85%] flex-shrink-0">
                            <div class="rounded-2xl overflow-hidden border border-zinc-100 dark:border-zinc-800 group">
                                <div class="overflow-hidden">
                                    <img src="https://placehold.co/{{ $producto[1] }}?text={{ urlencode($producto[0]) }}"
                                         alt="{{ $producto[0] }}"
                                         class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="p-5">
                                    <flux:heading level="3">{{ $producto[0] }}</flux:heading>
                                    <flux:text class="mt-1 text-zinc-500">Desde $350 MXN</flux:text>
                                    <flux:button class="mt-4 w-full" variant="ghost" size="sm">
                                        Ver detalle
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Controles --}}
                <div class="flex justify-center gap-3 mt-8">
                    <flux:button icon="arrow-left" variant="ghost" size="sm"
                        x-on:click="active = active > 0 ? active - 1 : 0" />
                    <flux:button icon="arrow-right" variant="ghost" size="sm"
                        x-on:click="active = active < 3 ? active + 1 : 3" />
                </div>
            </div>
        </flux:container>
    </section>

    {{-- MINI GRID DE CATEGORÍAS --}}
    <section class="py-20 bg-zinc-50 dark:bg-zinc-800">
        <flux:container>
            <div class="text-center mb-12">
                <flux:heading size="xl" level="2">Explora por categoría</flux:heading>
                <flux:text class="mt-3 text-zinc-500">Encuentra el arreglo perfecto para cada ocasión</flux:text>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([
                    ['Ramos', 'f9a8d4/831843', 'ramos'],
                    ['Orquídeas', 'e0f2fe/0c4a6e', 'orquideas'],
                    ['Eventos', 'dcfce7/14532d', 'eventos'],
                    ['Plantas', 'fef9c3/713f12', 'plantas'],
                ] as $cat)
                    <a href="{{ route('categorias.index') }}"
                       class="group relative rounded-2xl overflow-hidden aspect-square">
                        <img src="https://placehold.co/600x600/{{ $cat[1] }}?text={{ urlencode($cat[0]) }}"
                             alt="{{ $cat[0] }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/30 flex items-end p-5">
                            <flux:heading level="3" class="text-white">{{ $cat[0] }}</flux:heading>
                        </div>
                    </a>
                @endforeach
            </div>
        </flux:container>
    </section>

    {{-- CTA FINAL --}}
    <section class="py-20 bg-white dark:bg-zinc-900 text-center">
        <flux:container>
            <flux:heading size="xl" level="2" class="mb-4">¿Tienes un evento especial?</flux:heading>
            <flux:text class="text-zinc-500 max-w-xl mx-auto mb-8">
                Visita nuestro estudio o contáctanos. Creamos propuestas personalizadas para bodas,
                corporativos y cualquier celebración.
            </flux:text>
            <div class="flex flex-wrap gap-4 justify-center">
                <flux:button href="{{ route('estudio') }}" variant="primary" wire:navigate>
                    Visitar estudio
                </flux:button>
                <flux:button href="{{ route('empresas') }}" variant="ghost" wire:navigate>
                    Soluciones empresariales
                </flux:button>
            </div>
        </flux:container>
    </section>

</x-layouts::public>