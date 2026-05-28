<x-layouts::public title="Pedido {{ $pedido->numero }}">
<div class="py-16 px-8 max-w-[900px] mx-auto space-y-8">

    <div class="space-y-1">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
            <a href="{{ route('pedido-publico.buscar') }}" class="hover:text-on-surface transition-colors">← Consultar otro pedido</a>
        </p>
    </div>

    {{-- Header --}}
    <div class="space-y-2">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="font-serif text-3xl text-on-surface">Pedido</h1>
            <span class="font-mono text-xl text-on-surface-variant">{{ $pedido->numero }}</span>
            @include('pages.public.cuenta.partials.badge-estado', ['estado' => $pedido->estado])
        </div>
        <p class="text-sm text-on-surface-variant font-light">
            Realizado el {{ $pedido->created_at->translatedFormat('d \d\e F, Y') }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <div class="lg:col-span-2 space-y-8">

            {{-- Productos --}}
            <div class="space-y-4">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">Productos</p>
                @foreach($pedido->items as $item)
                    <div class="flex gap-4 py-4 border-b border-outline-variant/50">
                        <div class="w-16 h-16 shrink-0 bg-surface-container-low overflow-hidden">
                            @if($item->imagen_url)
                                <img src="{{ $item->imagen_url }}" alt="{{ $item->nombre_snapshot }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-on-surface">{{ $item->nombre_snapshot }}</p>
                            <p class="text-xs text-on-surface-variant mt-1">
                                {{ $item->cantidad }} × ${{ number_format($item->precio_snapshot, 2) }}
                            </p>
                        </div>
                        <p class="text-sm font-medium text-on-surface shrink-0">${{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach

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
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">Entrega</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Tipo</p>
                        <p class="text-sm text-on-surface">{{ $pedido->tipo_entrega === 'tienda' ? 'Recoger en tienda' : 'Envío a domicilio' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Fecha programada</p>
                        <p class="text-sm text-on-surface">{{ $pedido->fecha_entrega?->translatedFormat('d \d\e F, Y') ?? '—' }}</p>
                    </div>
                    @if($pedido->tipo_entrega === 'envio')
                        <div class="col-span-2 space-y-1">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-on-surface-variant">Dirección</p>
                            <p class="text-sm text-on-surface">
                                {{ $pedido->calle }} {{ $pedido->numero_ext }}
                                @if($pedido->numero_int), Int. {{ $pedido->numero_int }} @endif,
                                {{ $pedido->colonia }}, CP {{ $pedido->cp }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="border border-outline-variant p-5 space-y-3">
                <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Pago</p>
                @if($pedido->pago)
                    <p class="text-sm text-on-surface capitalize">{{ $pedido->pago->estado }}</p>
                    <p class="text-xs text-on-surface-variant capitalize">{{ $pedido->pago->metodo }}</p>
                @endif
            </div>

            <a href="https://wa.me/5212345678?text={{ urlencode('Hola, tengo una consulta sobre mi pedido ' . $pedido->numero) }}"
                target="_blank"
                class="flex items-center justify-center gap-2 border border-outline-variant text-on-surface-variant px-5 py-3 text-xs tracking-[0.15em] uppercase hover:border-on-surface hover:text-on-surface transition-colors w-full">
                <flux:icon name="chat-bubble-left-ellipsis" class="w-4 h-4" />
                Contactar por WhatsApp
            </a>

            <div class="border border-outline-variant p-5 space-y-3 text-center">
                <p class="text-xs text-on-surface-variant font-light">¿Quieres ver todos tus pedidos en un solo lugar?</p>
                <a href="{{ route('register') }}"
                    class="inline-block text-[11px] tracking-[0.15em] uppercase border-b border-outline-variant pb-0.5 text-on-surface-variant hover:text-on-surface transition-colors">
                    Crear una cuenta
                </a>
            </div>
        </div>
    </div>
</div>
</x-layouts::public>