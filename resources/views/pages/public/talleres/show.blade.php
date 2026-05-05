<x-layouts::public :title="$taller->nombre">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('talleres.index') }}" class="hover:text-on-surface transition-colors">Talleres</a>
            <span class="mx-2">·</span>
            {{ $taller->nombre }}
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">

            {{-- Imagen --}}
            <div>
                @if ($taller->imagen)
                    <div class="aspect-[4/3] overflow-hidden bg-surface-container-low">
                        <img src="{{ Storage::url($taller->imagen) }}"
                             alt="{{ $taller->nombre }}"
                             class="w-full h-full object-cover">
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col gap-8">

                @if ($taller->fecha)
                    <div class="inline-flex items-center gap-3">
                        <flux:icon name="calendar" class="w-4 h-4 text-primary" />
                        <p class="text-[11px] tracking-[0.2em] uppercase text-primary">
                            {{ $taller->fecha->isoFormat('dddd D [de] MMMM [de] YYYY · HH:mm [hrs]') }}
                        </p>
                    </div>
                @endif

                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight">
                    {{ $taller->nombre }}
                </h1>

                @if ($taller->detalles)
                    <div class="text-on-surface-variant font-light leading-relaxed">
                        {!! $taller->detalles !!}
                    </div>
                @endif

                {{-- CTA --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="https://wa.me/5212345678?text={{ urlencode('Hola, me interesa el taller: ' . $taller->nombre) }}"
                       target="_blank"
                       class="inline-block bg-primary text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300 text-center">
                        Reservar lugar
                    </a>
                    <a href="{{ route('talleres.index') }}"
                       class="inline-block border border-outline-variant text-on-surface px-10 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface transition-all duration-300 text-center">
                        Ver todos los talleres
                    </a>
                </div>

            </div>
        </div>

    </div>

</x-layouts::public>