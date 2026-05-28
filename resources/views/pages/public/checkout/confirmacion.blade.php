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
                                    {{ $pedido->calle }} {{ $pedido->numero_ext }}
                                    @if($pedido->numero_int) Int. {{ $pedido->numero_int }} @endif,
                                    {{ $pedido->colonia }},
                                    {{ $pedido->municipio?->nombre ?? '' }}
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
                
                @if($pedido->pago?->metodo === 'transferencia')
                    <section class="space-y-4 border border-outline-variant p-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                            Datos para transferencia
                        </p>
                        <p class="text-sm text-on-surface-variant font-light leading-relaxed">
                            Recibirás un correo con los datos bancarios para realizar tu transferencia vía SPEI.
                            Una vez confirmado el pago procesaremos tu pedido.
                        </p>
                        <div class="space-y-1 pt-2">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Referencia</p>
                            <p class="text-sm font-medium text-on-surface font-mono">{{ $pedido->numero }}</p>
                        </div>
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

    {{-- Dialog fechas importantes (solo usuarios autenticados) --}}
@auth
<div
    x-data="fechasDialog()"
    x-init="init()"
    x-show="visible"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0,0,0,0.5)">

    <div class="bg-white dark:bg-zinc-900 border border-outline-variant p-10 max-w-md w-full space-y-6">

        <div class="space-y-2">
            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">1310 Studio</p>
            <h2 class="font-serif text-2xl text-on-surface leading-tight">
                Celebra cada momento especial
            </h2>
            <p class="text-sm text-on-surface-variant font-light leading-relaxed">
                Si esta compra es para conmemorar una fecha especial, regístrala y el próximo año
                te avisaremos con anticipación — y te haremos llegar promociones exclusivas
                para que puedas celebrarla con el arreglo perfecto.
            </p>
        </div>

        <div x-show="!guardado" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Día *</label>
                    <select x-model="dia"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface appearance-none">
                        <option value="">—</option>
                        <template x-for="d in diasDisponibles" :key="d">
                            <option :value="d" x-text="d"></option>
                        </template>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Mes *</label>
                    <select x-model.number="mes" x-on:change="ajustarDia()"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface appearance-none">
                        <option value="">—</option>
                        <template x-for="m in meses" :key="m.num">
                            <option :value="m.num" x-text="m.nombre"></option>
                        </template>
                    </select>
                </div>
                <div class="space-y-2 col-span-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">¿Qué celebramos? *</label>
                    <input type="text" x-model="etiqueta" maxlength="100"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                        placeholder="Aniversario, cumpleaños, graduación…" />
                </div>
            </div>

            <p x-show="error" x-text="error" class="text-xs text-red-600"></p>

            <div class="flex gap-3">
                <button type="button" x-on:click="visible = false"
                    class="flex-1 border border-outline-variant text-on-surface-variant py-3 text-xs tracking-[0.2em] uppercase hover:border-on-surface transition-colors">
                    Ahora no
                </button>
                <button type="button" x-on:click="guardar()" :disabled="guardando"
                    class="flex-1 bg-on-surface text-surface py-3 text-xs tracking-[0.2em] uppercase hover:opacity-80 transition-colors disabled:opacity-40">
                    <span x-text="guardando ? 'Guardando…' : 'Registrar fecha'"></span>
                </button>
            </div>
        </div>

        {{-- Estado guardado --}}
        <div x-show="guardado" class="text-center space-y-4 py-4">
            <flux:icon name="check-circle" class="w-12 h-12 mx-auto text-on-surface" />
            <p class="font-serif text-xl text-on-surface">¡Fecha registrada!</p>
            <p class="text-sm text-on-surface-variant font-light">
                Te avisaremos antes de que llegue el momento especial.
            </p>
            <button type="button" x-on:click="visible = false"
                class="text-[11px] tracking-[0.15em] uppercase border-b border-outline-variant pb-0.5 text-on-surface-variant hover:text-on-surface transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('fechasDialog', () => ({
        visible:   false,
        guardado:  false,
        guardando: false,
        error:     '',
        dia:       '',
        mes:       '',
        etiqueta:  '',

        meses: [
            { num: 1,  nombre: 'Enero' },    { num: 2,  nombre: 'Febrero' },
            { num: 3,  nombre: 'Marzo' },    { num: 4,  nombre: 'Abril' },
            { num: 5,  nombre: 'Mayo' },     { num: 6,  nombre: 'Junio' },
            { num: 7,  nombre: 'Julio' },    { num: 8,  nombre: 'Agosto' },
            { num: 9,  nombre: 'Septiembre' },{ num: 10, nombre: 'Octubre' },
            { num: 11, nombre: 'Noviembre' },{ num: 12, nombre: 'Diciembre' },
        ],

        get diasEnMes() {
            if (!this.mes) return 31;
            return [31,29,31,30,31,30,31,31,30,31,30,31][this.mes - 1];
        },

        get diasDisponibles() {
            return Array.from({ length: this.diasEnMes }, (_, i) => i + 1);
        },

        ajustarDia() {
            if (this.dia > this.diasEnMes) this.dia = '';
        },

        init() {
            // Esperar 5 segundos antes de mostrar el dialog
            setTimeout(() => { this.visible = true; }, 5000);
        },

        async guardar() {
            this.error = '';

            if (!this.dia || !this.mes || !this.etiqueta.trim()) {
                this.error = 'Completa todos los campos.';
                return;
            }

            this.guardando = true;

            try {
                const res = await fetch('{{ route('cuenta.fechas.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        dia:      this.dia,
                        mes:      this.mes,
                        etiqueta: this.etiqueta,
                    }),
                });

                if (res.ok) {
                    this.guardado = true;
                } else {
                    this.error = 'No se pudo guardar la fecha.';
                }
            } catch (e) {
                this.error = 'Error de conexión.';
            } finally {
                this.guardando = false;
            }
        },
    }));
});
</script>
@endauth

</x-layouts::public>