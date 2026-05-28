<x-emails.layout titulo="Confirmación de pedido">

    <h2>Pedido {{ $pedido->numero }}</h2>
    <h1>Tu pedido ha sido recibido.</h1>

    <p>
        Hola {{ $pedido->nombre }}, gracias por tu compra en 1310 Studio.
        Hemos recibido tu pedido y lo estamos procesando.
    </p>

    <hr class="divider">

    {{-- Productos --}}
    <span class="label">Productos</span>
    @foreach($pedido->items as $item)
        <div class="item-row">
            <div>
                <span class="item-name">{{ $item->nombre_snapshot }}</span>
                @if($item->opciones_snapshot)
                    <br>
                    @foreach($item->opciones_snapshot as $op)
                        <span style="font-size:11px;color:#8a8a8a;">{{ $op['tipo'] }}: {{ $op['opcion'] }}</span>
                    @endforeach
                @endif
            </div>
            <div style="text-align:right;">
                <span class="item-qty">× {{ $item->cantidad }}</span><br>
                <span class="item-price">${{ number_format($item->subtotal, 2) }}</span>
            </div>
        </div>
    @endforeach

    <div style="margin-top: 16px;">
        <div class="total-row">
            <span class="total-label">Subtotal</span>
            <span class="total-value">${{ number_format($pedido->subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Envío</span>
            <span class="total-value">
                {{ $pedido->costo_envio > 0 ? '$' . number_format($pedido->costo_envio, 2) : 'Gratis' }}
            </span>
        </div>
        <hr class="divider">
        <div class="total-row">
            <span class="grand-total-label">Total</span>
            <span class="grand-total-value">${{ number_format($pedido->total, 2) }}</span>
        </div>
    </div>

    <hr class="divider">

    {{-- Entrega --}}
    <span class="label">Entrega</span>
    <div class="grid" style="margin-top: 12px;">
        <div class="grid-item">
            <span class="label">Tipo</span>
            <span class="value">{{ $pedido->tipo_entrega === 'tienda' ? 'Recoger en tienda' : 'Envío a domicilio' }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Fecha</span>
            <span class="value">{{ $pedido->fecha_entrega?->translatedFormat('d \d\e F, Y') }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Horario</span>
            <span class="value">{{ $pedido->bloque_entrega_label }}</span>
        </div>
        @if($pedido->tipo_entrega === 'envio')
        <div class="grid-item">
            <span class="label">Zona</span>
            <span class="value">{{ $pedido->zona?->nombre }}</span>
        </div>
        @endif
    </div>

    @if($pedido->tipo_entrega === 'envio')
    <div style="margin-top: 8px;">
        <span class="label">Dirección</span>
        <span class="value">
            {{ $pedido->calle }} {{ $pedido->numero_ext }}
            @if($pedido->numero_int) Int. {{ $pedido->numero_int }} @endif,
            {{ $pedido->colonia }}, CP {{ $pedido->cp }}
        </span>
    </div>
    @endif

    @if($pedido->pago?->metodo === 'transferencia')
    <hr class="divider">
    <span class="label">Instrucciones de pago</span>
    <p style="margin-top: 8px;">
        Para confirmar tu pedido realiza una transferencia vía SPEI con la referencia
        <strong>{{ $pedido->numero }}</strong>. Te enviaremos los datos bancarios en breve.
    </p>
    @endif

    <hr class="divider">

    <div style="text-align: center;">
        <a href="{{ route('cuenta.pedidos.show', $pedido->numero) }}" class="btn">
            Ver mi pedido
        </a>
    </div>

    <p style="font-size: 12px; color: #b0a898; text-align: center;">
        Si tienes alguna pregunta, contáctanos por WhatsApp o escríbenos a
        <a href="mailto:hola@1310studio.mx" style="color: #927F64;">hola@1310studio.mx</a>
    </p>

</x-emails.layout>