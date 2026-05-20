<x-layouts::public :title="'Pago — ' . $pedido->numero">

    <div class="py-16 px-8 max-w-3xl mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
            <span class="mx-2">·</span>
            <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
            <span class="mx-2">·</span>
            Pago
        </p>

        <h1 class="font-serif text-4xl text-on-surface leading-tight mb-4">
            Completa tu pago
        </h1>

        <p class="text-on-surface-variant font-light mb-10">
            Pedido <span class="text-on-surface font-medium">{{ $pedido->numero }}</span>
            · Total:
            <span class="font-serif text-xl text-on-surface ml-1">
                ${{ number_format($pedido->total, 2) }}
            </span>
        </p>

        {{-- Resumen del pedido --}}
        <div class="border border-outline-variant p-6 mb-8 space-y-3">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">
                Resumen
            </p>
            @foreach($pedido->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-on-surface-variant font-light">
                        {{ $item->nombre_snapshot }}
                        @if($item->cantidad > 1)
                            <span class="text-xs">× {{ $item->cantidad }}</span>
                        @endif
                    </span>
                    <span class="text-on-surface">${{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
            @if($pedido->costo_envio > 0)
                <div class="flex justify-between text-sm border-t border-outline-variant pt-3">
                    <span class="text-on-surface-variant font-light">Envío</span>
                    <span class="text-on-surface">${{ number_format($pedido->costo_envio, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between items-baseline border-t border-outline-variant pt-3">
                <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                <span class="font-serif text-2xl text-on-surface">
                    ${{ number_format($pedido->total, 2) }}
                </span>
            </div>
        </div>

        {{-- Método de pago --}}
        <div class="border border-outline-variant p-6 mb-8">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-2">
                Método de pago
            </p>
            <p class="text-sm text-on-surface">
                @if($metodo === 'tarjeta')
                    Tarjeta de crédito o débito
                @elseif($metodo === 'efectivo')
                    Tienda de conveniencia (OXXO)
                @else
                    Pago contra entrega
                @endif
            </p>
        </div>

        {{-- Contenedor de Bricks --}}
        <div id="bricks-container">
            {{-- Loader inicial --}}
            <div id="bricks-loader" class="flex items-center justify-center py-16 text-on-surface-variant gap-3">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span class="text-sm">Cargando pasarela de pago…</span>
            </div>
        </div>

        {{-- Error --}}
        <div id="error-pago"
             class="hidden mt-6 border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-700" id="error-pago-msg"></p>
            <a href="{{ route('checkout.index') }}"
               class="inline-block mt-3 text-xs tracking-[0.1em] uppercase text-red-600 underline hover:text-red-800 transition-colors">
                Volver al checkout
            </a>
        </div>

        {{-- Procesando --}}
        <div id="procesando-pago"
             class="hidden mt-6 flex items-center justify-center gap-3 py-8 text-on-surface-variant">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <span class="text-sm">Procesando pago, no cierres esta ventana…</span>
        </div>

    </div>

    {{-- SDK de Mercado Pago --}}
    <script src="https://sdk.mercadopago.com/js/v2"></script>

    <script>
    (async () => {

        const publicKey       = '{{ config('services.mercadopago.public_key') }}';
        const csrfToken       = '{{ csrf_token() }}';
        const metodo          = '{{ $metodo }}';
        const pedidoNumero    = '{{ $pedido->numero }}';
        const totalPedido     = {{ (float) $pedido->total }};
        const urlPreferencia  = '{{ route('pagos.preferencia') }}';
        const urlTarjeta      = '{{ route('pagos.procesar-tarjeta') }}';
        const urlPendiente    = '{{ route('pagos.pendiente') }}';

        function mostrarError(msg) {
            document.getElementById('bricks-loader')?.remove();
            document.getElementById('error-pago').classList.remove('hidden');
            document.getElementById('error-pago-msg').textContent = msg;
        }

        // ── Inicializar MP SDK ──────────────────────────────────────────────
        let mp;
        try {
            mp = new MercadoPago(publicKey, { locale: 'es-MX' });
        } catch(e) {
            mostrarError('No se pudo inicializar la pasarela de pago.');
            return;
        }

        const bricks = mp.bricks();

        // ── CardPayment Brick (tarjeta) ─────────────────────────────────────
        if (metodo === 'tarjeta') {

            try {
                await bricks.create('cardPayment', 'bricks-container', {
                    initialization: {
                        amount: totalPedido,
                    },
                    customization: {
                        visual: {
                            style: { theme: 'default' },
                        },
                        paymentMethods: {
                            minInstallments: 1,
                            maxInstallments: 1,
                        },
                    },
                    callbacks: {
                        onReady: () => {
                            document.getElementById('bricks-loader')?.remove();
                        },
                        onSubmit: async (cardFormData) => {
                            document.getElementById('procesando-pago').classList.remove('hidden');

                            try {
                                const res = await fetch(urlTarjeta, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept':       'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                    },
                                    body: JSON.stringify({
                                        ...cardFormData,
                                        pedido_numero: pedidoNumero,
                                    }),
                                });

                                const data = await res.json();

                                if (data.ok) {
                                    window.location.href = data.redirect;
                                } else {
                                    document.getElementById('procesando-pago').classList.add('hidden');
                                    mostrarError(data.message ?? 'Error al procesar el pago.');
                                }

                            } catch(e) {
                                document.getElementById('procesando-pago').classList.add('hidden');
                                mostrarError('Error de conexión. Intenta de nuevo.');
                            }
                        },
                        onError: (error) => {
                            console.error('CardPayment Brick error:', error);
                            mostrarError('Error en la pasarela de pago. Intenta de nuevo.');
                        },
                    },
                });
            } catch(e) {
                console.error('Error al crear CardPayment Brick:', e);
                mostrarError('No se pudo cargar el formulario de pago.');
            }

        // ── Payment Brick (efectivo / OXXO) ────────────────────────────────
        } else if (metodo === 'efectivo') {

            // Para efectivo necesitamos preference_id
            let preferenceId;
            try {
                const res  = await fetch(urlPreferencia, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ metodo: 'efectivo' }),
                });

                const data = await res.json();

                if (!res.ok || !data.ok) {
                    throw new Error(data.message ?? 'Error al crear la preferencia.');
                }

                preferenceId = data.preference_id;

            } catch(e) {
                mostrarError(e.message);
                return;
            }

            try {
                await bricks.create('payment', 'bricks-container', {
                    initialization: {
                        preferenceId: preferenceId,
                        amount:       totalPedido,
                    },
                    customization: {
                        paymentMethods: {
                            excludedPaymentTypes: ['credit_card', 'debit_card', 'mercado_credito'],
                            maxInstallments: 1,
                        },
                        visual: {
                            style: { theme: 'default' },
                        },
                    },
                    callbacks: {
                        onReady: () => {
                            document.getElementById('bricks-loader')?.remove();
                        },
                        onSubmit: ({ selectedPaymentMethod, formData }) => {
                            return new Promise((resolve) => { resolve(); });
                        },
                        onError: (error) => {
                            console.error('Payment Brick error:', error);
                            mostrarError('Error en la pasarela de pago. Intenta de nuevo.');
                        },
                    },
                });
            } catch(e) {
                console.error('Error al crear Payment Brick:', e);
                mostrarError('No se pudo cargar el formulario de pago.');
            }

        // ── Contra entrega ─────────────────────────────────────────────────
        } else {
            document.getElementById('bricks-loader')?.remove();
            document.getElementById('bricks-container').innerHTML = `
                <div class="border border-outline-variant p-8 text-center space-y-4">
                    <p class="font-serif text-2xl text-on-surface">Pedido confirmado</p>
                    <p class="text-on-surface-variant font-light text-sm">
                        Pagarás cuando recojas tu pedido en la tienda.
                        Te enviamos un correo con los detalles.
                    </p>
                    <a href="{{ route('checkout.confirmacion', $pedido->numero) }}"
                       class="inline-block bg-primary text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300 mt-4">
                        Ver mi pedido
                    </a>
                </div>
            `;
        }

    })();
    </script>

</x-layouts::public>