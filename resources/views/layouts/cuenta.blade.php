<x-layouts::public :title="$title ?? 'Mi cuenta'">

    <div class="max-w-[1440px] mx-auto px-8 py-16">
        <div class="flex gap-12 items-start">

            {{-- Sidebar --}}
            <aside class="w-56 shrink-0 sticky top-24 hidden lg:block">
                <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant mb-4 px-3">
                    Mi cuenta
                </p>

                @php
                    $navItems = [
                        ['route' => 'cuenta.dashboard',   'label' => 'Dashboard',         'icon' => 'home'],
                        ['route' => 'cuenta.pedidos',     'label' => 'Mis pedidos',        'icon' => 'shopping-bag'],
                        ['route' => 'cuenta.direcciones', 'label' => 'Direcciones',        'icon' => 'map-pin'],
                        ['route' => 'cuenta.perfil',      'label' => 'Datos personales',   'icon' => 'user'],
                        ['route' => 'cuenta.password',    'label' => 'Contraseña',         'icon' => 'lock-closed'],
                        ['route' => 'cuenta.fechas',      'label' => 'Fechas importantes', 'icon' => 'calendar'],
                    ];
                @endphp

                <nav class="space-y-0.5">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 px-3 py-2.5 text-sm transition-colors
                                {{ request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*')
                                    ? 'bg-on-surface text-surface font-medium'
                                    : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                            <flux:icon name="{{ $item['icon'] }}" class="w-4 h-4 shrink-0" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="pt-4 mt-4 border-t border-outline-variant">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 px-3 py-2.5 text-sm text-on-surface-variant hover:text-red-500 transition-colors w-full text-left">
                                <flux:icon name="arrow-right-start-on-rectangle" class="w-4 h-4 shrink-0" />
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            {{-- Mobile nav --}}
            <div class="lg:hidden w-full" x-data='{ open: false }'>
                <button x-on:click="open = !open"
                    class="flex items-center justify-between w-full border border-outline-variant px-4 py-3 text-sm text-on-surface mb-4">
                    <span>Mi cuenta</span>
                    <flux:icon name="chevron-down" class="w-4 h-4 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" x-cloak class="border border-outline-variant divide-y divide-outline-variant/50 mb-8">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm
                                {{ request()->routeIs($item['route']) ? 'bg-surface-container-low font-medium text-on-surface' : 'text-on-surface-variant' }}">
                            <flux:icon name="{{ $item['icon'] }}" class="w-4 h-4 shrink-0" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Contenido --}}
            <main class="flex-1 min-w-0">
                {{ $slot }}
            </main>

        </div>
    </div>

</x-layouts::public>