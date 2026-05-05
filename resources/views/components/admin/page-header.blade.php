@props([
    'titulo',
    'breadcrumbs' => [], // [['label' => 'Admin', 'route' => 'admin.dashboard'], ['label' => 'Categorías']]
    'accion'      => null, // ['label' => 'Nueva categoría', 'route' => 'admin.categorias.create']
])

<div class="mb-8">
    {{-- Breadcrumbs --}}
    @if(count($breadcrumbs))
        <flux:breadcrumbs class="mb-3">
            @foreach($breadcrumbs as $crumb)
               @if(isset($crumb['route']))
                    <flux:breadcrumbs.item :href="isset($crumb['param']) ? route($crumb['route'], $crumb['param']) : route($crumb['route'])">
                        {{ $crumb['label'] }}
                    </flux:breadcrumbs.item>
                @else
                    <flux:breadcrumbs.item>{{ $crumb['label'] }}</flux:breadcrumbs.item>
                @endif
            @endforeach
        </flux:breadcrumbs>
    @endif

    {{-- Título + botón de acción --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ $titulo }}</flux:heading>
            @if($slot->isNotEmpty())
                <flux:text class="mt-1 text-zinc-500">{{ $slot }}</flux:text>
            @endif
        </div>

        @if($accion)
            <flux:button
                :href="isset($accion['param']) ? route($accion['route'], $accion['param']) : route($accion['route'])"
                variant="primary"
                icon="plus"
                size="sm"
            >
                {{ $accion['label'] }}
            </flux:button>
        @endif
    </div>

    <flux:separator class="mt-5" />
</div>