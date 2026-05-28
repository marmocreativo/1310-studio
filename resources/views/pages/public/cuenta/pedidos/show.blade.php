<x-layouts::cuenta title="Pedido {{ $pedido->numero }}">
<div class="space-y-8"
     x-data="{ modalCancelar: false, modalFecha: false, modalReporte: false }">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="space-y-1">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="font-serif text-3xl text-on-surface">Pedido</h1>
                <span class="font-mono text-xl text-on-surface-variant">{{ $pedido->numero }}</span>
            </div>
            @include('pages.public.cuenta.partials.badge-estado', ['estado' => $pedido->estado])
        </div>
        <a href="{{ route('cuenta.pedidos') }}"
            class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant hover:text-on-surface transition-colors border-b border-outline-variant pb-0.5">
            ← Mis pedidos
        </a>
    </div>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Productos --}}
            <div class="space-y-4">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                    Productos
                </p>
                <div class="space-y-px">
                    @foreach($pedido->items as $item)
                        <div class="flex gap-4 py-4 border-b border-outline-variant/50">
                            <div class="w-16 h-16 shrink-0 bg-surface-container-low overflow-hidden">
                                @if($item->imagen_url)
                                    <img src="{{ $item->imagen_url }}" alt="{{ $item->nombre_snapshot }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-on-surface">{{ $item->nombre_snapshot }}</p>
                                @if($item->opciones_snapshot)
                                    @foreach($item->opciones_snapshot as $op)
                                        <p class="text-[11px] text-on-surface-variant">{{ $op['tipo'] }}: {{ $op['opcion'] }}</p>
                                    @endforeach
                                @endif
                                <p class="text-xs text-on-surface-variant mt-1">
                                    {{ $item->cantidad }} × ${{ number_format($item->precio_snapshot, 2) }}
                                </p>
                            </div>
                            <p class="text-sm font-medium text-on-surface shrink-0">
                                ${{ number_format($item->subtotal, 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Totales --}}
                <div class="space-y-2 pt-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-on-surface-variant font-light">Subtotal</span>
                        <span>${{ number_format($pedido->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-on-surface-variant font-light">Envío</span>
                        <span>{{ $pedido->costo_envio > 0 ? '$' . number_format($pedido->costo_envio, 2) : 'Gratis' }}</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-outline-variant">
                        <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                        <span class="font-serif text-2xl text-on-surface">${{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Entrega --}}
            <div class="space-y-4">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                    Entrega
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Tipo</p>
                        <p class="text-sm text-on-surface">
                            {{ $pedido->tipo_entrega === 'tienda' ? 'Recoger en tienda' : 'Envío a domicilio' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Fecha</p>
                        <p class="text-sm text-on-surface">
                            {{ $pedido->fecha_entrega?->translatedFormat('d \d\e F, Y') ?? '—' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Horario</p>
                        <p class="text-sm text-on-surface">
                            @php
                                echo match($pedido->bloque_entrega) {
                                    'manana' => 'Mañana (9:00 - 13:00)',
                                    'tarde'  => 'Tarde (13:00 - 18:00)',
                                    'noche'  => 'Noche (18:00 - 21:00)',
                                    default  => '—',
                                };
                            @endphp
                        </p>
                    </div>
                    @if($pedido->tipo_entrega === 'envio')
                        <div class="space-y-1 col-span-2">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Dirección</p>
                            <p class="text-sm text-on-surface">
                                {{ $pedido->calle }} {{ $pedido->numero_ext }}
                                @if($pedido->numero_int) Int. {{ $pedido->numero_int }} @endif,
                                {{ $pedido->colonia }}, CP {{ $pedido->cp }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones --}}
            @if(in_array($pedido->estado, ['pendiente', 'pagado', 'preparando']))
                <div class="space-y-4">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Acciones
                    </p>
                    <div class="flex flex-wrap gap-3">
                        {{-- Cambio de fecha --}}
                        <button @click="modalFecha = true"
                            class="border border-outline-variant text-on-surface-variant px-5 py-2.5 text-xs tracking-[0.15em] uppercase hover:border-on-surface hover:text-on-surface transition-colors">
                            Solicitar cambio de fecha
                        </button>

                        {{-- Reportar problema --}}
                        <button @click="modalReporte = true"
                            class="border border-outline-variant text-on-surface-variant px-5 py-2.5 text-xs tracking-[0.15em] uppercase hover:border-on-surface hover:text-on-surface transition-colors">
                            Reportar problema
                        </button>

                        {{-- Cancelar --}}
                        @if(in_array($pedido->estado, ['pendiente', 'pagado']))
                            <button @click="modalCancelar = true"
                                class="border border-red-200 text-red-600 px-5 py-2.5 text-xs tracking-[0.15em] uppercase hover:bg-red-50 transition-colors">
                                Cancelar pedido
                            </button>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Pago --}}
            <div class="border border-outline-variant p-5 space-y-3">
                <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Pago</p>
                @if($pedido->pago)
                    <div class="flex items-center gap-2">
                        @php
                            $pagoColor = match($pedido->pago->estado) {
                                'aprobado' => 'bg-green-500',
                                'pendiente' => 'bg-yellow-500',
                                default => 'bg-zinc-400',
                            };
                        @endphp
                        <div class="w-2 h-2 rounded-full {{ $pagoColor }}"></div>
                        <p class="text-sm text-on-surface capitalize">{{ $pedido->pago->estado }}</p>
                    </div>
                    <p class="text-xs text-on-surface-variant capitalize">{{ $pedido->pago->metodo }}</p>
                @else
                    <p class="text-sm text-on-surface-variant font-light">Sin información de pago</p>
                @endif
            </div>

            {{-- Destinatario --}}
            @if($pedido->destinatario_nombre)
                <div class="border border-outline-variant p-5 space-y-3">
                    <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Destinatario</p>
                    <p class="text-sm text-on-surface">{{ $pedido->destinatario_nombre }}</p>
                    @if($pedido->destinatario_telefono)
                        <p class="text-xs text-on-surface-variant">{{ $pedido->destinatario_telefono }}</p>
                    @endif
                    @if($pedido->mensaje_tarjeta)
                        <p class="text-xs text-on-surface-variant italic border-l-2 border-outline-variant pl-3 leading-relaxed">
                            "{{ $pedido->mensaje_tarjeta }}"
                        </p>
                    @endif
                </div>
            @endif

            {{-- Notas --}}
            @if($pedido->notas)
                <div class="border border-outline-variant p-5 space-y-3">
                    <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Notas</p>
                    <p class="text-xs text-on-surface-variant font-light leading-relaxed whitespace-pre-line">{{ $pedido->notas }}</p>
                </div>
            @endif

            {{-- WhatsApp --}}
            <a href="https://wa.me/5212345678?text={{ urlencode('Hola, tengo una consulta sobre mi pedido ' . $pedido->numero) }}"
                target="_blank"
                class="flex items-center justify-center gap-2 border border-outline-variant text-on-surface-variant px-5 py-3 text-xs tracking-[0.15em] uppercase hover:border-on-surface hover:text-on-surface transition-colors w-full">
                <flux:icon name="chat-bubble-left-ellipsis" class="w-4 h-4" />
                Contactar por WhatsApp
            </a>
        </div>
    </div>

    {{-- ── Modal Cancelar ── --}}
    <div x-show="modalCancelar" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-zinc-900 border border-outline-variant p-8 max-w-md w-full space-y-6"
             @click.outside="modalCancelar = false">
            <div class="space-y-1">
                <h2 class="font-serif text-2xl text-on-surface">Cancelar pedido</h2>
                <p class="text-sm text-on-surface-variant font-light">Esta acción no se puede deshacer.</p>
            </div>
            <form method="POST" action="{{ route('cuenta.pedidos.cancelar', $pedido->numero) }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Motivo *</label>
                    <textarea name="motivo" rows="3" required
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface resize-none"
                        placeholder="¿Por qué deseas cancelar este pedido?"></textarea>
                    @error('motivo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modalCancelar = false"
                        class="flex-1 border border-outline-variant text-on-surface-variant py-3 text-xs tracking-[0.15em] uppercase hover:border-on-surface transition-colors">
                        Volver
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-600 text-white py-3 text-xs tracking-[0.15em] uppercase hover:bg-red-700 transition-colors">
                        Cancelar pedido
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal Cambio de fecha ── --}}
    <div x-show="modalFecha" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-zinc-900 border border-outline-variant p-8 max-w-md w-full space-y-6"
             @click.outside="modalFecha = false">
            <h2 class="font-serif text-2xl text-on-surface">Solicitar cambio de fecha</h2>
            <form method="POST" action="{{ route('cuenta.pedidos.cambio-fecha', $pedido->numero) }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nueva fecha *</label>
                    <input type="date" name="nueva_fecha" required
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface" />
                    @error('nueva_fecha') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Horario *</label>
                    <select name="nuevo_bloque" required
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface appearance-none">
                        <option value="">Selecciona un horario</option>
                        <option value="manana">Mañana (9:00 - 13:00)</option>
                        <option value="tarde">Tarde (13:00 - 18:00)</option>
                        <option value="noche">Noche (18:00 - 21:00)</option>
                    </select>
                    @error('nuevo_bloque') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                        Motivo <span class="normal-case font-light">(opcional)</span>
                    </label>
                    <textarea name="motivo" rows="2"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface resize-none"
                        placeholder="¿Por qué necesitas cambiar la fecha?"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modalFecha = false"
                        class="flex-1 border border-outline-variant text-on-surface-variant py-3 text-xs tracking-[0.15em] uppercase hover:border-on-surface transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-on-surface text-surface py-3 text-xs tracking-[0.15em] uppercase hover:opacity-80 transition-colors">
                        Enviar solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal Reporte ── --}}
    <div x-show="modalReporte" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-zinc-900 border border-outline-variant p-8 max-w-md w-full space-y-6"
             @click.outside="modalReporte = false">
            <h2 class="font-serif text-2xl text-on-surface">Reportar problema</h2>
            <form method="POST" action="{{ route('cuenta.pedidos.reporte', $pedido->numero) }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Tipo de problema *</label>
                    <select name="tipo" required
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface appearance-none">
                        <option value="">Selecciona una opción</option>
                        <option value="dano">Producto dañado</option>
                        <option value="faltante">Producto faltante</option>
                        <option value="retraso">Retraso en entrega</option>
                        <option value="otro">Otro</option>
                    </select>
                    @error('tipo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Descripción *</label>
                    <textarea name="descripcion" rows="4" required
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface resize-none"
                        placeholder="Describe el problema con detalle…"></textarea>
                    @error('descripcion') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modalReporte = false"
                        class="flex-1 border border-outline-variant text-on-surface-variant py-3 text-xs tracking-[0.15em] uppercase hover:border-on-surface transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-on-surface text-surface py-3 text-xs tracking-[0.15em] uppercase hover:opacity-80 transition-colors">
                        Enviar reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts::cuenta>