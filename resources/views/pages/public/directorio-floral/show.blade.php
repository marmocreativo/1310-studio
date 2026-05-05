<x-layouts::public :title="$flor->nombre">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('directorio-floral.index') }}" class="hover:text-on-surface transition-colors">Directorio Floral</a>
            <span class="mx-2">·</span>
            {{ $flor->nombre }}
        </p>

        {{-- Layout principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-24">

            {{-- Galería --}}
            <div x-data="{ activa: 0 }">
                <div class="aspect-square overflow-hidden bg-surface-container-low mb-4">
                    @if ($flor->galeria->count())
                        @foreach ($flor->galeria as $i => $img)
                            <img src="{{ Storage::url($img->imagen) }}"
                                 alt="{{ $flor->nombre }}"
                                 x-show="activa === {{ $i }}"
                                 class="w-full h-full object-cover">
                        @endforeach
                    @elseif ($flor->imagen)
                        <img src="{{ Storage::url($flor->imagen) }}"
                             alt="{{ $flor->nombre }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <flux:icon name="photo" class="w-16 h-16 text-outline-variant" />
                        </div>
                    @endif
                </div>

                @if ($flor->galeria->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-1">
                        @foreach ($flor->galeria as $i => $img)
                            <button @click="activa = {{ $i }}"
                                    class="flex-shrink-0 w-20 h-20 overflow-hidden border-2 transition-colors duration-200"
                                    :class="activa === {{ $i }} ? 'border-primary' : 'border-transparent'">
                                <img src="{{ Storage::url($img->imagen) }}"
                                     alt="{{ $flor->nombre }}"
                                     class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col gap-8">

                @if ($flor->categoria)
                    <p class="text-[10px] tracking-[0.2em] uppercase text-primary">{{ $flor->categoria }}</p>
                @endif

                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight italic">
                    {{ $flor->nombre }}
                </h1>

                @if ($flor->descripcion)
                    <p class="text-on-surface-variant font-light leading-relaxed text-lg">
                        {{ $flor->descripcion }}
                    </p>
                @endif

                @if ($flor->contenido)
                    <div class="pt-4 border-t border-outline-variant text-on-surface-variant font-light leading-relaxed text-sm">
                        {!! $flor->contenido !!}
                    </div>
                @endif

                {{-- Productos con esta flor --}}
                @if ($flor->productos->count())
                    <div class="pt-4 border-t border-outline-variant">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">
                            Arreglos que la incluyen
                        </p>
                        <div class="flex flex-col gap-3">
                            @foreach ($flor->productos->take(4) as $producto)
                                <a href="{{ route('productos.show', $producto->slug) }}"
                                   class="flex items-center gap-4 group">
                                    @php $portada = $producto->galeria->first(); @endphp
                                    <div class="w-14 h-14 overflow-hidden bg-surface-container-low flex-shrink-0">
                                        @if ($portada)
                                            <img src="{{ Storage::url($portada->imagen) }}"
                                                 alt="{{ $producto->nombre }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-serif text-on-surface group-hover:text-primary transition-colors duration-300">
                                            {{ $producto->nombre }}
                                        </p>
                                        <p class="text-xs text-on-surface-variant">
                                            ${{ number_format($producto->precio_venta, 2) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Flores relacionadas --}}
        @if ($relacionadas->count())
            <div class="border-t border-outline-variant pt-16">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
                    Flores similares
                </p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($relacionadas as $rel)
                        <a href="{{ route('directorio-floral.show', $rel->slug) }}" class="group block">
                            <div class="aspect-square overflow-hidden bg-surface-container-low mb-3">
                                @if ($rel->imagen)
                                    <img src="{{ Storage::url($rel->imagen) }}"
                                         alt="{{ $rel->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                @endif
                            </div>
                            <p class="font-serif text-sm text-on-surface group-hover:text-primary transition-colors duration-300">
                                {{ $rel->nombre }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</x-layouts::public>