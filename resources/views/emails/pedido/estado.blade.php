<x-emails.layout titulo="Actualización de tu pedido">

    <h2>Pedido {{ $pedido->numero }}</h2>
    <h1>{{ $titulo }}</h1>

    <p>{{ $mensaje }}</p>

    <hr class="divider">

    <div class="grid">
        <div class="grid-item">
            <span class="label">Estado</span>
            <span class="value">{{ $pedido->estado_label }}</span>
        </div>
        <div class="grid-item">
            <span class="label">Fecha de entrega</span>
            <span class="value">{{ $pedido->fecha_entrega?->translatedFormat('d \d\e F, Y') }}</span>
        </div>
    </div>

    <hr class="divider">

    <div style="text-align: center;">
        <a href="{{ route('cuenta.pedidos.show', $pedido->numero) }}" class="btn">
            Ver mi pedido
        </a>
    </div>

</x-emails.layout>