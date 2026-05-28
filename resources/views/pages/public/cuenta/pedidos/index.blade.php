<x-layouts::cuenta title="Mis pedidos">
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="font-serif text-3xl text-on-surface">Mis pedidos</h1>
    </div>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Filtro estado --}}
    <form method="GET" class="flex gap-2 flex-wrap">
        @foreach(['', 'pendiente', 'pagado', 'preparando', 'enviado', 'entregado', 'cancelado'] as $est)
            <a href="{{ route('cuenta.pedidos', $est ? ['estado' => $est] : []) }}"
                class="px-4 py-1.5 text-[11px] tracking-[0.1em] uppercase transition-colors
                    {{ request('estado', '') === $est
                        ? 'bg-on-surface text-surface'
                        : 'border border-outline-variant text-on-surface-variant hover:border-on-surface hover:text-on-surface' }}">
                {{ $est ?: 'Todos' }}
            </a>
        @endforeach
    </form>

    {{-- Lista --}}
    @forelse($pedidos as $pedido)
        <div class="border border-outline-variant p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <p class="font-mono text-sm font-medium text-on-surface">{{ $pedido->numero }}</p>
                        @include('pages.public.cuenta.partials.badge-estado', ['estado' => $pedido->estado])
                    </div>
                    <p class="text-xs text-on-surface-variant font-light">
                        {{ $pedido->created_at->translatedFormat('d \d\e F, Y') }}
                        &middot;
                        {{ $pedido->tipo_entrega === 'tienda' ? 'Recoger en tienda' : 'Envío a domicilio' }}
                        &middot; ${{ number_format($pedido->total, 2) }}
                    </p>
                </div>
                <a href="{{ route('cuenta.pedidos.show', $pedido->numero) }}"
                    class="text-[11px] tracking-[0.15em] uppercase border-b border-outline-variant pb-0.5 text-on-surface-variant hover:text-on-surface transition-colors shrink-0">
                    Ver detalle
                </a>
            </div>

            {{-- Items resumidos --}}
            <div class="flex gap-2 flex-wrap">
                @foreach($pedido->items->take(3) as $item)
                    <div class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5">
                        <p class="text-xs text-on-surface">{{ $item->nombre_snapshot }}</p>
                        <span class="text-[10px] text-on-surface-variant">×{{ $item->cantidad }}</span>
                    </div>
                @endforeach
                @if($pedido->items->count() > 3)
                    <div class="flex items-center px-3 py-1.5">
                        <p class="text-xs text-on-surface-variant">+{{ $pedido->items->count() - 3 }} más</p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="border border-outline-variant p-16 text-center space-y-4">
            <flux:icon name="shopping-bag" class="w-10 h-10 mx-auto text-outline-variant" />
            <p class="text-on-surface-variant font-light">Aún no tienes pedidos.</p>
            <a href="{{ route('categorias.index') }}"
                class="inline-block border border-on-surface text-on-surface px-8 py-3 text-xs tracking-[0.3em] uppercase hover:bg-on-surface hover:text-surface transition-colors">
                Explorar colección
            </a>
        </div>
    @endforelse

    @if($pedidos->hasPages())
        <div>{{ $pedidos->links() }}</div>
    @endif

</div>
</x-layouts::cuenta>