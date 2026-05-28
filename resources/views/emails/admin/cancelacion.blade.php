<x-emails.layout titulo="Solicitud de cancelación">

    <h2>Panel de administración</h2>
    <h1>Solicitud de cancelación</h1>

    <p>El cliente <strong>{{ $pedido->nombre }}</strong> ha cancelado el pedido <strong>{{ $pedido->numero }}</strong>.</p>

    <hr class="divider">

    <div class="grid">
        <div class="grid-item">
            <span class="label">Número</span>
            <span class="value">{{ $pedido->numero }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Total</span>
            <span class="value">${{ number_format($pedido->total, 2) }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Cliente</span>
            <span class="value">{{ $pedido->nombre }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Correo</span>
            <span class="value">{{ $pedido->email }}</span>
        </div>
    </div>

    <hr class="divider">

    <span class="label">Motivo de cancelación</span>
    <p style="margin-top: 8px; font-style: italic; border-left: 2px solid #e8e4df; padding-left: 16px;">
        "{{ $pedido->notas_cancelacion }}"
    </p>

    <div style="text-align: center; margin-top: 24px;">
        <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn">
            Ver en el panel
        </a>
    </div>

</x-emails.layout>