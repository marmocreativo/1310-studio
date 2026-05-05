<x-layouts::public :title="__('Inicio')">

    {{-- ═══════════════════════════════════════════
        HERO — 3 Slides
    ════════════════════════════════════════════ --}}
    <section class="relative h-screen w-full overflow-hidden"
            x-data="{
                slide: 0,
                total: 3,
                timer: null,
                start() {
                    this.stop();
                    this.timer = setInterval(() => this.next(), 10000);
                },
                stop() {
                    clearInterval(this.timer);
                },
                next() {
                    this.slide = (this.slide + 1) % this.total;
                },
                prev() {
                    this.slide = (this.slide - 1 + this.total) % this.total;
                },
                goTo(i) {
                    this.slide = i;
                    this.start();
                }
            }"
            x-init="start()"
            @mouseenter="stop()"
            @mouseleave="start()">

        {{-- ────────────────────────────────────────
            SLIDE 1 — Imagen de fondo
        ──────────────────────────────────────────── --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
            :class="slide === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'">

            <img src="{{ asset('images/hero_1.jpg') }}"
                alt="1310 Studio"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/25"></div>

            {{-- Copy centrado --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center space-y-8 px-4">
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

        </div>


        {{-- ────────────────────────────────────────
            SLIDE 2 — Video de fondo
        ──────────────────────────────────────────── --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
            :class="slide === 1 ? 'opacity-100 z-10' : 'opacity-0 z-0'">

            <video class="w-full h-full object-cover"
                autoplay muted loop playsinline>
                <source src="{{ asset('videos/video_hero_1.mp4') }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-black/30"></div>

            {{-- Copy centrado --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center space-y-8 px-4">
                <p class="font-body text-white/90 text-2xl tracking-widest uppercase font-light"
                style="text-shadow: 0 2px 4px rgba(0,0,0,0.3)">
                    EL ARTE DE LO VIVO
                </p>
                <h1 class="font-serif font-light italic text-white drop-shadow-md"
                    style="font-size: 4.55rem; line-height: 1.1; text-shadow: 0 2px 4px rgba(0,0,0,0.3)">
                    Cada flor, una historia
                </h1>
            </div>

        </div>


        {{-- ────────────────────────────────────────
            SLIDE 3 — Capas: fondo · logo · flor
        ──────────────────────────────────────────── --}}
        <div class="absolute inset-0 transition-opacity duration-1000"
            :class="slide === 2 ? 'opacity-100 z-10' : 'opacity-0 z-0'">

            {{-- Capa 1: fondo — imagen en cover --}}
            <div class="absolute inset-0">
                <img src="{{ asset('images/hero_bg.jpg') }}"
                    alt=""
                    class="w-full h-full object-cover select-none pointer-events-none">
            </div>

            {{-- Capa 2: logo + textos centrados en columna --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-8">
                    <img src="{{ asset('images/logo_hero.png') }}"
                        alt="1310 Studio"
                        class="w-[15%] max-w-xl opacity-90 select-none pointer-events-none">

                    <p class="font-body text-white/80 text-sm tracking-[0.3em] uppercase font-light">
                        Luxury Flower Lab
                    </p>
                    <h1 class="font-serif font-light text-white text-center leading-tight"
                        style="font-size: 4rem; line-height: 1.1">
                        La belleza <em>que permanece.</em>
                    </h1>
                    <div class="pt-2">
                        <a href="{{ route('categorias.index') }}" wire:navigate
                        class="inline-block border border-white text-white px-12 py-4 text-xs tracking-[0.3em] uppercase hover:bg-white hover:text-on-surface transition-all duration-500">
                            Explorar colección
                        </a>
                    </div>
                </div>

            {{-- Capa 3: flor con transparencia, cuadrada, pegada al fondo, alineada a la derecha --}}
            <div class="absolute bottom-0 right-0 h-full aspect-square pointer-events-none">
                <img src="{{ asset('images/flor_hero.png') }}"
                    alt=""
                    class="w-full h-full object-cover object-top select-none"
                    style="animation: flor-drift 8s ease-in-out infinite;">
            </div>

        </div>


        {{-- ────────────────────────────────────────
            Controles: flechas
        ──────────────────────────────────────────── --}}
        <button @click="prev(); start()"
                class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center transition-colors duration-300 cursor-pointer"
                :class="slide === 2 ? 'text-white/70 hover:text-white' : 'text-white/70 hover:text-white'">
            <flux:icon name="chevron-left" class="w-8 h-8" />
        </button>
        <button @click="next(); start()"
                class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center transition-colors duration-300 cursor-pointer"
                :class="slide === 2 ? 'text-white/70 hover:text-white' : 'text-white/70 hover:text-white'">
            <flux:icon name="chevron-right" class="w-8 h-8" />
        </button>


        {{-- ────────────────────────────────────────
            Controles: dots
        ──────────────────────────────────────────── --}}
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 flex items-center gap-3">
            <template x-for="i in total" :key="i">
                <button @click="goTo(i - 1)"
                        class="transition-all duration-500 rounded-full cursor-pointer"
                        :class="slide === i - 1
                            ? 'w-8 h-[3px] bg-white'
                            : 'w-[3px] h-[3px] bg-white/50 hover:bg-white/80'">
                </button>
            </template>
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
                    <img src="{{ asset('images/arreglo_floral.jpg') }}"
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
        CARRUSEL — Productos destacados
    ════════════════════════════════════════════ --}}
    <section class="py-24 bg-surface-container-low overflow-hidden">

        <div class="px-8 max-w-[1440px] mx-auto mb-16 text-center">
            <h2 class="font-serif text-5xl text-on-surface leading-tight">Colección 1310</h2>
            <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em] mt-2">
                Piezas icónicas de nuestro atelier
            </p>
        </div>

        @if($destacados->isNotEmpty())
        <div class="w-full overflow-hidden">
            <div class="flex gap-10 w-max"
                x-data
                @mouseenter="$el.style.animationPlayState='paused'"
                @mouseleave="$el.style.animationPlayState='running'"
                style="animation: scroll-carousel 40s linear infinite;">

                @php $items = $destacados->count() < 4 ? $destacados->concat($destacados) : $destacados; @endphp

                {{-- Doble loop para el efecto infinito --}}
                @foreach ([$items, $items] as $grupo)
                    @foreach ($grupo as $producto)
                        @php $imagen = $producto->galeria->first(); @endphp
                        <a href="{{ route('productos.show', $producto->slug) }}" wire:navigate
                        class="group flex-shrink-0 block" style="width: 280px;">

                            {{-- Imagen --}}
                            <div class="overflow-hidden bg-stone-100 relative" style="aspect-ratio: 3/4;">
                                @if($imagen)
                                    <img src="{{ Storage::disk('public')->url($imagen->imagen) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                                @else
                                    <div class="w-full h-full bg-stone-200 flex items-center justify-center">
                                        <flux:icon name="photo" class="w-10 h-10 text-outline-variant" />
                                    </div>
                                @endif
                            </div>

                            {{-- Nombre --}}
                            <div class="mt-4 text-center">
                                <p class="font-serif text-sm italic text-on-surface-variant tracking-wide">
                                    {{ $producto->nombre }}
                                </p>
                            </div>

                        </a>
                    @endforeach
                @endforeach

            </div>
        </div>
        @endif

    </section>

    {{-- ═══════════════════════════════════════════
        GRID — Directorio floral al azar
    ════════════════════════════════════════════ --}}
    @if($flores->isNotEmpty())
    <section class="py-24 bg-surface">
        <div class="px-8 max-w-[1440px] mx-auto">

            <div class="mb-16 flex items-end justify-between">
                <div class="space-y-3">
                    <h2 class="font-serif text-5xl text-on-surface leading-tight">Directorio Floral</h2>
                    <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em]">
                        Conoce nuestras flores
                    </p>
                </div>
                <a href="{{ route('directorio-floral.index') }}" wire:navigate
                class="hidden md:inline-block text-[10px] tracking-[0.2em] uppercase border-b border-outline pb-1 text-on-surface-variant hover:text-on-surface hover:border-on-surface transition-all duration-300">
                    Ver directorio completo
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($flores as $flor)
                    @php $imagen = $flor->galeria->first() ?? null; @endphp
                    <a href="{{ route('directorio-floral.show', $flor->slug) }}" wire:navigate
                    class="group block relative overflow-hidden bg-stone-100" style="aspect-ratio: 1/1;">

                        {{-- Imagen --}}
                        @if($imagen)
                            <img src="{{ Storage::disk('public')->url($imagen->imagen) }}"
                                alt="{{ $flor->nombre }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @elseif($flor->imagen)
                            <img src="{{ Storage::disk('public')->url($flor->imagen) }}"
                                alt="{{ $flor->nombre }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-stone-200 flex items-center justify-center">
                                <flux:icon name="photo" class="w-10 h-10 text-outline-variant" />
                            </div>
                        @endif

                        {{-- Overlay con nombre --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent
                                    flex items-end p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                            <div>
                                <p class="font-serif italic text-white text-lg leading-tight">
                                    {{ $flor->nombre }}
                                </p>
                                @if($flor->categoria)
                                    <p class="text-white/60 text-[10px] tracking-[0.15em] uppercase mt-1">
                                        {{ $flor->categoria }}
                                    </p>
                                @endif
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>

            {{-- Link móvil --}}
            <div class="mt-10 text-center md:hidden">
                <a href="{{ route('directorio-floral.index') }}" wire:navigate
                class="text-[10px] tracking-[0.2em] uppercase border-b border-outline pb-1 text-on-surface-variant hover:text-on-surface transition-all duration-300">
                    Ver directorio completo
                </a>
            </div>

        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════
        GRID VISUAL — Categorías
    ════════════════════════════════════════════ --}}
    @if($categorias->isNotEmpty())
    <section class="py-32 bg-surface">
        <div class="px-8 max-w-[1440px] mx-auto">

            <div class="mb-20 space-y-4">
                <h2 class="font-serif text-5xl text-on-surface leading-tight">Explora</h2>
                <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em]">
                    Encuentra el arreglo perfecto para cada ocasión
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
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

                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent
                                    flex items-end p-8">
                            <div class="translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                                <h3 class="font-serif italic text-white text-2xl leading-tight">
                                    {{ $categoria->titulo }}
                                </h3>
                                @if($categoria->resumen)
                                    <p class="text-white/70 text-xs tracking-wide mt-2 font-light max-w-xs">
                                        {{ $categoria->resumen }}
                                    </p>
                                @endif
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>

        </div>
    </section>
    @endif


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
<style>
    @keyframes scroll-carousel {
        0%   { transform: translateX(0); }
        100% { transform: translateX(calc(-300px * 6 - 3rem * 6)); }
    }
    @keyframes flor-drift {
        0%, 100% { transform: translateX(0px); }
        50%       { transform: translateX(12px); }
    }
</style>