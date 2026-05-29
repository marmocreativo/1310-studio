<x-layouts::cuenta title="Mi cuenta">
<div class="space-y-8">

    <div>
        <h1 class="font-serif text-3xl text-on-surface">Hola, {{ $usuario->name }}</h1>
        <p class="text-sm text-on-surface-variant mt-1 font-light">
            Cliente desde {{ $usuario->created_at->translatedFormat('F \d\e Y') }}
        </p>
    </div>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="border border-outline-variant p-6 space-y-2">
            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Total de pedidos</p>
            <p class="font-serif text-4xl text-on-surface">{{ $totalPedidos }}</p>
        </div>
        <div class="border border-outline-variant p-6 space-y-2">
            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Pedidos activos</p>
            <p class="font-serif text-4xl text-on-surface">{{ $pedidosActivos }}</p>
        </div>
        <div class="border border-outline-variant p-6 space-y-2">
            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Miembro hace</p>
            <p class="font-serif text-4xl text-on-surface">{{ (int) $usuario->created_at->diffInDays(now()) }}</p>
            <p class="text-xs text-on-surface-variant">días</p>
        </div>
    </div>

    {{-- Último pedido --}}
    @if($pedidoReciente)
        <div class="space-y-4">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                Último pedido
            </p>
            <div class="border border-outline-variant p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <p class="font-mono text-sm font-medium text-on-surface">{{ $pedidoReciente->numero }}</p>
                        @include('pages.public.cuenta.partials.badge-estado', ['estado' => $pedidoReciente->estado])
                    </div>
                    <p class="text-xs text-on-surface-variant font-light">
                        {{ $pedidoReciente->created_at->translatedFormat('d \d\e F, Y') }}
                        &middot; {{ $pedidoReciente->items->count() }} {{ Str::plural('producto', $pedidoReciente->items->count()) }}
                        &middot; ${{ number_format($pedidoReciente->total, 2) }}
                    </p>
                </div>
                <a href="{{ route('cuenta.pedidos.show', $pedidoReciente->numero) }}"
                    class="text-[11px] tracking-[0.15em] uppercase border-b border-outline-variant pb-0.5 text-on-surface-variant hover:text-on-surface transition-colors shrink-0">
                    Ver detalle
                </a>
            </div>
        </div>
    @endif

    {{-- Accesos rápidos --}}
    <div class="space-y-4">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
            Accesos rápidos
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach([
                ['route' => 'cuenta.pedidos',     'label' => 'Mis pedidos',     'icon' => 'shopping-bag'],
                ['route' => 'cuenta.direcciones', 'label' => 'Mis direcciones', 'icon' => 'map-pin'],
                ['route' => 'cuenta.perfil',      'label' => 'Mis datos',       'icon' => 'user'],
            ] as $acceso)
                <a href="{{ route($acceso['route']) }}"
                    class="flex items-center gap-3 border border-outline-variant p-4 hover:border-on-surface hover:bg-surface-container-low transition-colors group">
                    <flux:icon name="{{ $acceso['icon'] }}" class="w-4 h-4 text-on-surface-variant shrink-0 group-hover:text-on-surface transition-colors" />
                    <span class="text-sm text-on-surface">{{ $acceso['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

</div>
</x-layouts::cuenta>