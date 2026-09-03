<x-layouts::public :title="__('Categorías')">

    {{-- ═══════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════ --}}
    <section class="py-20 px-8 max-w-[1440px] mx-auto">
        <div class="mb-16 space-y-3">
            <h1 class="font-serif text-5xl text-on-surface leading-tight">Categorías</h1>
            <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em]">
                Encuentra el arreglo perfecto para cada ocasión
            </p>
        </div>

        @if($categorias->isEmpty())
            <p class="text-on-surface-variant text-sm">No hay categorías disponibles.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($categorias as $categoria)
                    <a href="{{ route('categorias.show', $categoria->slug) }}" wire:navigate
                    class="relative overflow-hidden bg-stone-100 group block" style="aspect-ratio: 4/3;">

                        @if($categoria->imagen)
                            <img src="{{ Storage::disk('public')->url($categoria->imagen) }}"
                                alt="{{ $categoria->titulo }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-stone-200"></div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent
                                    flex items-end p-8">
                            <div class="translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                                <h2 class="font-serif italic text-white text-2xl leading-tight">
                                    {{ $categoria->titulo }}
                                </h2>
                                @if($categoria->resumen)
                                    <p class="hidden sm:block text-white/70 text-xs tracking-wide mt-2 font-light max-w-xs">
                                        {{ $categoria->resumen }}
                                    </p>
                                @endif
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>
        @endif
        </div>

    </section>

</x-layouts::public>