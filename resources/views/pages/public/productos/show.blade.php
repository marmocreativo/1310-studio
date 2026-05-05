<x-layouts::public :title="$producto->nombre">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
            <span class="mx-2">·</span>
            @if ($producto->categorias->first())
                <a href="{{ route('categorias.show', $producto->categorias->first()->slug) }}"
                   class="hover:text-on-surface transition-colors">
                    {{ $producto->categorias->first()->titulo }}
                </a>
                <span class="mx-2">·</span>
            @endif
            {{ $producto->nombre }}
        </p>

        {{-- Layout principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-24">

            {{-- Galería --}}
            <div x-data="{ activa: 0 }">
                {{-- Imagen principal --}}
                <div class="aspect-[3/4] overflow-hidden bg-surface-container-low mb-4">
                    @foreach ($producto->galeria as $i => $img)
                        <img src="{{ Storage::url($img->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             x-show="activa === {{ $i }}"
                             class="w-full h-full object-cover">
                    @endforeach
                    @if ($producto->galeria->isEmpty())
                        <div class="w-full h-full flex items-center justify-center">
                            <flux:icon name="photo" class="w-16 h-16 text-outline-variant" />
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if ($producto->galeria->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-1">
                        @foreach ($producto->galeria as $i => $img)
                            <button @click="activa = {{ $i }}"
                                    class="flex-shrink-0 w-20 h-20 overflow-hidden border-2 transition-colors duration-200"
                                    :class="activa === {{ $i }} ? 'border-primary' : 'border-transparent'">
                                <img src="{{ Storage::url($img->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col gap-8">

                {{-- Categorías --}}
                @if ($producto->categorias->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($producto->categorias as $cat)
                            <a href="{{ route('categorias.show', $cat->slug) }}"
                               class="text-[10px] tracking-[0.15em] uppercase text-primary border-b border-primary/40 hover:border-primary transition-colors duration-300">
                                {{ $cat->titulo }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight">
                    {{ $producto->nombre }}
                </h1>

                {{-- Precio --}}
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl text-on-surface">
                        ${{ number_format($producto->precio_venta, 2) }}
                    </span>
                    @if ($producto->tiene_descuento)
                        <span class="text-lg text-outline line-through">
                            ${{ number_format($producto->precio_lista, 2) }}
                        </span>
                        <span class="text-xs tracking-[0.1em] uppercase text-tertiary">
                            -{{ $producto->porcentaje_descuento }}% descuento
                        </span>
                    @endif
                </div>

                {{-- Descripción --}}
                @if ($producto->descripcion)
                    <p class="text-on-surface-variant font-light leading-relaxed">
                        {{ $producto->descripcion }}
                    </p>
                @endif

                {{-- CTA --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a href="https://wa.me/5212345678?text={{ urlencode('Hola, me interesa el producto: ' . $producto->nombre) }}"
                       target="_blank"
                       class="inline-block bg-primary text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300 text-center">
                        Pedir por WhatsApp
                    </a>
                    <a href="{{ route('visitanos') }}"
                       class="inline-block border border-outline-variant text-on-surface px-10 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface transition-all duration-300 text-center">
                        Visítanos
                    </a>
                </div>

                {{-- Flores incluidas --}}
                @if ($producto->flores->count())
                    <div class="pt-4 border-t border-outline-variant">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">
                            Flores incluidas
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($producto->flores as $flor)
                                <a href="{{ route('directorio-floral.show', $flor->slug) }}"
                                   class="text-xs tracking-[0.1em] px-3 py-1 border border-outline-variant text-on-surface-variant hover:border-primary hover:text-primary transition-colors duration-300">
                                    {{ $flor->nombre }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Detalles --}}
                @if ($producto->detalles)
                    <div class="pt-4 border-t border-outline-variant">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Detalles</p>
                        <div class="text-on-surface-variant font-light leading-relaxed text-sm">
                            {!! nl2br(e($producto->detalles)) !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Productos relacionados --}}
        @if ($relacionados->count())
            <div class="border-t border-outline-variant pt-16">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
                    También te puede interesar
                </p>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relacionados as $rel)
                        @php $portada = $rel->galeria->first(); @endphp
                        <a href="{{ route('productos.show', $rel->slug) }}" class="group block">
                            <div class="aspect-[3/4] overflow-hidden bg-surface-container-low mb-3">
                                @if ($portada)
                                    <img src="{{ Storage::url($portada->imagen) }}"
                                         alt="{{ $rel->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                @endif
                            </div>
                            <h3 class="font-serif text-sm text-on-surface group-hover:text-primary transition-colors duration-300">
                                {{ $rel->nombre }}
                            </h3>
                            <p class="text-sm text-on-surface-variant mt-1">
                                ${{ number_format($rel->precio_venta, 2) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</x-layouts::public>