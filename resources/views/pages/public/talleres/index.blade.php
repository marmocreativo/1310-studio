<x-layouts::public :title="__('Talleres')">

    {{-- ═══════════════════════════════════════════
         HERO
    ════════════════════════════════════════════ --}}
    <section class="py-12 px-8 max-w-[1440px] mx-auto">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-3">Aprende con nosotros</p>
        <h1 class="font-serif text-4xl text-on-surface">Talleres</h1>
    </section>

    <div class="px-8 max-w-[1440px] mx-auto pb-24">

        {{-- ═══════════════════════════════════════════
             PRÓXIMOS — Cartelera
        ════════════════════════════════════════════ --}}
        @if ($proximos->count())
            <div class="mb-24">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10 pb-4 border-b border-outline-variant">
                    Próximos talleres
                </p>

                <div class="relative">

                    {{-- Línea vertical --}}
                    <div class="hidden md:block absolute left-[7.5rem] top-0 bottom-0 w-px bg-outline-variant"></div>

                    <div class="flex flex-col gap-0">
                        @php $mesActual = null; @endphp

                        @foreach ($proximos as $taller)
                            @php $mes = $taller->fecha?->isoFormat('MMMM YYYY'); @endphp

                            {{-- Separador de mes --}}
                            @if ($mes !== $mesActual)
                                @php $mesActual = $mes; @endphp
                                <div class="flex items-center gap-6 mb-6 mt-10 first:mt-0">
                                    <div class="hidden md:block w-[7.5rem] shrink-0"></div>
                                    <div class="hidden md:block w-3 h-3 rounded-full bg-primary ring-4 ring-surface shrink-0 -ml-[calc(0.375rem+0.5px)]"></div>
                                    <p class="text-[10px] tracking-[0.25em] uppercase text-primary font-medium">
                                        {{ $mes }}
                                    </p>
                                </div>
                            @endif

                            {{-- Fila del taller --}}
                            <a href="{{ route('talleres.show', $taller->slug) }}"
                               class="group flex flex-col md:flex-row gap-6 md:gap-0 py-6 border-b border-outline-variant hover:bg-surface-container-low transition-colors duration-300 -mx-4 px-4 rounded">

                                {{-- Fecha --}}
                                <div class="md:w-[7.5rem] shrink-0 flex md:flex-col items-center md:items-end md:pr-8 gap-3 md:gap-0">
                                    @if($taller->fecha)
                                        <div class="text-center md:text-right">
                                            <p class="font-serif text-4xl text-on-surface leading-none">
                                                {{ $taller->fecha->format('d') }}
                                            </p>
                                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant mt-1">
                                                {{ $taller->fecha->isoFormat('ddd') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Punto en la línea --}}
                                <div class="hidden md:flex items-start pt-3 shrink-0 -ml-[calc(0.25rem+0.5px)]">
                                    <div class="w-2 h-2 rounded-full bg-outline-variant group-hover:bg-primary transition-colors duration-300 ring-4 ring-surface"></div>
                                </div>

                                {{-- Imagen + Info --}}
                                <div class="flex gap-6 md:pl-8 flex-1 items-start">

                                    {{-- Imagen --}}
                                    <div class="w-24 h-24 md:w-28 md:h-28 shrink-0 overflow-hidden bg-stone-100">
                                        @if($taller->imagen)
                                            <img src="{{ Storage::url($taller->imagen) }}"
                                                 alt="{{ $taller->nombre }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <flux:icon name="academic-cap" class="w-8 h-8 text-outline-variant" />
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Texto --}}
                                    <div class="flex flex-col gap-2 flex-1">
                                        @if($taller->fecha)
                                            <p class="text-[10px] tracking-[0.15em] uppercase text-primary">
                                                {{ $taller->fecha->isoFormat('HH:mm [hrs]') }}
                                            </p>
                                        @endif
                                        <h3 class="font-serif text-xl text-on-surface group-hover:text-primary transition-colors duration-300 leading-tight">
                                            {{ $taller->nombre }}
                                        </h3>
                                        @if($taller->detalles)
                                            <p class="text-sm text-on-surface-variant font-light leading-relaxed line-clamp-2">
                                                {{ Str::of($taller->detalles)->stripTags()->limit(120) }}
                                            </p>
                                        @endif
                                        <p class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant border-b border-outline-variant pb-0.5 inline-block w-fit mt-1 group-hover:border-primary group-hover:text-primary transition-colors duration-300">
                                            Ver detalles
                                        </p>
                                    </div>

                                </div>

                            </a>

                        @endforeach
                    </div>
                </div>
            </div>
        @endif


        {{-- ═══════════════════════════════════════════
             PASADOS — Grid compacto
        ════════════════════════════════════════════ --}}
        @if ($pasados->count())
            <div>
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10 pb-4 border-b border-outline-variant">
                    Talleres anteriores
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($pasados as $taller)
                        <a href="{{ route('talleres.show', $taller->slug) }}"
                           class="group block relative aspect-square overflow-hidden bg-stone-100 grayscale hover:grayscale-0 transition-all duration-500">
                            @if($taller->imagen)
                                <img src="{{ Storage::url($taller->imagen) }}"
                                     alt="{{ $taller->nombre }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-stone-200"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-4">
                                <div>
                                    @if($taller->fecha)
                                        <p class="text-white/50 text-[9px] tracking-[0.15em] uppercase mb-1">
                                            {{ $taller->fecha->isoFormat('D MMM YYYY') }}
                                        </p>
                                    @endif
                                    <p class="font-serif italic text-white text-sm leading-tight">
                                        {{ $taller->nombre }}
                                    </p>
                                </div>
                            </div>
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