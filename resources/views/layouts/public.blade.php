<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-surface text-on-surface font-body flex flex-col">

        {{-- ═══════════════════════════════════════════
             PAGE LOADER
        ════════════════════════════════════════════ --}}
        <div id="page-loader"
             class="fixed inset-0 z-[200] bg-surface flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-300">
            <div class="flex flex-col items-center gap-4">
                <img src="/images/logo_menu.png" class="h-10 w-auto animate-pulse">
                <div class="w-32 h-px bg-outline-variant overflow-hidden relative">
                    <div class="absolute inset-y-0 left-0 bg-primary animate-[loader_1.2s_ease-in-out_infinite]"></div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             OFFCANVAS — Menú navegación
        ════════════════════════════════════════════ --}}
        <div id="nav-overlay"
             class="fixed inset-0 z-[90] bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300"
             onclick="closeAll()">
        </div>

        <aside id="nav-offcanvas"
               class="fixed top-0 left-0 h-full w-80 z-[100] bg-surface border-r border-outline-variant
                      -translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]
                      flex flex-col">

            {{-- Header del offcanvas --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-outline-variant">
                <a href="{{ route('home') }}" wire:navigate onclick="closeAll()">
                    <img src="/images/logo_menu.png" class="h-8 w-auto">
                </a>
                <button onclick="closeAll()" class="text-on-surface-variant hover:text-on-surface transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Links de navegación --}}
            <nav class="flex-1 overflow-y-auto px-8 py-10 flex flex-col gap-1">
                @foreach ([
                    ['label' => 'Inicio',           'route' => 'home'],
                    ['label' => 'Categorías',       'route' => 'categorias.index'],
                    ['label' => 'Directorio Floral','route' => 'directorio-floral.index'],
                    ['label' => 'Talleres',         'route' => 'talleres.index'],
                    ['label' => 'Eventos',          'route' => 'eventos'],
                    ['label' => 'Visítanos',        'route' => 'visitanos'],
                ] as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate
                       onclick="closeAll()"
                       class="py-4 border-b border-outline-variant text-xs tracking-[0.15em] uppercase
                              text-on-surface-variant hover:text-on-surface transition-colors duration-300">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Footer del offcanvas --}}
            <div class="px-8 py-6 border-t border-outline-variant">
                <p class="text-[10px] tracking-[0.1em] uppercase text-outline font-light">
                    Lun–Vie 9:00–19:00 · Sáb 9:00–15:00
                </p>
                <p class="text-[10px] tracking-[0.1em] uppercase text-outline font-light mt-1">
                    Orizaba 78, Roma Norte · CDMX
                </p>
            </div>

        </aside>

        {{-- ═══════════════════════════════════════════
             OFFCANVAS — Carrito
        ════════════════════════════════════════════ --}}
        <aside id="cart-offcanvas"
               class="fixed top-0 right-0 h-full w-96 z-[100] bg-surface border-l border-outline-variant
                      translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]
                      flex flex-col">

            {{-- Header del carrito --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-outline-variant">
                <p class="text-xs tracking-[0.2em] uppercase text-on-surface">Carrito</p>
                <button onclick="closeAll()" class="text-on-surface-variant hover:text-on-surface transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Items del carrito (simulado) --}}
            <div class="flex-1 overflow-y-auto px-8 py-8">

                {{-- Estado vacío --}}
                <div id="cart-empty" class="flex flex-col items-center justify-center h-full gap-4 text-center">
                    <svg class="w-12 h-12 text-outline-variant" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                    <p class="text-xs tracking-[0.15em] uppercase text-on-surface-variant">Tu carrito está vacío</p>
                    <button onclick="closeAll()"
                            class="mt-4 text-[10px] tracking-[0.2em] uppercase border-b border-outline pb-1 text-on-surface hover:border-on-surface transition-colors duration-300">
                        Seguir explorando
                    </button>
                </div>

                {{-- Items (ocultos en el estado simulado) --}}
                <div id="cart-items" class="hidden flex-col gap-6">
                    {{-- Aquí irán los items del carrito dinámicamente --}}
                </div>

            </div>

            {{-- Footer del carrito --}}
            <div class="px-8 py-6 border-t border-outline-variant flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <p class="text-xs tracking-[0.1em] uppercase text-on-surface-variant">Total</p>
                    <p class="font-serif text-xl text-on-surface">$0.00</p>
                </div>
                <button class="w-full bg-primary text-on-primary py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300">
                    Proceder al pago
                </button>
                <button onclick="closeAll()"
                        class="w-full border border-outline-variant text-on-surface py-3 text-xs tracking-[0.2em] uppercase hover:border-on-surface transition-all duration-300">
                    Seguir comprando
                </button>
            </div>

        </aside>

        {{-- ═══════════════════════════════════════════
             HEADER
        ════════════════════════════════════════════ --}}
        <header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-xl border-b border-outline-variant">
            <div class="max-w-[1920px] mx-auto px-8 py-4 flex items-center justify-between">

                {{-- IZQUIERDA --}}
                <div class="flex-1 flex items-center gap-8">

                    {{-- Burger — solo móvil --}}
                    <button onclick="openNav()"
                            class="md:hidden flex flex-col gap-1.5 group"
                            aria-label="Abrir menú">
                        <span class="w-6 h-px bg-on-surface group-hover:bg-primary transition-colors duration-300"></span>
                        <span class="w-4 h-px bg-on-surface group-hover:bg-primary transition-colors duration-300"></span>
                        <span class="w-6 h-px bg-on-surface group-hover:bg-primary transition-colors duration-300"></span>
                    </button>

                    {{-- Nav links — solo desktop --}}
                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ route('categorias.index') }}" wire:navigate
                        class="text-xs tracking-[0.1em] font-light uppercase text-on-surface-variant hover:text-on-surface transition-colors duration-300">
                            Categorías
                        </a>
                        <a href="{{ route('directorio-floral.index') }}" wire:navigate
                        class="text-xs tracking-[0.1em] font-light uppercase text-on-surface-variant hover:text-on-surface transition-colors duration-300">
                            Directorio Floral
                        </a>
                        <a href="{{ route('talleres.index') }}" wire:navigate
                        class="text-xs tracking-[0.1em] font-light uppercase text-on-surface-variant hover:text-on-surface transition-colors duration-300">
                            Talleres
                        </a>
                    </div>
                </div>

                {{-- CENTRO — Logo --}}
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" wire:navigate>
                        <img src="/images/logo_menu.png" class="max-h-[50px] w-auto">
                    </a>
                </div>

                {{-- DERECHA --}}
                <div class="flex-1 flex justify-end items-center gap-5">

                    {{-- Nav links derecha — solo desktop --}}
                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ route('eventos') }}" wire:navigate
                        class="text-xs tracking-[0.1em] font-light uppercase text-on-surface-variant hover:text-on-surface transition-colors duration-300">
                            Eventos
                        </a>
                        <a href="{{ route('visitanos') }}" wire:navigate
                        class="text-xs tracking-[0.1em] font-light uppercase text-on-surface-variant hover:text-on-surface transition-colors duration-300">
                            Visítanos
                        </a>
                    </div>

                    {{-- Usuario --}}
                    @auth
                        <flux:dropdown position="bottom" align="end">
                            <button class="text-on-surface-variant hover:text-on-surface transition-colors duration-300"
                                    aria-label="Mi cuenta">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </button>
                            <flux:menu>
                                <div class="px-4 py-3 border-b border-outline-variant">
                                    <p class="text-xs font-medium text-on-surface">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-on-surface-variant mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                                @if(auth()->user()->isAdmin ?? false)
                                    <flux:menu.item href="{{ route('admin.dashboard') }}" icon="squares-2x2" wire:navigate>
                                        Panel Admin
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                @endif
                                <flux:menu.item href="{{ route('profile.edit') }}" icon="user" wire:navigate>
                                    Mi perfil
                                </flux:menu.item>
                                <flux:menu.separator />
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                                        Cerrar sesión
                                    </flux:menu.item>
                                </form>
                            </flux:menu>
                        </flux:dropdown>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                        class="text-on-surface-variant hover:text-on-surface transition-colors duration-300"
                        aria-label="Iniciar sesión">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </a>
                    @endauth

                    {{-- Carrito --}}
                    <button onclick="openCart()"
                            class="relative text-on-surface-variant hover:text-on-surface transition-colors duration-300"
                            aria-label="Carrito">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                        </svg>
                        <span id="cart-badge"
                            class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-primary text-on-primary text-[9px] rounded-full hidden items-center justify-center">
                            0
                        </span>
                    </button>

                </div>
            </div>
        </header>

        {{-- Espaciador --}}
        <div class="h-[65px] shrink-0"></div>

        {{-- Contenido principal --}}
        <div class="flex-1">
            {{ $slot }}
        </div>

        {{-- ═══════════════════════════════════════════
             FOOTER
        ════════════════════════════════════════════ --}}
        <footer class="bg-[#f0f0f0] text-on-surface-variant mt-16">
            <div class="max-w-[1440px] mx-auto px-8">

                <div class="py-16 grid grid-cols-1 md:grid-cols-3 gap-10">

                    {{-- Marca --}}
                    <div class="flex flex-col items-start gap-4">
                        <span class="font-serif text-2xl text-on-surface">{{ config('app.name') }}</span>
                        <p class="text-sm font-light leading-relaxed text-on-surface-variant max-w-xs">
                            Estudio floral en la Colonia Roma Norte, Ciudad de México.
                            Diseños únicos para momentos únicos.
                        </p>
                        <div class="flex items-center gap-3 mt-1">
                            <a href="#" class="text-outline hover:text-on-surface transition-colors duration-300">
                                <flux:icon name="at-symbol" class="w-5 h-5" />
                            </a>
                            <a href="#" class="text-outline hover:text-on-surface transition-colors duration-300">
                                <flux:icon name="phone" class="w-5 h-5" />
                            </a>
                        </div>
                    </div>

                    {{-- Navegación --}}
                    <div>
                        <p class="text-[10px] tracking-[0.2em] uppercase font-medium text-outline mb-5">Navegación</p>
                        <ul class="space-y-3">
                            @foreach ([
                                ['label' => 'Inicio',           'route' => 'home'],
                                ['label' => 'Categorías',       'route' => 'categorias.index'],
                                ['label' => 'Directorio Floral','route' => 'directorio-floral.index'],
                                ['label' => 'Talleres',         'route' => 'talleres.index'],
                                ['label' => 'Eventos',          'route' => 'eventos'],
                                ['label' => 'Visítanos',        'route' => 'visitanos'],
                            ] as $item)
                                <li>
                                    <a href="{{ route($item['route']) }}" wire:navigate
                                       class="text-[10px] tracking-[0.2em] uppercase font-light text-outline hover:text-on-surface transition-colors duration-300">
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Contacto --}}
                    <div>
                        <p class="text-[10px] tracking-[0.2em] uppercase font-medium text-outline mb-5">Contacto</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2">
                                <flux:icon name="map-pin" class="w-4 h-4 text-outline mt-0.5 shrink-0" />
                                <span class="text-sm font-light text-on-surface-variant leading-snug">
                                    Orizaba 78, Roma Norte<br>Cuauhtémoc, CDMX 06700
                                </span>
                            </li>
                            <li class="flex items-center gap-2">
                                <flux:icon name="phone" class="w-4 h-4 text-outline shrink-0" />
                                <span class="text-sm font-light text-on-surface-variant">+52 55 1234 5678</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <flux:icon name="envelope" class="w-4 h-4 text-outline shrink-0" />
                                <span class="text-sm font-light text-on-surface-variant">hola@1310studio.mx</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <flux:icon name="clock" class="w-4 h-4 text-outline shrink-0" />
                                <span class="text-sm font-light text-on-surface-variant">Lun–Vie 9:00–19:00 · Sáb 9:00–15:00</span>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="border-t border-outline-variant"></div>

                <div class="py-5 flex flex-col md:flex-row items-center justify-between gap-3">
                    <p class="flex items-center gap-1.5 text-[10px] tracking-[0.2em] uppercase font-light text-outline">
                        <flux:icon name="map-pin" class="w-3.5 h-3.5" />
                        Orizaba 78, Roma Norte, Cuauhtémoc, 06700 Ciudad de México, CDMX
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        @foreach ([
                            ['label' => 'Instagram', 'href' => '#'],
                            ['label' => 'Facebook',  'href' => '#'],
                            ['label' => 'Términos',  'route' => 'paginas.show', 'param' => 'terminos-y-condiciones'],
                            ['label' => 'Privacidad','route' => 'paginas.show', 'param' => 'aviso-de-privacidad'],
                        ] as $link)
                            <a href="{{ isset($link['route']) ? route($link['route'], $link['param'] ?? []) : $link['href'] }}"
                               @if(isset($link['route'])) wire:navigate @endif
                               class="text-[10px] tracking-[0.2em] uppercase font-medium text-outline hover:text-on-surface transition-colors duration-300">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="pb-5 text-center">
                    <p class="text-[10px] tracking-[0.1em] uppercase font-light text-outline/60">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Luxury Flower Lab.
                    </p>
                </div>

            </div>
        </footer>

        @fluxScripts

        {{-- ═══════════════════════════════════════════
             SCRIPTS — Offcanvas + Loader
        ════════════════════════════════════════════ --}}
        <script>
            // ── Protección contra doble ejecución (Livewire SPA) ──
            if (typeof window._offcanvasInit === 'undefined') {
                window._offcanvasInit = true;

                window.openNav = function() {
                    document.getElementById('cart-offcanvas').classList.add('translate-x-full');
                    document.getElementById('nav-offcanvas').classList.remove('-translate-x-full');
                    document.getElementById('nav-overlay').classList.remove('opacity-0', 'pointer-events-none');
                    document.getElementById('nav-overlay').classList.add('opacity-100');
                    document.body.classList.add('overflow-hidden');
                }

                window.openCart = function() {
                    document.getElementById('nav-offcanvas').classList.add('-translate-x-full');
                    document.getElementById('cart-offcanvas').classList.remove('translate-x-full');
                    document.getElementById('nav-overlay').classList.remove('opacity-0', 'pointer-events-none');
                    document.getElementById('nav-overlay').classList.add('opacity-100');
                    document.body.classList.add('overflow-hidden');
                }

                window.closeAll = function() {
                    document.getElementById('nav-offcanvas').classList.add('-translate-x-full');
                    document.getElementById('cart-offcanvas').classList.add('translate-x-full');
                    document.getElementById('nav-overlay').classList.add('opacity-0', 'pointer-events-none');
                    document.getElementById('nav-overlay').classList.remove('opacity-100');
                    document.body.classList.remove('overflow-hidden');
                }

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') window.closeAll();
                });
            }

            // ── Cerrar offcanvas en cada navegación ──
            document.addEventListener('livewire:navigating', () => {
                window.closeAll();
            });

            // ── Page Loader ──
            function showLoader() {
                var loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.remove('opacity-0');
                    loader.classList.add('opacity-100', 'pointer-events-auto');
                }
            }

            function hideLoader() {
                var loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.add('opacity-0');
                    loader.classList.remove('opacity-100', 'pointer-events-auto');
                }
            }

            document.addEventListener('livewire:navigate', showLoader);
            document.addEventListener('livewire:navigating', showLoader);
            document.addEventListener('livewire:navigated', () => {
                setTimeout(hideLoader, 200);
            });

            window.addEventListener('load', () => {
                setTimeout(hideLoader, 300);
            });
        </script>

        {{-- Keyframe del loader en CSS --}}
        <style>
            @keyframes loader {
                0%   { left: -100%; width: 40%; }
                50%  { left: 30%;  width: 60%; }
                100% { left: 100%; width: 40%; }
            }
        </style>

    </body>
</html>