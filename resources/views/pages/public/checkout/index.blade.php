<x-layouts::public title="Checkout">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
            <span class="mx-2">·</span>
            <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
            <span class="mx-2">·</span>
            Checkout
        </p>

        <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight mb-12">
            Finalizar pedido
        </h1>

        @if(session('error'))
            <div class="mb-8 border border-red-200 bg-red-50 px-6 py-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('checkout.procesar') }}"
            id="form-checkout"
            x-data="checkoutManager({
                zonas: {{ $zonas->map(fn($z) => [
                    'id'     => $z->id,
                    'nombre' => $z->nombre,
                    'precio' => (float) $z->precio,
                    'alcaldias' => $z->alcaldias->pluck('nombre')->toArray(),
                ])->values()->toJson() }},
                subtotal: {{ $carrito->subtotal }},
            })"
        >
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

                {{-- ─── COLUMNA PRINCIPAL ───────────────── --}}
                <div class="lg:col-span-2 space-y-10">

                    {{-- 1. DATOS DEL COMPRADOR --}}
                    <section class="space-y-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            1 · Datos de contacto
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Nombre completo *
                                </label>
                                <input
                                    type="text"
                                    name="nombre"
                                    value="{{ old('nombre', $usuario?->name) }}"
                                    required
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="Tu nombre"
                                />
                                @error('nombre')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Correo electrónico *
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $usuario?->email) }}"
                                    required
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="correo@ejemplo.com"
                                />
                                @error('email')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Teléfono *
                                </label>
                                <input
                                    type="tel"
                                    name="telefono"
                                    value="{{ old('telefono') }}"
                                    required
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="10 dígitos"
                                />
                                @error('telefono')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- 2. TIPO DE ENTREGA --}}
                    <section class="space-y-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            2 · Tipo de entrega
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label
                                @click="tipoEntrega = 'envio'"
                                :class="tipoEntrega === 'envio'
                                    ? 'border-on-surface bg-surface-container-low'
                                    : 'border-outline-variant hover:border-outline'"
                                class="flex items-start gap-4 border p-5 cursor-pointer transition-colors">
                                <input type="radio" name="tipo_entrega" value="envio"
                                       x-model="tipoEntrega" class="sr-only" />
                                <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                    <div x-show="tipoEntrega === 'envio'"
                                         class="w-2 h-2 bg-on-surface"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">Envío a domicilio</p>
                                    <p class="text-xs text-on-surface-variant mt-1 font-light">
                                        CDMX y zona metropolitana
                                    </p>
                                </div>
                            </label>

                            <label
                                @click="tipoEntrega = 'tienda'"
                                :class="tipoEntrega === 'tienda'
                                    ? 'border-on-surface bg-surface-container-low'
                                    : 'border-outline-variant hover:border-outline'"
                                class="flex items-start gap-4 border p-5 cursor-pointer transition-colors">
                                <input type="radio" name="tipo_entrega" value="tienda"
                                       x-model="tipoEntrega" class="sr-only" />
                                <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                    <div x-show="tipoEntrega === 'tienda'"
                                         class="w-2 h-2 bg-on-surface"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">Recoger en tienda</p>
                                    <p class="text-xs text-on-surface-variant mt-1 font-light">
                                        Sin costo de envío
                                    </p>
                                </div>
                            </label>
                        </div>

                        {{-- Datos de envío --}}
                        <div x-show="tipoEntrega === 'envio'" class="space-y-6">

                            {{-- Zona --}}
                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Zona de entrega *
                                </label>
                                <select
                                    name="id_zona"
                                    x-model.number="zonaSeleccionada"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors appearance-none"
                                >
                                    <option value="">Selecciona tu zona</option>
                                    @foreach($zonas as $zona)
                                        <option value="{{ $zona->id }}"
                                            {{ old('id_zona') == $zona->id ? 'selected' : '' }}>
                                            {{ $zona->nombre }} — ${{ number_format($zona->precio, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Alcaldías de la zona seleccionada --}}
                                <template x-if="zonaInfo">
                                    <p class="text-[11px] text-on-surface-variant">
                                        Cubre: <span x-text="zonaInfo.alcaldias.join(', ')"></span>
                                    </p>
                                </template>
                                @error('id_zona')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Calle y número *
                                    </label>
                                    <input
                                        type="text"
                                        name="direccion"
                                        value="{{ old('direccion') }}"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                        placeholder="Av. Ejemplo 123, Int. 4"
                                    />
                                    @error('direccion')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Colonia *
                                    </label>
                                    <input
                                        type="text"
                                        name="colonia"
                                        value="{{ old('colonia') }}"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                        placeholder="Colonia"
                                    />
                                    @error('colonia')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Alcaldía / Municipio *
                                    </label>
                                    <input
                                        type="text"
                                        name="municipio"
                                        value="{{ old('municipio') }}"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                        placeholder="Alcaldía o municipio"
                                    />
                                    @error('municipio')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Código postal *
                                    </label>
                                    <input
                                        type="text"
                                        name="cp"
                                        value="{{ old('cp') }}"
                                        maxlength="5"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                        placeholder="00000"
                                    />
                                    @error('cp')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Referencias de entrega
                                    </label>
                                    <textarea
                                        name="referencias"
                                        rows="2"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                                        placeholder="Entre qué calles, color de la fachada, etc."
                                    >{{ old('referencias') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 3. FECHA Y BLOQUE --}}
                    <section class="space-y-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            3 · Fecha y horario de entrega
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Fecha de entrega *
                                </label>
                                <input
                                    type="date"
                                    name="fecha_entrega"
                                    value="{{ old('fecha_entrega') }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    required
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors"
                                />
                                @error('fecha_entrega')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                Bloque horario *
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @foreach([
                                    'manana' => ['label' => 'Mañana', 'hora' => '9:00 – 13:00', 'icon' => 'sun'],
                                    'tarde'  => ['label' => 'Tarde',  'hora' => '13:00 – 18:00', 'icon' => 'cloud'],
                                    'noche'  => ['label' => 'Noche',  'hora' => '18:00 – 21:00', 'icon' => 'moon'],
                                ] as $valor => $bloque)
                                    <label
                                        class="flex flex-col items-center gap-2 border p-5 cursor-pointer transition-colors"
                                        :class="bloqueEntrega === '{{ $valor }}'
                                            ? 'border-on-surface bg-surface-container-low'
                                            : 'border-outline-variant hover:border-outline'"
                                        @click="bloqueEntrega = '{{ $valor }}'">
                                        <input type="radio" name="bloque_entrega" value="{{ $valor }}"
                                               x-model="bloqueEntrega" class="sr-only" />
                                        <flux:icon name="{{ $bloque['icon'] }}" class="w-5 h-5 text-on-surface-variant" />
                                        <span class="text-sm font-medium text-on-surface">{{ $bloque['label'] }}</span>
                                        <span class="text-[11px] text-on-surface-variant">{{ $bloque['hora'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('bloque_entrega')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>

                    {{-- 4. DESTINATARIO --}}
                    <section class="space-y-6">
                        <div class="flex items-center justify-between border-b border-outline-variant pb-4">
                            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                                4 · Destinatario (opcional)
                            </p>
                            <button
                                type="button"
                                @click="mostrarDestinatario = !mostrarDestinatario"
                                class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant hover:text-on-surface transition-colors underline">
                                <span x-text="mostrarDestinatario ? 'Ocultar' : 'Es un regalo'"></span>
                            </button>
                        </div>

                        <div x-show="mostrarDestinatario" class="space-y-6">
                            <p class="text-xs text-on-surface-variant font-light">
                                Si el pedido es para alguien más, ingresa sus datos. Si no, se usarán los tuyos.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Nombre del destinatario
                                    </label>
                                    <input
                                        type="text"
                                        name="destinatario_nombre"
                                        value="{{ old('destinatario_nombre') }}"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                        placeholder="Nombre de quien recibe"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Teléfono del destinatario
                                    </label>
                                    <input
                                        type="tel"
                                        name="destinatario_telefono"
                                        value="{{ old('destinatario_telefono') }}"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                        placeholder="10 dígitos"
                                    />
                                </div>
                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                        Mensaje para la tarjeta
                                    </label>
                                    <textarea
                                        name="mensaje_tarjeta"
                                        rows="3"
                                        maxlength="500"
                                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                                        placeholder="Tu mensaje para acompañar el arreglo…"
                                    >{{ old('mensaje_tarjeta') }}</textarea>
                                    <p class="text-[10px] text-on-surface-variant text-right">Máximo 500 caracteres</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 5. MÉTODO DE PAGO --}}
                    <section class="space-y-6">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            5 · Método de pago
                        </p>

                        <div class="space-y-3">
                            @foreach([
                                'tarjeta'        => ['label' => 'Tarjeta de crédito o débito',    'desc' => 'Visa, Mastercard, American Express'],
                                'efectivo'       => ['label' => 'Pago en tienda de conveniencia', 'desc' => 'OXXO, 7-Eleven, Farmacias del Ahorro'],
                                'contra_entrega' => ['label' => 'Pago contra entrega',            'desc' => 'Solo disponible para recoger en tienda'],
                            ] as $valor => $metodo)
                                <label
                                    @click="metodoPago = '{{ $valor }}'"
                                    :class="metodoPago === '{{ $valor }}'
                                        ? 'border-on-surface bg-surface-container-low'
                                        : 'border-outline-variant hover:border-outline'"
                                    class="flex items-center gap-4 border p-5 cursor-pointer transition-colors"
                                    x-bind:class="'{{ $valor }}' === 'contra_entrega' && tipoEntrega !== 'tienda' ? 'opacity-40 pointer-events-none' : ''"
                                >
                                    <input type="radio" name="metodo_pago" value="{{ $valor }}"
                                           x-model="metodoPago" class="sr-only" />
                                    <div class="w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                        <div x-show="metodoPago === '{{ $valor }}'"
                                             class="w-2 h-2 bg-on-surface"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-on-surface">{{ $metodo['label'] }}</p>
                                        <p class="text-xs text-on-surface-variant font-light mt-0.5">{{ $metodo['desc'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('metodo_pago')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Nota contra entrega --}}
                        <template x-if="metodoPago === 'contra_entrega' && tipoEntrega === 'tienda'">
                            <p class="text-xs text-on-surface-variant font-light border-l-2 border-outline-variant pl-4">
                                Pagarás cuando recojas tu pedido en la tienda. Te enviaremos un correo con los detalles.
                            </p>
                        </template>
                    </section>

                    {{-- 6. NOTAS --}}
                    <section class="space-y-4">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-4">
                            6 · Notas adicionales
                        </p>
                        <textarea
                            name="notas"
                            rows="3"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                            placeholder="Instrucciones especiales, alergias, preferencias…"
                        >{{ old('notas') }}</textarea>
                    </section>

                </div>

                {{-- ─── RESUMEN ──────────────────────────── --}}
                <div class="lg:col-span-1">
                    <div class="border border-outline-variant p-8 space-y-6 sticky top-8">

                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                            Tu pedido
                        </p>

                        {{-- Items --}}
                        <div class="space-y-4">
                            @foreach($carrito->items as $item)
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 shrink-0 overflow-hidden bg-surface-container-low">
                                        @if($item->imagen_url)
                                            <img src="{{ $item->imagen_url }}"
                                                 alt="{{ $item->nombre_snapshot }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-on-surface truncate">
                                            {{ $item->nombre_snapshot }}
                                        </p>
                                        @if($item->opciones_snapshot)
                                            @foreach($item->opciones_snapshot as $op)
                                                <p class="text-[10px] text-on-surface-variant">
                                                    {{ $op['opcion'] }}
                                                </p>
                                            @endforeach
                                        @endif
                                        <p class="text-[10px] text-on-surface-variant mt-0.5">
                                            {{ $item->cantidad }} × ${{ number_format($item->precio_snapshot, 2) }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-on-surface shrink-0">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-outline-variant pt-4 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant font-light">Subtotal</span>
                                <span class="text-on-surface">${{ number_format($carrito->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant font-light">Envío</span>
                                <span class="text-on-surface" x-text="costoEnvioFormateado"></span>
                            </div>
                        </div>

                        <div class="border-t border-outline-variant pt-4 flex justify-between items-baseline">
                            <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                            <span class="font-serif text-2xl text-on-surface" x-text="totalFormateado"></span>
                        </div>

                        <button
                            type="submit"
                            form="form-checkout"
                            :disabled="enviando"
                            :class="enviando ? 'opacity-60 cursor-wait' : 'hover:opacity-90'"
                            class="w-full bg-primary text-on-primary px-6 py-4 text-xs tracking-[0.3em] uppercase transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <template x-if="enviando">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                            </template>
                            <span x-text="enviando ? 'Procesando…' : 'Confirmar pedido'"></span>
                        </button>

                        <p class="text-[10px] tracking-[0.05em] text-on-surface-variant text-center leading-relaxed">
                            Al confirmar aceptas nuestros
                            <a href="{{ route('paginas.show', 'terminos-y-condiciones') }}"
                               class="underline hover:text-on-surface transition-colors" target="_blank">
                                términos y condiciones
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </form>

    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutManager', (config) => ({

            tipoEntrega:        '{{ old('tipo_entrega', 'envio') }}',
            zonaSeleccionada:   {{ old('id_zona', 0) }},
            bloqueEntrega:      '{{ old('bloque_entrega', '') }}',
            metodoPago:         '{{ old('metodo_pago', 'tarjeta') }}',
            mostrarDestinatario: {{ old('destinatario_nombre') ? 'true' : 'false' }},
            enviando:           false,

            get zonaInfo() {
                if (!this.zonaSeleccionada) return null;
                return config.zonas.find(z => z.id === this.zonaSeleccionada) ?? null;
            },

            get costoEnvio() {
                if (this.tipoEntrega === 'tienda') return 0;
                return this.zonaInfo ? this.zonaInfo.precio : 0;
            },

            get total() {
                return config.subtotal + this.costoEnvio;
            },

            get costoEnvioFormateado() {
                if (this.tipoEntrega === 'tienda') return 'Gratis';
                if (!this.zonaInfo) return 'Por definir';
                return '$' + this.costoEnvio.toLocaleString('es-MX', {
                    minimumFractionDigits: 2, maximumFractionDigits: 2
                });
            },

            get totalFormateado() {
                return '$' + this.total.toLocaleString('es-MX', {
                    minimumFractionDigits: 2, maximumFractionDigits: 2
                });
            },

            init() {
                // Si cambia a recoger en tienda y tenía contra_entrega, mantenerlo
                // Si cambia a envío y tenía contra_entrega, resetear
                this.$watch('tipoEntrega', (val) => {
                    if (val === 'envio' && this.metodoPago === 'contra_entrega') {
                        this.metodoPago = 'tarjeta';
                    }
                });

                // Spinner al enviar
                document.getElementById('form-checkout').addEventListener('submit', () => {
                    this.enviando = true;
                });
            },
        }));
    });
    </script>

</x-layouts::public>