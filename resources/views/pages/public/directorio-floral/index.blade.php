<x-layouts::public :title="__('Directorio Floral')">

    {{-- Hero --}}
    <div class="py-24 px-8 max-w-[1440px] mx-auto text-center">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Enciclopedia floral</p>
        <h1 class="font-serif text-5xl md:text-6xl text-on-surface mb-6">Directorio Floral</h1>
        <p class="text-on-surface-variant font-light max-w-xl mx-auto leading-relaxed">
            Conoce las flores que trabajamos, su origen, temporada y cómo cuidarlas.
        </p>
    </div>

    {{-- Filtro por categoría --}}
    @if ($categorias->count())
        <div class="px-8 max-w-[1440px] mx-auto mb-12">
            <div class="flex flex-wrap gap-3 justify-center">
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

    {{-- Grid --}}
    <div class="px-8 max-w-[1440px] mx-auto pb-24">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($flores as $flor)
                <a href="{{ route('directorio-floral.show', $flor->slug) }}" class="group block">
                    <div class="aspect-square overflow-hidden bg-surface-container-low mb-4">
                        @if ($flor->galeria->first())
                            <img src="{{ Storage::url($flor->galeria->first()->imagen) }}"
                                 alt="{{ $flor->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @elseif ($flor->imagen)
                            <img src="{{ Storage::url($flor->imagen) }}"
                                 alt="{{ $flor->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <flux:icon name="photo" class="w-10 h-10 text-outline-variant" />
                            </div>
                        @endif
                    </div>
                    <h3 class="font-serif text-on-surface group-hover:text-primary transition-colors duration-300">
                        {{ $flor->nombre }}
                    </h3>
                    @if ($flor->categoria)
                        <p class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant mt-1">
                            {{ $flor->categoria }}
                        </p>
                    @endif
                </a>
            @empty
                <div class="col-span-4 py-20 text-center">
                    <p class="text-on-surface-variant font-light">No hay flores en el directorio aún.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">{{ $flores->links() }}</div>
    </div>

</x-layouts::public>