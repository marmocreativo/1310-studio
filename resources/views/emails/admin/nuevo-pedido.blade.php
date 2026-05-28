<x-emails.layout titulo="Nuevo pedido recibido">

    <h2>Panel de administración</h2>
    <h1>Nuevo pedido recibido</h1>

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
            <span class="label">Método de pago</span>
            <span class="value">{{ $pedido->pago?->metodo ?? '—' }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Tipo de entrega</span>
            <span class="value">{{ $pedido->tipo_entrega === 'tienda' ? 'Recoger en tienda' : 'Envío a domicilio' }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Fecha de entrega</span>
            <span class="value">{{ $pedido->fecha_entrega?->translatedFormat('d \d\e F, Y') }}</span>
        </div>
    </div>

    <hr class="divider">

    <span class="label">Productos</span>
    @foreach($pedido->items as $item)
        <div class="item-row">
            <span class="item-name">{{ $item->nombre_snapshot }} × {{ $item->cantidad }}</span>
            <span class="item-price">${{ number_format($item->subtotal, 2) }}</span>
        </div>
    @endforeach

    <hr class="divider">

    <div style="text-align: center;">
        <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn">
            Ver en el panel
        </a>
    </div>

</x-emails.layout>