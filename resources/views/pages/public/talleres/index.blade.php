<x-layouts::public :title="__('Talleres')">

    {{-- Hero --}}
    <div class="py-24 px-8 max-w-[1440px] mx-auto text-center">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Aprende con nosotros</p>
        <h1 class="font-serif text-5xl md:text-6xl text-on-surface mb-6">Talleres</h1>
        <p class="text-on-surface-variant font-light max-w-xl mx-auto leading-relaxed">
            Descubre el arte floral en nuestros talleres prácticos. Aprende técnicas de diseño,
            composición y cuidado de flores de la mano de nuestros expertos.
        </p>
    </div>

    <div class="px-8 max-w-[1440px] mx-auto pb-24">

        {{-- Próximos talleres --}}
        @if ($proximos->count())
            <div class="mb-24">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
                    Próximos talleres
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($proximos as $taller)
                        <a href="{{ route('talleres.show', $taller->slug) }}" class="group block">
                            <div class="aspect-[4/3] overflow-hidden bg-surface-container-low mb-5">
                                @if ($taller->imagen)
                                    <img src="{{ Storage::url($taller->imagen) }}"
                                         alt="{{ $taller->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <flux:icon name="academic-cap" class="w-10 h-10 text-outline-variant" />
                                    </div>
                                @endif
                            </div>
                            @if ($taller->fecha)
                                <p class="text-[10px] tracking-[0.15em] uppercase text-primary mb-2">
                                    {{ $taller->fecha->isoFormat('dddd D [de] MMMM · HH:mm') }}
                                </p>
                            @endif
                            <h3 class="font-serif text-xl text-on-surface group-hover:text-primary transition-colors duration-300 mb-2">
                                {{ $taller->nombre }}
                            </h3>
                            <p class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant border-b border-outline-variant pb-1 inline-block group-hover:border-primary group-hover:text-primary transition-colors duration-300">
                                Ver detalles
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Talleres pasados --}}
        @if ($pasados->count())
            <div>
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
                    Talleres anteriores
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($pasados as $taller)
                        <a href="{{ route('talleres.show', $taller->slug) }}" class="group block opacity-70 hover:opacity-100 transition-opacity duration-300">
                            <div class="aspect-[4/3] overflow-hidden bg-surface-container-low mb-5 grayscale group-hover:grayscale-0 transition-all duration-500">
                                @if ($taller->imagen)
                                    <img src="{{ Storage::url($taller->imagen) }}"
                                         alt="{{ $taller->nombre }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            @if ($taller->fecha)
                                <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant mb-2">
                                    {{ $taller->fecha->isoFormat('D [de] MMMM, YYYY') }}
                                </p>
                            @endif
                            <h3 class="font-serif text-on-surface mb-1">{{ $taller->nombre }}</h3>
                        </a>
                    @endforeach
                </div>
                <div class="mt-12">{{ $pasados->links() }}</div>
            </div>
        @endif

        @if ($proximos->isEmpty() && $pasados->isEmpty())
            <div class="py-20 text-center">
                <p class="text-on-surface-variant font-light">No hay talleres disponibles en este momento.</p>
                <a href="{{ route('home') }}"
                   class="inline-block mt-6 text-xs tracking-[0.15em] uppercase border-b border-outline pb-1 text-on-surface">
                    Volver al inicio
                </a>
            </div>
        @endif

    </div>

</x-layouts::public>