<x-layouts::app :title="'Pedido ' . $pedido->numero">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="$pedido->numero"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Pedidos',   'route' => 'admin.pedidos.index'],
            ['label' => $pedido->numero],
        ]"
    />

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    {{-- Métricas rápidas --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Total</flux:text>
            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">
                ${{ number_format($pedido->total, 2) }}
            </p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Estado</flux:text>
            <div class="mt-2">
                <flux:badge :color="$pedido->estado_color">{{ $pedido->estado_label }}</flux:badge>
            </div>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Entrega</flux:text>
            <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">
                {{ $pedido->fecha_entrega?->format('d M Y') ?? '—' }}
            </p>
            <p class="text-xs text-zinc-400">{{ $pedido->bloque_entrega_label }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Pago</flux:text>
            <div class="mt-2">
                @if($pedido->pago)
                    @php
                        $colorPago = match($pedido->pago->estado) {
                            'aprobado'    => 'green',
                            'rechazado'   => 'red',
                            'reembolsado' => 'blue',
                            default       => 'yellow',
                        };
                    @endphp
                    <flux:badge :color="$colorPago">{{ $pedido->pago->estado_label }}</flux:badge>
                @else
                    <flux:text class="text-xs text-zinc-400">Sin pago</flux:text>
                @endif
            </div>
        </div>
    </div>

    <div class="flex gap-6 items-start">

        {{-- ─── Columna principal ───────────────────── --}}
        <div class="flex-1 space-y-6">

            {{-- Items --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="sm">Productos</flux:heading>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Producto</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wide w-20">Cant.</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wide w-28">Precio</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wide w-28">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($pedido->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 shrink-0 overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                            @if($item->imagen_url)
                                                <img src="{{ $item->imagen_url }}"
                                                     alt="{{ $item->nombre_snapshot }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <flux:icon name="photo" class="size-4 text-zinc-400" />
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                                {{ $item->nombre_snapshot }}
                                            </p>
                                            @if($item->opciones_snapshot)
                                                <div class="flex flex-wrap gap-x-3">
                                                    @foreach($item->opciones_snapshot as $op)
                                                        <p class="text-xs text-zinc-400">
                                                            {{ $op['tipo'] }}: {{ $op['opcion'] }}
                                                        </p>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-zinc-600 dark:text-zinc-400">
                                    {{ $item->cantidad }}
                                </td>
                                <td class="px-6 py-4 text-right text-zinc-600 dark:text-zinc-400">
                                    ${{ number_format($item->precio_snapshot, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-zinc-800 dark:text-zinc-200">
                                    ${{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-xs text-zinc-500 uppercase tracking-wide">Subtotal</td>
                            <td class="px-6 py-3 text-right text-sm text-zinc-700 dark:text-zinc-300">${{ number_format($pedido->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-xs text-zinc-500 uppercase tracking-wide">Envío</td>
                            <td class="px-6 py-3 text-right text-sm text-zinc-700 dark:text-zinc-300">
                                @if($pedido->costo_envio > 0)
                                    ${{ number_format($pedido->costo_envio, 2) }}
                                @else
                                    Gratis
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Total</td>
                            <td class="px-6 py-3 text-right font-semibold text-zinc-900 dark:text-white">${{ number_format($pedido->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Entrega --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
                <flux:heading size="sm">Entrega</flux:heading>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Tipo</flux:text>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">
                            {{ $pedido->tipo_entrega === 'envio' ? 'Envío a domicilio' : 'Recoger en tienda' }}
                        </p>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Fecha</flux:text>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">
                            {{ $pedido->fecha_entrega?->format('d M Y') ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Horario</flux:text>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">
                            {{ $pedido->bloque_entrega_label }}
                        </p>
                    </div>
                    @if($pedido->es_envio)
                        <div>
                            <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Zona</flux:text>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">
                                {{ $pedido->zona?->nombre ?? '—' }}
                            </p>
                        </div>
                        <div class="col-span-2">
                            <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Dirección</flux:text>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">
                                {{ $pedido->direccion }}, {{ $pedido->colonia }},
                                {{ $pedido->municipio }}
                                {{ $pedido->cp ? 'CP ' . $pedido->cp : '' }}
                            </p>
                            @if($pedido->referencias)
                                <p class="text-xs text-zinc-400 mt-1">Ref: {{ $pedido->referencias }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Destinatario --}}
            @if($pedido->destinatario_nombre)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
                    <flux:heading size="sm">Destinatario</flux:heading>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Nombre</flux:text>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $pedido->destinatario_nombre }}</p>
                        </div>
                        @if($pedido->destinatario_telefono)
                            <div>
                                <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Teléfono</flux:text>
                                <p class="text-sm text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $pedido->destinatario_telefono }}</p>
                            </div>
                        @endif
                        @if($pedido->mensaje_tarjeta)
                            <div class="col-span-2">
                                <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Mensaje tarjeta</flux:text>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 italic leading-relaxed border-l-2 border-zinc-200 dark:border-zinc-700 pl-3">
                                    "{{ $pedido->mensaje_tarjeta }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Pagos --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="sm">Historial de pagos</flux:heading>
                </div>
                @if($pedido->pagos->isEmpty())
                    <div class="px-6 py-8 text-center">
                        <flux:text class="text-xs text-zinc-400">Sin registros de pago.</flux:text>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Método</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">MP Payment ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($pedido->pagos as $pago)
                                @php
                                    $colorPago = match($pago->estado) {
                                        'aprobado'    => 'green',
                                        'rechazado'   => 'red',
                                        'reembolsado' => 'blue',
                                        default       => 'yellow',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-6 py-3 text-zinc-700 dark:text-zinc-300">{{ $pago->metodo_label }}</td>
                                    <td class="px-6 py-3 font-mono text-xs text-zinc-400">
                                        {{ $pago->mp_payment_id ?? '—' }}
                                    </td>
                                    <td class="px-6 py-3 text-zinc-700 dark:text-zinc-300">
                                        ${{ number_format($pago->monto, 2) }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <flux:badge :color="$colorPago" size="sm">{{ $pago->estado_label }}</flux:badge>
                                    </td>
                                    <td class="px-6 py-3 text-zinc-400 text-xs">
                                        {{ $pago->created_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Notas --}}
            @if($pedido->notas)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-2">
                    <flux:heading size="sm">Notas</flux:heading>
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ $pedido->notas }}</flux:text>
                </div>
            @endif

        </div>

        {{-- ─── Sidebar ──────────────────────────────── --}}
        <div class="w-72 shrink-0 space-y-4">

            {{-- Cambiar estado --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
                <flux:heading size="sm">Estado del pedido</flux:heading>
                <div
                    x-data="{ estado: '{{ $pedido->estado }}', guardando: false }"
                    class="space-y-3"
                >
                    <flux:select x-model="estado" class="w-full">
                        @foreach(['pendiente','pagado','preparando','enviado','entregado','cancelado'] as $e)
                            <flux:select.option value="{{ $e }}" :selected="$pedido->estado === '{{ $e }}'">
                                {{ ucfirst($e) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <button
                        type="button"
                        :disabled="guardando"
                        @click="
                            guardando = true;
                            fetch('{{ route('admin.pedidos.estado', $pedido) }}', {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ estado: estado })
                            })
                            .then(r => r.json())
                            .then(d => { if(d.ok) window.location.reload(); })
                            .finally(() => { guardando = false; });
                        "
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 text-sm font-medium disabled:opacity-40 hover:opacity-90 transition-opacity"
                    >
                        <span x-text="guardando ? 'Guardando…' : 'Actualizar estado'"></span>
                    </button>
                </div>
            </div>

            {{-- Cliente --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                <flux:heading size="sm">Cliente</flux:heading>
                <div class="space-y-1.5">
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $pedido->nombre }}</p>
                    <p class="text-xs text-zinc-400">{{ $pedido->email }}</p>
                    <p class="text-xs text-zinc-400">{{ $pedido->telefono }}</p>
                    @if($pedido->usuario)
                        <flux:badge color="blue" size="sm" icon="user">Usuario registrado</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Invitado</flux:badge>
                    @endif
                </div>
            </div>

            {{-- Fechas --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                <flux:heading size="sm">Registro</flux:heading>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Creado</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $pedido->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizado</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $pedido->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            {{-- WhatsApp --}}
            
                href="https://wa.me/{{ preg_replace('/\D/', '', $pedido->telefono) }}?text={{ urlencode('Hola ' . $pedido->nombre . ', te contactamos de 1310 Studio sobre tu pedido ' . $pedido->numero . '.') }}"
                target="_blank"
                class="flex items-center justify-center gap-2 w-full rounded-lg border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 px-4 py-2.5 text-sm hover:border-zinc-400 transition-colors"
            >
                <flux:icon name="chat-bubble-left-ellipsis" class="size-4" />
                Contactar por WhatsApp
            </a>

            {{-- Eliminar --}}
            <form method="POST" action="{{ route('admin.pedidos.destroy', $pedido) }}"
                  onsubmit="return confirm('¿Eliminar este pedido? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full text-xs text-red-500 hover:text-red-700 transition-colors py-2 text-center">
                    Eliminar pedido
                </button>
            </form>

        </div>
    </div>

</div>
</x-layouts::app>