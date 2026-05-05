<x-layouts::app :title="__('Productos')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <flux:heading size="xl">Productos</flux:heading>
            <flux:button href="{{ route('admin.productos.create') }}" variant="primary" wire:navigate>
                Nuevo producto
            </flux:button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            @forelse ($productos as $producto)
                <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        @php $portada = $producto->galeria->first(); @endphp
                        @if ($portada)
                            <img src="{{ Storage::url($portada->imagen) }}"
                                 alt="{{ $producto->nombre }}"
                                 class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                <flux:icon name="shopping-bag" class="w-5 h-5 text-zinc-400" />
                            </div>
                        @endif
                        <div>
                            <flux:heading>{{ $producto->nombre }}</flux:heading>
                            <div class="flex items-center gap-2 mt-1">
                                @if ($producto->destacado)
                                    <flux:badge size="sm" color="yellow">Destacado</flux:badge>
                                @endif
                                <flux:badge size="sm" color="{{ $producto->estado ? 'green' : 'zinc' }}">
                                    {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                                </flux:badge>
                                @if ($producto->tiene_descuento)
                                    <flux:badge size="sm" color="red">-{{ $producto->porcentaje_descuento }}%</flux:badge>
                                @endif
                                <flux:text class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    ${{ number_format($producto->precio_venta, 2) }}
                                </flux:text>
                            </div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($producto->categorias as $cat)
                                    <flux:badge size="sm" color="blue">{{ $cat->nombre }}</flux:badge>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('admin.productos.edit', $producto) }}" size="sm" variant="ghost" wire:navigate>
                            Editar
                        </flux:button>
                        <form method="POST" action="{{ route('admin.productos.destroy', $producto) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" size="sm" variant="danger"
                                onclick="return confirm('¿Eliminar este producto?')">
                                Eliminar
                            </flux:button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <flux:text>No hay productos creados aún.</flux:text>
                </div>
            @endforelse
        </div>

        <div>{{ $productos->links() }}</div>

    </div>
</x-layouts::app>