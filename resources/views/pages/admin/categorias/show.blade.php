<x-layouts::app :title="$categoria->titulo">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        {{-- Page Header --}}
        <x-admin.page-header
            :titulo="$categoria->titulo"
            :breadcrumbs="[
                ['label' => 'Dashboard',   'route' => 'admin.dashboard'],
                ['label' => 'Categorías',  'route' => 'admin.categorias.index'],
                ['label' => $categoria->titulo],
            ]"
            :accion="['label' => 'Editar categoría', 'route' => 'admin.categorias.edit', 'param' => $categoria]"
        >
            @if($categoria->padre)
                Subcategoría de <strong>{{ $categoria->padre->titulo }}</strong>
            @else
                Categoría raíz
            @endif
        </x-admin.page-header>

        {{-- Métricas rápidas --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Productos</flux:text>
                <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $productos->total() }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Estado</flux:text>
                <div class="mt-1">
                    @if($categoria->estado === 'publicado')
                        <flux:badge color="green" icon="check-circle">Publicado</flux:badge>
                    @else
                        <flux:badge color="zinc" icon="pencil">Borrador</flux:badge>
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Tipo</flux:text>
                <div class="mt-1">
                    @if($categoria->padre)
                        <flux:badge color="blue" size="sm">Subcategoría</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Raíz</flux:badge>
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Slug</flux:text>
                <p class="mt-1 text-sm font-mono text-zinc-500 dark:text-zinc-400 truncate">/{{ $categoria->slug }}</p>
            </div>
        </div>

        {{-- Productos --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <flux:heading size="lg">Productos en esta categoría</flux:heading>
                <flux:text class="text-sm text-zinc-400">{{ $productos->total() }} {{ Str::plural('producto', $productos->total()) }}</flux:text>
            </div>

            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-left">
                            <th class="px-4 py-3 w-14"></th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Nombre</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Precio venta</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Precio lista</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($productos as $producto)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">

                                {{-- Imagen --}}
                                <td class="px-4 py-3">
                                    @if($producto->galeria->first())
                                        <img
                                            src="{{ Storage::url($producto->galeria->first()->imagen) }}"
                                            alt="{{ $producto->nombre }}"
                                            class="h-10 w-10 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                        >
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center ring-1 ring-zinc-200 dark:ring-zinc-700">
                                            <flux:icon name="photo" class="size-4 text-zinc-400" />
                                        </div>
                                    @endif
                                </td>

                                {{-- Nombre + slug --}}
                                <td class="px-4 py-3">
                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $producto->nombre }}</p>
                                    <p class="text-xs text-zinc-400 font-mono mt-0.5">/{{ $producto->slug }}</p>
                                </td>

                                {{-- Precio venta --}}
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                    ${{ number_format($producto->precio_venta, 2) }}
                                </td>

                                {{-- Precio lista --}}
                                <td class="px-4 py-3 text-zinc-500">
                                    @if($producto->tiene_descuento)
                                        <span class="line-through">${{ number_format($producto->precio_lista, 2) }}</span>
                                        <flux:badge color="amber" size="sm" class="ml-1">-{{ $producto->porcentaje_descuento }}%</flux:badge>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 py-3">
                                    @if($producto->estado)
                                        <flux:badge color="green" size="sm" icon="check-circle">Activo</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm" icon="x-circle">Inactivo</flux:badge>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button
                                            href="{{ route('admin.productos.edit', $producto) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil-square"
                                            title="Editar"
                                            wire:navigate
                                        />
                                        <flux:button
                                            href="{{ route('productos.show', $producto) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="arrow-top-right-on-square"
                                            title="Ver en sitio"
                                            target="_blank"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <flux:icon name="shopping-bag" class="size-10 mx-auto mb-3 text-zinc-300" />
                                    <flux:text class="text-zinc-400">Esta categoría no tiene productos asignados.</flux:text>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($productos->hasPages())
                <div class="mt-4">{{ $productos->links() }}</div>
            @endif
        </div>

    </div>
</x-layouts::app>