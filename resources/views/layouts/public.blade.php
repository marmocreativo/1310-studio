<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900 flex flex-col">

        <flux:header container class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <flux:brand href="{{ route('home') }}" name="{{ config('app.name') }}" />

            <flux:navbar class="hidden md:flex">
                <flux:navbar.item href="{{ route('home') }}" :current="request()->routeIs('home')" wire:navigate>
                    Inicio
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('categorias.index') }}" :current="request()->routeIs('categorias.*')" wire:navigate>
                    Categorías
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('paginas.index') }}" :current="request()->routeIs('paginas.*')" wire:navigate>
                    Páginas
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('estudio') }}" :current="request()->routeIs('estudio')" wire:navigate>
                    Nuestro Estudio
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('empresas') }}" :current="request()->routeIs('empresas')" wire:navigate>
                    Empresas
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            {{-- Menú móvil --}}
            <flux:dropdown class="md:hidden">
                <flux:button variant="ghost" icon="bars-3" />
                <flux:navmenu>
                    <flux:navmenu.item href="{{ route('home') }}" wire:navigate>Inicio</flux:navmenu.item>
                    <flux:navmenu.item href="{{ route('categorias.index') }}" wire:navigate>Categorías</flux:navmenu.item>
                    <flux:navmenu.item href="{{ route('paginas.index') }}" wire:navigate>Páginas</flux:navmenu.item>
                    <flux:navmenu.item href="{{ route('estudio') }}" wire:navigate>Nuestro Estudio</flux:navmenu.item>
                    <flux:navmenu.item href="{{ route('empresas') }}" wire:navigate>Empresas</flux:navmenu.item>
                    <flux:separator />
                    @auth
                        <flux:navmenu.item href="{{ route('admin.dashboard') }}" wire:navigate>Panel Admin</flux:navmenu.item>
                    @else
                        <flux:navmenu.item href="{{ route('login') }}" wire:navigate>Iniciar sesión</flux:navmenu.item>
                        <flux:navmenu.item href="{{ route('register') }}" wire:navigate>Registrarse</flux:navmenu.item>
                    @endauth
                </flux:navmenu>
            </flux:dropdown>

            @auth
                <flux:button href="{{ route('admin.dashboard') }}" variant="primary" size="sm" wire:navigate class="hidden md:flex">
                    {{ __('Panel') }}
                </flux:button>
            @else
                <div class="hidden md:flex items-center gap-2">
                    <flux:button href="{{ route('login') }}" variant="ghost" size="sm" wire:navigate>
                        {{ __('Iniciar sesión') }}
                    </flux:button>
                    <flux:button href="{{ route('register') }}" variant="primary" size="sm" wire:navigate>
                        {{ __('Registrarse') }}
                    </flux:button>
                </div>
            @endauth
        </flux:header>

        <div class="flex-1">
            {{ $slot }}
        </div>

        {{-- FOOTER --}}
        <footer class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 mt-16">
            <flux:container>
                <div class="py-12 grid grid-cols-1 md:grid-cols-3 gap-10">

                    {{-- Columna 1: Marca --}}
                    <div>
                        <flux:heading level="3" class="mb-3">{{ config('app.name') }}</flux:heading>
                        <flux:text class="text-zinc-500 leading-relaxed text-sm">
                            Estudio floral en la Colonia Roma Norte, Ciudad de México.
                            Diseños únicos para momentos únicos.
                        </flux:text>
                        <div class="flex items-center gap-3 mt-5">
                            <a href="#" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition">
                                <flux:icon name="at-symbol" class="w-5 h-5" />
                            </a>
                            <a href="#" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition">
                                <flux:icon name="phone" class="w-5 h-5" />
                            </a>
                        </div>
                    </div>

                    {{-- Columna 2: Navegación --}}
                    <div>
                        <flux:heading level="4" class="mb-4 text-sm uppercase tracking-widest text-zinc-400">
                            Navegación
                        </flux:heading>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('home') }}" wire:navigate
                                   class="text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition">
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categorias.index') }}" wire:navigate
                                   class="text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition">
                                    Categorías
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('estudio') }}" wire:navigate
                                   class="text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition">
                                    Nuestro Estudio
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('empresas') }}" wire:navigate
                                   class="text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition">
                                    Soluciones Empresariales
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Columna 3: Contacto --}}
                    <div>
                        <flux:heading level="4" class="mb-4 text-sm uppercase tracking-widest text-zinc-400">
                            Contacto
                        </flux:heading>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2">
                                <flux:icon name="map-pin" class="w-4 h-4 text-zinc-400 mt-0.5 shrink-0" />
                                <flux:text class="text-sm text-zinc-500">
                                    Orizaba 78, Roma Norte<br>Cuauhtémoc, CDMX 06700
                                </flux:text>
                            </li>
                            <li class="flex items-center gap-2">
                                <flux:icon name="phone" class="w-4 h-4 text-zinc-400 shrink-0" />
                                <flux:text class="text-sm text-zinc-500">+52 55 1234 5678</flux:text>
                            </li>
                            <li class="flex items-center gap-2">
                                <flux:icon name="envelope" class="w-4 h-4 text-zinc-400 shrink-0" />
                                <flux:text class="text-sm text-zinc-500">hola@1310studio.mx</flux:text>
                            </li>
                            <li class="flex items-center gap-2">
                                <flux:icon name="clock" class="w-4 h-4 text-zinc-400 shrink-0" />
                                <flux:text class="text-sm text-zinc-500">Lun–Vie 9:00–19:00 · Sáb 9:00–15:00</flux:text>
                            </li>
                        </ul>
                    </div>

                </div>

                <flux:separator />

                <div class="py-5 flex flex-col md:flex-row items-center justify-between gap-3">
                    <flux:text class="text-xs text-zinc-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                    </flux:text>
                    <div class="flex gap-4">
                        <a href="{{ route('paginas.show', 'terminos-y-condiciones') }}" wire:navigate
                           class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                            Términos y Condiciones
                        </a>
                        <a href="{{ route('paginas.show', 'aviso-de-privacidad') }}" wire:navigate
                           class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                            Aviso de Privacidad
                        </a>
                    </div>
                </div>
            </flux:container>
        </footer>

        @fluxScripts
    </body>
</html>