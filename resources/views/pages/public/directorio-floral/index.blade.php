<x-layouts::public :title="__('Directorio Floral')">

{{-- ═══════════════════════════════════════════
     HERO — Minimalista
════════════════════════════════════════════ --}}
<section class="py-12 px-8 max-w-[1440px] mx-auto text-center">
    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-3">Enciclopedia floral</p>
    <h1 class="font-serif text-4xl text-on-surface mb-4">Directorio Floral</h1>
    <p class="text-on-surface-variant font-light max-w-xl mx-auto leading-relaxed text-sm">
        Conoce las flores que trabajamos, su origen, temporada y cómo cuidarlas.
    </p>
</section>

    {{-- ═══════════════════════════════════════════
         FILTROS
    ════════════════════════════════════════════ --}}
    @if ($categorias->count())
        <div class="px-8 max-w-[1440px] mx-auto mb-12">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('directorio-floral.index') }}"
                   class="px-5 py-2 text-[10px] tracking-[0.15em] uppercase transition-colors duration-300
                          {{ !request('categoria') ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface-variant hover:border-primary hover:text-primary' }}">
                    Todas
                </a>
                @foreach ($categorias as $cat)
                    <a href="{{ route('directorio-floral.index', ['categoria' => $cat]) }}"
                       class="px-5 py-2 text-[10px] tracking-[0.15em] uppercase transition-colors duration-300
                              {{ request('categoria') === $cat ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface-variant hover:border-primary hover:text-primary' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         GRID
    ════════════════════════════════════════════ --}}
    <div class="px-8 max-w-[1440px] mx-auto pb-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse ($flores as $flor)
                <a href="{{ route('directorio-floral.show', $flor->slug) }}"
                   class="group block relative aspect-square overflow-hidden bg-stone-100">

                    {{-- Imagen --}}
                    @if ($flor->galeria->first())
                        <img src="{{ Storage::disk('public')->url($flor->galeria->first()->imagen) }}"
                             alt="{{ $flor->nombre }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @elseif ($flor->imagen)
                        <img src="{{ Storage::disk('public')->url($flor->imagen) }}"
                             alt="{{ $flor->nombre }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-stone-200">
                            <flux:icon name="photo" class="w-10 h-10 text-outline-variant" />
                        </div>
                    @endif

                    {{-- Overlay con título centrado --}}
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100
                                transition-opacity duration-500 flex flex-col items-center justify-center gap-2 p-6 text-center">
                        <h3 class="font-serif italic text-white text-2xl leading-tight">
                            {{ $flor->nombre }}
                        </h3>
                        @if ($flor->categoria)
                            <p class="text-white/60 text-[10px] tracking-[0.2em] uppercase">
                                {{ $flor->categoria }}
                            </p>
                        @endif
                    </div>

                    {{-- Nombre siempre visible en bottom (sutil) --}}
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/50 to-transparent
                                p-4 translate-y-0 group-hover:opacity-0 transition-opacity duration-300">
                        <p class="font-serif italic text-white text-center text-sm">{{ $flor->nombre }}</p>
                    </div>

                </a>
            @empty
                <div class="col-span-3 py-20 text-center">
                    <p class="text-on-surface-variant font-light">No hay flores en el directorio aún.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">{{ $flores->links() }}</div>
    </div>

</x-layouts::public>