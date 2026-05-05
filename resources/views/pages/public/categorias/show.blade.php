<x-layouts::public :title="$categoria->titulo">

    {{-- Hero de categoría --}}
    <div class="relative h-64 md:h-80 overflow-hidden">
        <img src="{{ $categoria->imagen_url }}"
             alt="{{ $categoria->titulo }}"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40 flex items-end">
            <div class="px-8 py-8 max-w-[1440px] w-full mx-auto">
                <p class="text-[11px] tracking-[0.2em] uppercase text-white/70 mb-2">
                    <a href="{{ route('categorias.index') }}" class="hover:text-white transition-colors">Categorías</a>
                    <span class="mx-2">·</span>
                    {{ $categoria->titulo }}
                </p>
                <h1 class="font-serif text-4xl md:text-5xl text-white">{{ $categoria->titulo }}</h1>
                @if ($categoria->resumen)
                    <p class="text-white/80 font-light mt-2 max-w-xl">{{ $categoria->resumen }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Subcategorías si las hay --}}
        @if ($categoria->hijos->count())
            <div class="mb-16">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-6">Subcategorías</p>
                <div class="flex flex-wrap gap-3">
                    @foreach ($categoria->hijos as $hijo)
                        <a href="{{ route('categorias.show', $hijo->slug) }}"
                           class="px-5 py-2 border border-outline-variant text-xs tracking-[0.1em] uppercase text-on-surface-variant hover:border-primary hover:text-primary transition-colors duration-300">
                            {{ $hijo->titulo }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Productos --}}
        @if ($productos->count())
            <div class="mb-10 flex items-baseline justify-between">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                    {{ $productos->total() }} {{ Str::plural('producto', $productos->total()) }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($productos as $producto)
                    @php $portada = $producto->galeria->first(); @endphp
                    <a href="{{ route('productos.show', $producto->slug) }}"
                       class="group block">
                        <div class="aspect-[3/4] overflow-hidden bg-surface-container-low mb-4">
                            @if ($portada)
                                <img src="{{ Storage::disk('public')->url($portada->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <flux:icon name="photo" class="w-10 h-10 text-outline-variant" />
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1 text-center">
                            <h3 class="font-serif text-on-surface group-hover:text-primary transition-colors duration-300">
                                {{ $producto->nombre }}
                            </h3>
                            <div class="flex items-center gap-3 justify-center">
                                @if ($producto->tiene_descuento)
                                    <span class="text-sm text-outline line-through">
                                        ${{ number_format($producto->precio_lista, 2) }}
                                    </span>
                                @endif
                                <span class="text-sm font-medium text-on-surface">
                                    ${{ number_format($producto->precio_venta, 2) }}
                                </span>
                                @if ($producto->tiene_descuento)
                                    <span class="text-[10px] tracking-[0.1em] uppercase text-tertiary">
                                        -{{ $producto->porcentaje_descuento }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12">{{ $productos->links() }}</div>

        @else
            <div class="py-20 text-center">
                <p class="text-on-surface-variant font-light">No hay productos en esta categoría aún.</p>
                <a href="{{ route('categorias.index') }}"
                   class="inline-block mt-6 text-xs tracking-[0.15em] uppercase border-b border-outline pb-1 text-on-surface hover:border-on-surface transition-colors duration-300">
                    Ver todas las categorías
                </a>
            </div>
        @endif

    </div>

</x-layouts::public>