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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-24"
             x-data="{
                 activa: 0,
                 lightbox: false,
                 zoom: false,
                 total: {{ $producto->galeria->count() }},
                 next() { this.activa = (this.activa + 1) % this.total; this.zoom = false; },
                 prev() { this.activa = (this.activa - 1 + this.total) % this.total; this.zoom = false; },
             }"
             @keydown.escape.window="lightbox = false; zoom = false"
             @keydown.arrow-right.window="if(lightbox) next()"
             @keydown.arrow-left.window="if(lightbox) prev()">

            {{-- Galería --}}
            <div>
                {{-- Imagen principal cuadrada --}}
                <div class="aspect-square overflow-hidden bg-surface-container-low mb-4 relative group cursor-zoom-in"
                     @click="lightbox = true">
                    @foreach ($producto->galeria as $i => $img)
                        <img src="{{ Storage::url($img->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             x-show="activa === {{ $i }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @endforeach
                    @if ($producto->galeria->isEmpty())
                        <div class="w-full h-full flex items-center justify-center">
                            <flux:icon name="photo" class="w-16 h-16 text-outline-variant" />
                        </div>
                    @endif

                    {{-- Indicador de galería --}}
                    @if ($producto->galeria->count() > 1)
                        <div class="absolute bottom-4 right-4 bg-black/50 text-white text-[10px] tracking-[0.1em] px-3 py-1">
                            <span x-text="activa + 1"></span> / {{ $producto->galeria->count() }}
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if ($producto->galeria->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-1">
                        @foreach ($producto->galeria as $i => $img)
                            <button @click="activa = {{ $i }}"
                                    class="flex-shrink-0 w-20 h-20 overflow-hidden border-2 transition-colors duration-200"
                                    :class="activa === {{ $i }} ? 'border-primary' : 'border-transparent hover:border-outline-variant'">
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
                    @if($producto->precio_venta > 0)
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
                    @else
                        <span class="font-serif text-2xl text-on-surface-variant italic">Próximamente</span>
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
                        Pre-ordena por WhatsApp
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

            {{-- ─── LIGHTBOX ─────────────────────────────── --}}
            <div x-show="lightbox"
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[300] bg-black/95 flex items-center justify-center"
                 @click.self="lightbox = false; zoom = false"
                 style="display: none;">

                {{-- Cerrar --}}
                <button @click="lightbox = false; zoom = false"
                        class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors z-10">
                    <flux:icon name="x-mark" class="w-8 h-8" />
                </button>

                {{-- Contador --}}
                @if($producto->galeria->count() > 1)
                    <div class="absolute top-6 left-1/2 -translate-x-1/2 text-white/50 text-[11px] tracking-[0.2em] uppercase">
                        <span x-text="activa + 1"></span> / {{ $producto->galeria->count() }}
                    </div>
                @endif

                {{-- Imagen con zoom --}}
                <div class="relative w-full h-full flex items-center justify-center overflow-hidden"
                     :class="zoom ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                     @click="zoom = !zoom">
                    @foreach ($producto->galeria as $i => $img)
                        <img src="{{ Storage::url($img->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             x-show="activa === {{ $i }}"
                             :class="zoom ? 'scale-[2]' : 'scale-100'"
                             class="max-h-screen max-w-full object-contain transition-transform duration-500 select-none">
                    @endforeach
                </div>

                {{-- Flechas --}}
                @if($producto->galeria->count() > 1)
                    <button @click.stop="prev()"
                            class="absolute left-6 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                        <flux:icon name="chevron-left" class="w-10 h-10" />
                    </button>
                    <button @click.stop="next()"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                        <flux:icon name="chevron-right" class="w-10 h-10" />
                    </button>
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
                            <div class="aspect-square overflow-hidden bg-surface-container-low mb-3">
                                @if ($portada)
                                    <img src="{{ Storage::url($portada->imagen) }}"
                                         alt="{{ $rel->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                @endif
                            </div>
                            <div class="text-center space-y-1">
                                <h3 class="font-serif text-sm text-on-surface group-hover:text-primary transition-colors duration-300">
                                    {{ $rel->nombre }}
                                </h3>
                                <p class="text-sm text-on-surface-variant">
                                    @if($rel->precio_venta > 0)
                                        ${{ number_format($rel->precio_venta, 2) }}
                                    @else
                                        <span class="italic text-on-surface-variant/60">Próximamente</span>
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</x-layouts::public>