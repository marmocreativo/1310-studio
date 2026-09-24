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
                <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $productos->count() }}</p>
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
                <flux:text class="text-sm text-zinc-400">{{ $productos->count() }} {{ Str::plural('producto', $productos->count()) }}</flux:text>
            </div>

            <div
                x-data="categoriaProductosDnD(@js(route('admin.categorias.productos.orden', $categoria)), @js(csrf_token()))"
                class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900"
            >
                <div x-show="mensaje" x-text="mensaje" x-transition class="px-4 py-2 text-xs text-green-600 bg-green-50 dark:bg-green-900/20"></div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-left">
                            <th class="px-4 py-3 w-8"></th>
                            <th class="px-4 py-3 w-14"></th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Nombre</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Precio venta</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Precio lista</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                            <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($productos as $producto)
                            <tr
                                draggable="true"
                                data-id="{{ $producto->id }}"
                                x-on:dragstart="dragStart($event.currentTarget)"
                                x-on:dragover="dragOver($event, $event.currentTarget)"
                                x-on:dragend="dragEnd($refs.tbody)"
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors cursor-move"
                            >
                                {{-- Handle --}}
                                <td class="px-4 py-3 text-zinc-300 dark:text-zinc-600">
                                    <flux:icon name="bars-3" class="size-4" />
                                </td>

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
                                            icon="pencil"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-zinc-400">
                                    No hay productos en esta categoría aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts::app>