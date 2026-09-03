<x-layouts::public title="Checkout — Paso 3">
<div class="py-16 px-8 max-w-[800px] mx-auto">

    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
        <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
        <span class="mx-2">·</span>
        <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
        <span class="mx-2">·</span>
        Checkout
    </p>

    @include('pages.public.checkout.partials.pasos', ['pasoActual' => 3])

    <h1 class="font-serif text-3xl text-on-surface mb-8">Pago</h1>

    @if(session('error'))
        <div class="mb-6 border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">

        {{-- Formulario --}}
        <div class="lg:col-span-3 space-y-8">

            <form method="POST" action="{{ route('checkout.paso3.store') }}"
                  x-data="{ metodo: '{{ old('metodo_pago', 'tarjeta') }}', enviando: false }"
                  @submit="enviando = true">
                @csrf

                {{-- Métodos de pago --}}
                <div class="space-y-4">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Método de pago
                    </p>
                    <div class="space-y-3">
                        @foreach([
                            'tarjeta'        => ['label' => 'Tarjeta de crédito o débito',    'desc' => 'Visa, Mastercard, American Express'],
                            'efectivo'       => ['label' => 'Pago en tienda de conveniencia', 'desc' => 'OXXO, 7-Eleven, Farmacias del Ahorro'],
                            'transferencia'  => ['label' => 'Transferencia bancaria',          'desc' => 'SPEI - recibirás los datos por correo'],
                            'contra_entrega' => ['label' => 'Pago contra entrega',            'desc' => 'Solo disponible para recoger en tienda'],
                        ] as $valor => $info)
                            @php
                                $esContraEntrega = $valor === 'contra_entrega';
                                $contraEntregaDesactivada = $esContraEntrega && $paso2['tipo_entrega'] !== 'tienda';
                            @endphp
                            @php
                                $clickAttr = $contraEntregaDesactivada ? '' : "x-on:click=\"metodo = '{$valor}'\"";
                            @endphp
                            <label
                                :class="{
                                    'border-on-surface bg-surface-container-low': metodo === '{{ $valor }}',
                                    'border-outline-variant hover:border-outline cursor-pointer': metodo !== '{{ $valor }}' && !{{ $contraEntregaDesactivada ? 'true' : 'false' }},
                                    'opacity-40 cursor-not-allowed': {{ $contraEntregaDesactivada ? 'true' : 'false' }}
                                }"
                                {!! $clickAttr !!}
                                class="flex items-center gap-4 border p-5 transition-colors">
                                <input type="radio" name="metodo_pago" value="{{ $valor }}"
                                    x-model="metodo"
                                    @if($contraEntregaDesactivada) disabled @endif
                                    class="sr-only">
                                <div class="w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                    <div x-show="metodo === '{{ $valor }}'" class="w-2 h-2 bg-on-surface"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">{{ $info['label'] }}</p>
                                    <p class="text-xs text-on-surface-variant font-light mt-0.5">{{ $info['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('metodo_pago') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Notas --}}
                <div class="space-y-3">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Notas adicionales <span class="normal-case font-light">(opcional)</span>
                    </p>
                    <textarea name="notas" rows="3"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                        placeholder="Instrucciones especiales, alergias, preferencias…">{{ old('notas') }}</textarea>
                </div>

                <div class="flex justify-between pt-4">
                    <a href="{{ route('checkout.paso2') }}"
                        class="border border-outline-variant text-on-surface-variant px-8 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface hover:text-on-surface transition-colors">
                        Anterior
                    </a>
                    <button type="submit" :disabled="enviando"
                        :class="enviando ? 'opacity-60 cursor-wait' : 'hover:opacity-80'"
                        class="bg-on-surface text-surface px-10 py-4 text-xs tracking-[0.3em] uppercase transition-colors flex items-center gap-2">
                        <template x-if="enviando">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </template>
                        <span x-text="enviando ? 'Procesando…' : 'Confirmar pedido'"></span>
                    </button>
                </div>

                <p class="text-[10px] text-on-surface-variant text-center leading-relaxed">
                    Al confirmar aceptas nuestros
                    <a href="{{ route('paginas.show', 'terminos-y-condiciones') }}"
                       class="underline hover:text-on-surface transition-colors" target="_blank">términos y condiciones</a>
                </p>
            </form>
        </div>

        {{-- Resumen --}}
        <div class="lg:col-span-2">
            <div class="border border-outline-variant p-6 space-y-5 sticky top-8">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Resumen</p>

                {{-- Items --}}
                <div class="space-y-4">
                    @foreach($carrito->items as $item)
                        <div class="flex gap-3">
                            <div class="w-12 h-12 shrink-0 overflow-hidden bg-surface-container-low">
                                @if($item->imagen_url)
                                    <img src="{{ $item->imagen_url }}" alt="{{ $item->nombre_snapshot }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-on-surface truncate">{{ $item->nombre_snapshot }}</p>
                                <p class="text-[10px] text-on-surface-variant mt-0.5">
                                    {{ $item->cantidad }} × ${{ number_format($item->precio_snapshot, 2) }}
                                </p>
                            </div>
                            <p class="text-xs text-on-surface shrink-0">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Totales --}}
                <div class="border-t border-outline-variant pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-on-surface-variant font-light">Subtotal</span>
                        <span>${{ number_format($carrito->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-on-surface-variant font-light">Envío</span>
                        <span>
                            @if($paso2['tipo_entrega'] === 'tienda')
                                Gratis
                            @elseif($costoEnvio > 0)
                                ${{ number_format($costoEnvio, 2) }}
                            @else
                                Por definir
                            @endif
                        </span>
                    </div>
                </div>

                <div class="border-t border-outline-variant pt-4 flex justify-between items-baseline">
                    <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                    <span class="font-serif text-2xl text-on-surface">${{ number_format($total, 2) }}</span>
                </div>

                {{-- Datos del paso 1 --}}
                <div class="border-t border-outline-variant pt-4 space-y-1">
                    <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant mb-2">Contacto</p>
                    <p class="text-xs text-on-surface">{{ $paso1['nombre'] }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $paso1['email'] }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $paso1['telefono'] }}</p>
                </div>

                {{-- Datos del paso 2 --}}
                <div class="border-t border-outline-variant pt-4 space-y-1">
                    <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant mb-2">Entrega</p>
                    @if($paso2['tipo_entrega'] === 'tienda')
                        <p class="text-xs text-on-surface">Recoger en tienda</p>
                    @else
                        <p class="text-xs text-on-surface">
                            {{ $paso2['calle'] }} {{ $paso2['numero_ext'] }}
                            @if(!empty($paso2['numero_int'])) Int. {{ $paso2['numero_int'] }} @endif
                        </p>
                        <p class="text-xs text-on-surface-variant">{{ $paso2['colonia'] }}, CP {{ $paso2['cp'] }}</p>
                    @endif
                    @php
                        $bloqueLabel = match($paso2['bloque_entrega']) {
                            'manana' => '9:00 - 13:00',
                            'tarde'  => '13:00 - 18:00',
                            'noche'  => '18:00 - 21:00',
                            default  => '',
                        };
                    @endphp
                    <p class="text-xs text-on-surface-variant mt-1">
                        {{ \Carbon\Carbon::parse($paso2['fecha_entrega'])->translatedFormat('d \d\e F, Y') }} · {{ $bloqueLabel }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
</x-layouts::public>