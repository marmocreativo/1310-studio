<x-layouts::app title="Pedidos">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="'Pedidos'"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Pedidos'],
        ]"
    />

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.pedidos.index') }}"
          class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-48">
            <flux:input
                name="busqueda"
                value="{{ request('busqueda') }}"
                placeholder="Número, nombre o email…"
                icon="magnifying-glass"
            />
        </div>

        <div>
            <flux:select name="estado" class="w-40">
                <flux:select.option value="">Todos los estados</flux:select.option>
                @foreach($estados as $estado)
                    <flux:select.option
                        value="{{ $estado }}"
                        :selected="request('estado') === $estado">
                        {{ ucfirst($estado) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div>
            <flux:select name="tipo_entrega" class="w-44">
                <flux:select.option value="">Tipo de entrega</flux:select.option>
                <flux:select.option value="envio"   :selected="request('tipo_entrega') === 'envio'">Envío</flux:select.option>
                <flux:select.option value="tienda"  :selected="request('tipo_entrega') === 'tienda'">Tienda</flux:select.option>
            </flux:select>
        </div>

        <div>
            <flux:input
                type="date"
                name="fecha"
                value="{{ request('fecha') }}"
                class="w-44"
            />
        </div>

        <flux:button type="submit" variant="primary">Filtrar</flux:button>

        @if(request()->hasAny(['busqueda', 'estado', 'tipo_entrega', 'fecha']))
            <flux:button href="{{ route('admin.pedidos.index') }}" variant="ghost">
                Limpiar
            </flux:button>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
        @if($pedidos->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon name="shopping-bag" class="size-10 text-zinc-300 mb-3" />
                <flux:text class="text-zinc-500 text-sm">No hay pedidos que coincidan.</flux:text>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Pedido</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Cliente</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Entrega</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Total</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Estado</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Pago</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($pedidos as $pedido)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">

                            {{-- Número + fecha --}}
                            <td class="px-5 py-4">
                                <p class="font-medium text-zinc-800 dark:text-zinc-200 font-mono text-xs">
                                    {{ $pedido->numero }}
                                </p>
                                <p class="text-xs text-zinc-400 mt-0.5">
                                    {{ $pedido->created_at->format('d M Y, H:i') }}
                                </p>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-5 py-4">
                                <p class="text-zinc-700 dark:text-zinc-300">{{ $pedido->nombre }}</p>
                                <p class="text-xs text-zinc-400 mt-0.5">{{ $pedido->email }}</p>
                            </td>

                            {{-- Fecha + bloque --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5">
                                    @if($pedido->tipo_entrega === 'envio')
                                        <flux:icon name="truck" class="size-3.5 text-zinc-400 shrink-0" />
                                    @else
                                        <flux:icon name="building-storefront" class="size-3.5 text-zinc-400 shrink-0" />
                                    @endif
                                    <p class="text-zinc-700 dark:text-zinc-300 text-xs">
                                        {{ $pedido->fecha_entrega?->format('d M Y') ?? '—' }}
                                    </p>
                                </div>
                                <p class="text-xs text-zinc-400 mt-0.5 ml-5">
                                    {{ $pedido->bloque_entrega_label }}
                                </p>
                            </td>

                            {{-- Total --}}
                            <td class="px-5 py-4">
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                    ${{ number_format($pedido->total, 2) }}
                                </p>
                                <p class="text-xs text-zinc-400 mt-0.5">
                                    {{ $pedido->items->count() }} {{ $pedido->items->count() === 1 ? 'artículo' : 'artículos' }}
                                </p>
                            </td>

                            {{-- Estado pedido --}}
                            <td class="px-5 py-4">
                                <div
                                    x-data="{ open: false, estado: '{{ $pedido->estado }}' }"
                                    class="relative"
                                >
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="flex items-center gap-1.5 group"
                                    >
                                        <flux:badge :color="$pedido->estado_color" size="sm">
                                            {{ $pedido->estado_label }}
                                        </flux:badge>
                                        <flux:icon name="chevron-down" class="size-3 text-zinc-400 group-hover:text-zinc-600 transition-colors" />
                                    </button>

                                    <div
                                        x-show="open"
                                        @click.outside="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="absolute z-20 top-full left-0 mt-1 w-44 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg overflow-hidden"
                                        style="display:none"
                                    >
                                        @foreach(['pendiente','pagado','preparando','enviado','entregado','cancelado'] as $e)
                                            <button
                                                type="button"
                                                @click="
                                                    fetch('{{ route('admin.pedidos.estado', $pedido) }}', {
                                                        method: 'PATCH',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'Accept': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ estado: '{{ $e }}' })
                                                    })
                                                    .then(r => r.json())
                                                    .then(d => {
                                                        if (d.ok) {
                                                            window.location.reload();
                                                        }
                                                    });
                                                    open = false;
                                                "
                                                class="w-full text-left px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors
                                                    {{ $e === $pedido->estado ? 'font-medium bg-zinc-50 dark:bg-zinc-800' : '' }}"
                                            >
                                                {{ ucfirst($e) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            {{-- Estado pago --}}
                            <td class="px-5 py-4">
                                @if($pedido->pago)
                                    @php
                                        $colorPago = match($pedido->pago->estado) {
                                            'aprobado'    => 'green',
                                            'rechazado'   => 'red',
                                            'reembolsado' => 'blue',
                                            default       => 'yellow',
                                        };
                                    @endphp
                                    <flux:badge :color="$colorPago" size="sm">
                                        {{ $pedido->pago->estado_label }}
                                    </flux:badge>
                                    <p class="text-xs text-zinc-400 mt-1">
                                        {{ $pedido->pago->metodo_label }}
                                    </p>
                                @else
                                    <flux:text class="text-xs text-zinc-400">—</flux:text>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-5 py-4 text-right">
                                <flux:button
                                    href="{{ route('admin.pedidos.show', $pedido) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="eye"
                                    wire:navigate
                                />
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginación --}}
            @if($pedidos->hasPages())
                <div class="px-5 py-4 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $pedidos->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
</x-layouts::app>