<x-layouts::public :title="__('Inicio')">

    {{-- ═══════════════════════════════════════════
         HERO — Full screen editorial
    ════════════════════════════════════════════ --}}
    <section class="relative h-screen w-full overflow-hidden flex items-center justify-center">

        {{-- Imagen de fondo --}}
        <div class="absolute inset-0 z-0 bg-stone-200">
            <img src="https://placehold.co/1600x900/2a2420/ffffff?text=1310+Studio"
                 alt="Hero floral"
                 class="w-full h-full object-cover opacity-90 scale-105">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        {{-- Contenido central --}}
        <div class="relative z-10 text-center space-y-8 max-w-4xl px-4 flex flex-col items-center">
            <p class="font-body text-white/90 text-2xl tracking-widest uppercase font-light"
               style="text-shadow: 0 2px 4px rgba(0,0,0,0.3)">
                ELEGANCIA EN ESTADO NATURAL
            </p>
            <h1 class="font-serif font-light italic text-white drop-shadow-md"
                style="font-size: 4.55rem; line-height: 1.1; text-shadow: 0 2px 4px rgba(0,0,0,0.3)">
                Luxury Flower Lab
            </h1>
            <div class="pt-4">
                <a href="{{ route('categorias.index') }}" wire:navigate
                   class="inline-block bg-primary text-on-primary px-12 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-700">
                    Ramos y arreglos
                </a>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4 text-white/80">
            <span class="text-[10px] tracking-[0.2em] uppercase font-light">Scroll</span>
            <div class="w-px h-12 bg-white/40 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1/2 bg-white animate-bounce"></div>
            </div>
        </div>

    </section>


    {{-- ═══════════════════════════════════════════
         SECCIÓN ASIMÉTRICA — Editorial copy
    ════════════════════════════════════════════ --}}
    <section class="py-32 px-8 max-w-[1440px] mx-auto">
        <div class="flex flex-col md:flex-row gap-20 items-center">

            {{-- Imagen --}}
            <div class="w-full md:w-1/2 relative">
                <div class="aspect-[3/4] bg-stone-100 overflow-hidden">
                    <img src="https://placehold.co/600x800/e8e0d8/5a4a3a?text=Arreglo+floral"
                         alt="Arreglo floral editorial"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000">
                </div>
            </div>

            {{-- Copy --}}
            <div class="w-full md:w-1/2 space-y-10">
                <h2 class="font-serif text-5xl text-on-surface leading-tight">
                    La sutileza de lo efímero.
                </h2>
                <p class="text-on-surface-variant leading-relaxed max-w-md font-light">
                    Nuestras piezas no son solo ramos; son esculturas temporales diseñadas
                    para habitar espacios con intención. Cada flor es seleccionada por su
                    arquitectura y longevidad.
                </p>
                <div class="pt-4">
                    <a href="{{ route('categorias.index') }}" wire:navigate
                       class="text-on-surface border-b border-outline pb-1 text-xs tracking-widest uppercase hover:border-on-surface transition-all duration-300">
                        DIRECTORIO FLORAL
                    </a>
                </div>
            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         CARRUSEL — Colección (CSS infinite scroll)
    ════════════════════════════════════════════ --}}
    <section class="py-24 bg-surface-container-low overflow-hidden">

        <div class="px-8 max-w-[1440px] mx-auto mb-16 text-center">
            <h2 class="font-serif text-5xl text-on-surface leading-tight">Colección 1310</h2>
            <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em] mt-2">
                Piezas icónicas de nuestro atelier
            </p>
        </div>

        <div class="w-full overflow-hidden">
            <div class="flex gap-12 px-8 w-max"
                 style="animation: scroll-carousel 40s linear infinite;"
                 x-data
                 @mouseenter="$el.style.animationPlayState='paused'"
                 @mouseleave="$el.style.animationPlayState='running'">

                @php
                $coleccion = [
                    ['Ivory Form',         'ede8e0/7a6a58', 'Minimalismo en estado puro sobre formas orgánicas.'],
                    ['Lila Whisper',       'e8e0f0/5a4a7a', 'Un susurro de elegancia en tonos lavanda y lila.'],
                    ['Tulip Line',         'f0e8e0/7a5a4a', 'La danza de los tulipanes en diseño arquitectónico.'],
                    ['Golden Silhouette',  'f0ece0/7a6a3a', 'Escultura floral que captura la luz del atardecer.'],
                    ['Orchid Veil',        'e8f0ec/3a6a5a', 'La pureza de la orquídea en un velo de sofisticación.'],
                    ['Magenta Bloom',      'f0e0e8/7a3a5a', 'Intensidad cromática y texturas profundas.'],
                ];
                @endphp

                @foreach (array_merge($coleccion, $coleccion) as $i => $pieza)
                    <div class="group cursor-pointer flex-shrink-0" style="width: 300px;">
                        <div class="overflow-hidden bg-white relative" style="aspect-ratio: 3/4;">
                            <img src="https://placehold.co/600x800/{{ $pieza[1] }}?text={{ urlencode($pieza[0]) }}"
                                 alt="{{ $pieza[0] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                            {{-- Overlay hover --}}
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center p-6 text-center">
                                <p class="text-white font-light text-sm tracking-wide leading-relaxed">
                                    {{ $pieza[2] }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <h3 class="font-serif text-lg text-on-surface italic">{{ $pieza[0] }}</h3>
                            <div class="mt-2 w-8 h-px bg-outline-variant mx-auto"></div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </section>


    {{-- ═══════════════════════════════════════════
         GRID VISUAL — Categorías estilo Instagram
    ════════════════════════════════════════════ --}}
    <section class="py-32 bg-surface">
        <div class="px-8 max-w-[1440px] mx-auto">

            <div class="mb-20 space-y-4">
                <h2 class="font-serif text-5xl text-on-surface leading-tight">Explora</h2>
                <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em]">
                    Encuentra el arreglo perfecto para cada ocasión
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([
                    ['Ramos',      'f0e8e4/8a5a4a'],
                    ['Orquídeas',  'e4eef0/3a6a7a'],
                    ['Eventos',    'e8f0e4/4a6a3a'],
                    ['Plantas',    'f0ece4/6a5a3a'],
                    ['Bouquets',   'f0e4ec/7a3a6a'],
                    ['Silvestres', 'eef0e4/5a6a3a'],
                    ['Corporativo','e4e8f0/3a4a7a'],
                    ['Suscripción','f0eae4/7a5a3a'],
                ] as $cat)
                    <a href="{{ route('categorias.index') }}" wire:navigate
                       class="relative aspect-square overflow-hidden bg-stone-100 group block">
                        <img src="https://placehold.co/600x600/{{ $cat[1] }}?text={{ urlencode($cat[0]) }}"
                             alt="{{ $cat[0] }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <span class="text-white text-xs tracking-[0.15em] uppercase font-light">
                                {{ $cat[0] }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         CTA DUAL — Asesoría + Newsletter
    ════════════════════════════════════════════ --}}
    <section class="py-24 bg-surface-container-low border-y border-outline-variant">
        <div class="max-w-[1440px] mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 md:gap-24">

                {{-- Asesoría personalizada --}}
                <div class="text-center space-y-6 flex flex-col items-center">
                    <flux:icon name="building-storefront" class="w-10 h-10 text-on-surface" />
                    <h2 class="font-serif text-4xl text-on-surface leading-tight">
                        Asesoría personalizada
                    </h2>
                    <p class="text-on-surface-variant leading-relaxed font-light max-w-md">
                        Si buscas opciones personalizadas de ramos o arreglos para eventos especiales,
                        ponte en contacto con nuestra tienda física.
                    </p>
                </div>

                {{-- Newsletter --}}
                <div class="text-center space-y-6 flex flex-col items-center">
                    <flux:icon name="envelope" class="w-10 h-10 text-on-surface" />
                    <h2 class="font-serif text-4xl text-on-surface leading-tight">
                        Únete al atelier
                    </h2>
                    <p class="text-on-surface-variant leading-relaxed font-light max-w-md">
                        Recibe inspiraciones sobre nuestro arte floral, lanzamientos exclusivos
                        y consejos para el cuidado de tus arreglos.
                    </p>
                    <form class="flex flex-col sm:flex-row gap-4 pt-4 w-full max-w-md"
                          x-data="{ email: '' }" @submit.prevent>
                        <input
                            type="email"
                            x-model="email"
                            placeholder="Tu correo electrónico"
                            class="flex-1 bg-white border border-outline-variant px-6 py-4 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-outline">
                        <button type="submit"
                                class="bg-on-surface text-surface px-8 py-4 text-xs tracking-[0.2em] uppercase hover:opacity-80 transition-colors duration-300 whitespace-nowrap">
                            SUSCRIBIRSE
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>


</x-layouts::public>

{{-- CSS animación carrusel --}}
@push('styles')
<style>
    @keyframes scroll-carousel {
        0%   { transform: translateX(0); }
        100% { transform: translateX(calc(-300px * 6 - 3rem * 6)); }
    }
</style>
@endpush