<x-layouts::public :title="'Pedido ' . $pedido->numero">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Estado del pedido --}}
        @php
            $pagado = $pedido->pago?->estado === 'aprobado'
                   || $pedido->estado === 'pagado'
                   || $pedido->pago?->metodo === 'contra_entrega';

            $pendiente = $pedido->pago?->estado === 'pendiente'
                      && $pedido->pago?->metodo !== 'contra_entrega';
        @endphp

        {{-- Hero de confirmación --}}
        <div class="text-center mb-16 max-w-xl mx-auto">
            <div class="w-16 h-16 border border-outline-variant flex items-center justify-center mx-auto mb-6">
                @if($pagado)
                    <flux:icon name="check" class="w-7 h-7 text-on-surface" />
                @elseif($pendiente)
                    <flux:icon name="clock" class="w-7 h-7 text-on-surface-variant" />
                @else
                    <flux:icon name="document-text" class="w-7 h-7 text-on-surface-variant" />
                @endif
            </div>

            @if($pagado)
                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight mb-4">
                    ¡Gracias por tu pedido!
                </h1>
                <p class="text-on-surface-variant font-light leading-relaxed">
                    Tu pago fue confirmado. Recibirás un correo a
                    <span class="text-on-surface">{{ $pedido->email }}</span>
                    con los detalles de tu pedido.
                </p>
            @elseif($pendiente)
                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight mb-4">
                    Pago pendiente
                </h1>
                <p class="text-on-surface-variant font-light leading-relaxed">
                    Tu pedido fue registrado. Completa el pago para confirmar tu entrega.
                    Tienes 24 horas antes de que expire.
                </p>
            @else
                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight mb-4">
                    Pedido registrado
                </h1>
                <p class="text-on-surface-variant font-light leading-relaxed">
                    Tu pedido fue registrado correctamente. Te esperamos en la tienda.
                </p>
            @endif

            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mt-6">
                Número de pedido:
                <span class="text-on-surface font-medium ml-1">{{ $pedido->numero }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

            {{-- ─── DETALLE DEL PEDIDO ──────────────────── --}}
            <div class="lg:col-span-2 space-y-10">

                {{-- Items --}}
                <section class="space-y-6">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                        Productos
                    </p>

                    <div class="space-y-px">
                        @foreach($pedido->items as $item)
                            <div class="flex gap-5 py-5 border-b border-outline-variant/50">
                                <div class="w-20 h-20 shrink-0 overflow-hidden bg-surface-container-low">
                                    @if($item->imagen_url)
                                        <img src="{{ $item->imagen_url }}"
                                             alt="{{ $item->nombre_snapshot }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <flux:icon name="photo" class="w-6 h-6 text-outline-variant" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 flex flex-col justify-center gap-1">
                                    <p class="font-serif text-base text-on-surface">
                                        {{ $item->nombre_snapshot }}
                                    </p>
                                    @if($item->opciones_snapshot)
                                        <div class="flex flex-wrap gap-x-4 gap-y-0.5">
                                            @foreach($item->opciones_snapshot as $op)
                                                <p class="text-[11px] tracking-[0.05em] text-on-surface-variant">
                                                    {{ $op['tipo'] }}: {{ $op['opcion'] }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                    <p class="text-xs text-on-surface-variant font-light">
                                        {{ $item->cantidad }} × ${{ number_format($item->precio_snapshot, 2) }}
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center">
                                    <p class="text-sm font-medium text-on-surface">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totales --}}
                    <div class="space-y-3 pt-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-on-surface-variant font-light">Subtotal</span>
                            <span class="text-on-surface">${{ number_format($pedido->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-on-surface-variant font-light">Envío</span>
                            <span class="text-on-surface">
                                @if($pedido->costo_envio > 0)
                                    ${{ number_format($pedido->costo_envio, 2) }}
                                @else
                                    Gratis
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-outline-variant">
                            <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                            <span class="font-serif text-2xl text-on-surface">
                                ${{ number_format($pedido->total, 2) }}
                            </span>
                        </div>
                    </div>
                </section>

                {{-- Entrega --}}
                <section class="space-y-6">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                        Entrega
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Tipo</p>
                            <p class="text-sm text-on-surface">
                                {{ $pedido->tipo_entrega === 'envio' ? 'Envío a domicilio' : 'Recoger en tienda' }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Fecha</p>
                            <p class="text-sm text-on-surface">
                                {{ $pedido->fecha_entrega?->translatedFormat('d \d\e F, Y') }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Horario</p>
                            <p class="text-sm text-on-surface">{{ $pedido->bloque_entrega_label }}</p>
                        </div>

                        @if($pedido->es_envio)
                            <div class="space-y-1">
                                <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Zona</p>
                                <p class="text-sm text-on-surface">{{ $pedido->zona?->nombre }}</p>
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Dirección</p>
                                <p class="text-sm text-on-surface">
                                    {{ $pedido->direccion }},
                                    {{ $pedido->colonia }},
                                    {{ $pedido->municipio }}
                                    {{ $pedido->cp ? 'CP ' . $pedido->cp : '' }}
                                </p>
                                @if($pedido->referencias)
                                    <p class="text-xs text-on-surface-variant font-light mt-1">
                                        Ref: {{ $pedido->referencias }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Destinatario --}}
                @if($pedido->destinatario_nombre)
                    <section class="space-y-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            Destinatario
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre</p>
                                <p class="text-sm text-on-surface">{{ $pedido->destinatario_nombre }}</p>
                            </div>
                            @if($pedido->destinatario_telefono)
                                <div class="space-y-1">
                                    <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Teléfono</p>
                                    <p class="text-sm text-on-surface">{{ $pedido->destinatario_telefono }}</p>
                                </div>
                            @endif
                            @if($pedido->mensaje_tarjeta)
                                <div class="space-y-1 sm:col-span-2">
                                    <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Mensaje</p>
                                    <p class="text-sm text-on-surface-variant font-light italic leading-relaxed border-l-2 border-outline-variant pl-4">
                                        "{{ $pedido->mensaje_tarjeta }}"
                                    </p>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Pago con OXXO/efectivo pendiente --}}
                @if($pendiente && $pedido->pago?->metodo === 'efectivo')
                    <section class="space-y-4 border border-outline-variant p-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                            Instrucciones de pago
                        </p>
                        <p class="text-sm text-on-surface-variant font-light leading-relaxed">
                            Recibirás un correo de Mercado Pago con el código de pago para realizarlo
                            en cualquier tienda OXXO, 7-Eleven o Farmacias del Ahorro.
                            Una vez confirmado el pago procesaremos tu pedido.
                        </p>
                        <p class="text-xs text-on-surface-variant">
                            Número de referencia: <span class="font-medium text-on-surface">{{ $pedido->numero }}</span>
                        </p>
                    </section>
                @endif

                {{-- Notas --}}
                @if($pedido->notas)
                    <section class="space-y-3">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            Notas
                        </p>
                        <p class="text-sm text-on-surface-variant font-light">{{ $pedido->notas }}</p>
                    </section>
                @endif

            </div>

            {{-- ─── SIDEBAR ──────────────────────────────── --}}
            <div class="lg:col-span-1 space-y-4">

                {{-- Contacto --}}
                <div class="border border-outline-variant p-6 space-y-4">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                        Datos de contacto
                    </p>
                    <div class="space-y-2">
                        <p class="text-sm text-on-surface">{{ $pedido->nombre }}</p>
                        <p class="text-sm text-on-surface-variant font-light">{{ $pedido->email }}</p>
                        <p class="text-sm text-on-surface-variant font-light">{{ $pedido->telefono }}</p>
                    </div>
                </div>

                {{-- Estado del pago --}}
                <div class="border border-outline-variant p-6 space-y-4">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                        Estado del pago
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            @if($pagado)
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <p class="text-sm text-on-surface">Pago confirmado</p>
                            @elseif($pendiente)
                                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                <p class="text-sm text-on-surface">Pago pendiente</p>
                            @else
                                <div class="w-2 h-2 rounded-full bg-zinc-400"></div>
                                <p class="text-sm text-on-surface">{{ $pedido->estado_label }}</p>
                            @endif
                        </div>
                        @if($pedido->pago)
                            <p class="text-xs text-on-surface-variant font-light">
                                {{ $pedido->pago->metodo_label }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- CTA WhatsApp --}}
                <a
                    href="https://wa.me/5212345678?text={{ urlencode('Hola, tengo una pregunta sobre mi pedido ' . $pedido->numero) }}"
                    target="_blank"
                    class="flex items-center justify-center gap-2 w-full border border-outline-variant text-on-surface px-6 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface transition-all duration-300">
                    <flux:icon name="chat-bubble-left-ellipsis" class="w-4 h-4" />
                    Contactar por WhatsApp
                </a>

                {{-- Seguir comprando --}}
                <a href="{{ route('categorias.index') }}"
                   class="block w-full text-center border border-outline-variant text-on-surface-variant px-6 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface hover:text-on-surface transition-all duration-300">
                    Seguir comprando
                </a>

            </div>
        </div>

    </div>

</x-layouts::public>