<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <x-admin.page-header
            titulo="Dashboard"
            :breadcrumbs="[['label' => 'Dashboard']]"
        />

        {{-- Tarjetas de estadísticas --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-1">
                <p class="text-xs text-zinc-400 uppercase tracking-wide">Ventas del mes</p>
                <p class="font-serif text-2xl text-zinc-800">${{ number_format($ventasMes, 2) }}</p>
                <p class="text-xs text-zinc-400">MXN</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-1">
                <p class="text-xs text-zinc-400 uppercase tracking-wide">Pedidos totales</p>
                <p class="font-serif text-2xl text-zinc-800">{{ $totalPedidos }}</p>
                <div class="flex items-center gap-3 text-xs text-zinc-400 mt-1">
                    <span class="text-amber-600">{{ $pedidosPendientes }} pendientes</span>
                    <span class="text-blue-600">{{ $pedidosPagados }} pagados</span>
                </div>
            </div>

            <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-1">
                <p class="text-xs text-zinc-400 uppercase tracking-wide">Usuarios</p>
                <p class="font-serif text-2xl text-zinc-800">{{ $totalUsuarios }}</p>
                <p class="text-xs text-zinc-400">registrados</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-1">
                <p class="text-xs text-zinc-400 uppercase tracking-wide">Catálogo</p>
                <p class="font-serif text-2xl text-zinc-800">{{ $totalProductos }}</p>
                <div class="flex items-center gap-3 text-xs text-zinc-400 mt-1">
                    <span>productos activos</span>
                    <span>·</span>
                    <span>{{ $totalTalleres }} talleres</span>
                </div>
            </div>

        </div>

        {{-- Últimos pedidos --}}
        <div class="bg-white border border-zinc-200 rounded-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-zinc-100 flex items-center justify-between">
                <flux:heading size="sm" class="text-zinc-700">Últimos pedidos</flux:heading>
                <flux:button href="{{ route('admin.pedidos.index') }}" size="sm" variant="ghost">
                    Ver todos
                </flux:button>
            </div>

            @if($ultimosPedidos->count())
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase text-xs tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">Número</th>
                            <th class="px-4 py-3 text-left">Cliente</th>
                            <th class="px-4 py-3 text-center hidden sm:table-cell">Entrega</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($ultimosPedidos as $pedido)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-zinc-600">
                                    {{ $pedido->numero }}
                                </td>
                                <td class="px-4 py-3 text-zinc-800">
                                    {{ $pedido->nombre }}
                                    <span class="block text-xs text-zinc-400">{{ $pedido->email }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-zinc-500 text-xs hidden sm:table-cell">
                                    {{ $pedido->fecha_entrega?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <flux:badge color="{{ $pedido->estado_color }}" size="sm">
                                        {{ $pedido->estado_label }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-right text-zinc-800">
                                    ${{ number_format($pedido->total, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button href="{{ route('admin.pedidos.show', $pedido) }}" size="sm" variant="ghost">
                                        Ver
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="px-5 py-10 text-center text-zinc-400 text-sm">No hay pedidos aún.</p>
            @endif
        </div>

        {{-- Accesos rápidos --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Nuevo producto',   'route' => 'admin.productos.create',          'icon' => 'shopping-bag'],
                ['label' => 'Nueva categoría',  'route' => 'admin.categorias.create',          'icon' => 'tag'],
                ['label' => 'Nuevo taller',     'route' => 'admin.talleres.create',            'icon' => 'academic-cap'],
                ['label' => 'Configuraciones',  'route' => 'admin.configuraciones.index',      'icon' => 'cog-6-tooth'],
            ] as $link)
                <a href="{{ route($link['route']) }}"
                   class="bg-white border border-zinc-200 rounded-sm p-4 flex items-center gap-3 text-sm text-zinc-600 hover:border-[#927F64] hover:text-[#927F64] transition-colors duration-200">
                    <flux:icon name="{{ $link['icon'] }}" class="w-4 h-4 shrink-0" />
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

    </div>
</x-layouts::app>