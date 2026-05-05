<x-layouts::app :title="__('Productos')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Productos"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Productos'],
        ]"
        :accion="['label' => 'Nuevo producto', 'route' => 'admin.productos.create']"
    >
        Gestiona el catálogo de productos de la tienda.
    </x-admin.page-header>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.productos.index') }}" class="flex flex-wrap gap-3">
        <flux:input
            name="busqueda"
            value="{{ request('busqueda') }}"
            placeholder="Buscar por nombre…"
            icon="magnifying-glass"
            class="w-64"
        />
        <flux:select name="categoria" class="w-52">
            <flux:select.option value="">Todas las categorías</flux:select.option>
            @foreach($categorias as $cat)
                <flux:select.option value="{{ $cat->id }}" :selected="request('categoria') == $cat->id">
                    {{ $cat->titulo }}
                </flux:select.option>
            @endforeach
        </flux:select>
        <flux:select name="estado" class="w-36">
            <flux:select.option value="">Cualquier estado</flux:select.option>
            <flux:select.option value="activo"   :selected="request('estado') === 'activo'">Activo</flux:select.option>
            <flux:select.option value="inactivo" :selected="request('estado') === 'inactivo'">Inactivo</flux:select.option>
        </flux:select>
        <flux:select name="destacado" class="w-40">
            <flux:select.option value="">Todos</flux:select.option>
            <flux:select.option value="1" :selected="request('destacado') === '1'">Solo destacados</flux:select.option>
        </flux:select>
        <flux:button type="submit" variant="filled" icon="funnel" size="sm">Filtrar</flux:button>
        @if(request()->hasAny(['busqueda', 'categoria', 'estado', 'destacado']))
            <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" icon="x-mark" size="sm">
                Limpiar
            </flux:button>
        @endif
    </form>

    {{-- Tabla con acciones en lote --}}
    <div
        x-data="{
            seleccionados: [],
            todos: false,
            accionLote: '',
            categoriaLote: '',
            toggleTodos(ids) {
                if (this.todos) {
                    this.seleccionados = ids;
                } else {
                    this.seleccionados = [];
                }
            },
        }"
    >
        {{-- Barra de acciones en lote --}}
        <div
            x-show="seleccionados.length > 0"
            x-transition
            class="mb-3 flex items-center gap-3 rounded-xl border border-primary/30 bg-primary/5 px-4 py-3"
        >
            <flux:text class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                <span x-text="seleccionados.length"></span> seleccionado(s)
            </flux:text>

            <flux:separator vertical class="h-4" />

            <form method="POST" action="{{ route('admin.productos.lote') }}" x-ref="formLote" class="flex items-center gap-3">
                @csrf
                <template x-for="id in seleccionados" :key="id">
                    <input type="hidden" name="ids[]" :value="id" />
                </template>

                <flux:select name="accion" x-model="accionLote" class="w-44" required>
                    <flux:select.option value="">Elegir acción…</flux:select.option>
                    <flux:select.option value="activar">Activar</flux:select.option>
                    <flux:select.option value="desactivar">Desactivar</flux:select.option>
                    <flux:select.option value="destacar">Marcar destacado</flux:select.option>
                    <flux:select.option value="no-destacar">Quitar destacado</flux:select.option>
                    <flux:select.option value="categoria">Agregar categoría</flux:select.option>
                </flux:select>

                {{-- Select de categoría, solo visible si acción = categoria --}}
                <div x-show="accionLote === 'categoria'" x-transition>
                    <flux:select name="id_categoria" x-model="categoriaLote" class="w-52">
                        <flux:select.option value="">Selecciona categoría…</flux:select.option>
                        @foreach($categorias as $cat)
                            <flux:select.option value="{{ $cat->id }}">{{ $cat->titulo }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:button
                    type="submit"
                    variant="primary"
                    size="sm"
                    icon="bolt"
                    @click.prevent="
                        if (!accionLote) return;
                        if (accionLote === 'categoria' && !categoriaLote) return;
                        $refs.formLote.submit();
                    "
                >
                    Aplicar
                </flux:button>

                <flux:button
                    type="button"
                    variant="ghost"
                    size="sm"
                    icon="x-mark"
                    @click="seleccionados = []; todos = false;"
                />
            </form>
        </div>

        {{-- Tabla --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-left">
                        <th class="px-4 py-3 w-10">
                            <input
                                type="checkbox"
                                x-model="todos"
                                @change="toggleTodos({{ $productos->pluck('id') }})"
                                class="rounded border-zinc-300 text-primary focus:ring-primary"
                            />
                        </th>
                        <th class="px-4 py-3 w-14"></th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Nombre</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Categorías</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Precio venta</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Descuento</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-center">Destacado</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($productos as $producto)
                        @php $portada = $producto->galeria->first(); @endphp
                        <tr
                            class="transition-colors"
                            :class="seleccionados.includes({{ $producto->id }})
                                ? 'bg-primary/5'
                                : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/50'"
                        >
                            {{-- Checkbox --}}
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $producto->id }}"
                                    x-model="seleccionados"
                                    class="rounded border-zinc-300 text-primary focus:ring-primary"
                                />
                            </td>

                            {{-- Imagen --}}
                            <td class="px-4 py-3">
                                @if($portada)
                                    <img
                                        src="{{ Storage::url($portada->imagen) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="h-10 w-10 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                    >
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center ring-1 ring-zinc-200 dark:ring-zinc-700">
                                        <flux:icon name="shopping-bag" class="size-4 text-zinc-400" />
                                    </div>
                                @endif
                            </td>

                            {{-- Nombre + slug --}}
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $producto->nombre }}</p>
                                <p class="text-xs text-zinc-400 font-mono mt-0.5">/{{ $producto->slug }}</p>
                            </td>

                            {{-- Categorías --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($producto->categorias as $cat)
                                        <flux:badge color="blue" size="sm">{{ $cat->titulo }}</flux:badge>
                                    @empty
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Precio venta --}}
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                ${{ number_format($producto->precio_venta, 2) }}
                            </td>

                            {{-- Descuento --}}
                            <td class="px-4 py-3">
                                @if($producto->tiene_descuento)
                                    <div class="flex flex-col gap-0.5">
                                        <flux:badge color="red" size="sm">-{{ $producto->porcentaje_descuento }}%</flux:badge>
                                        <span class="text-xs text-zinc-400 line-through">${{ number_format($producto->precio_lista, 2) }}</span>
                                    </div>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>

                            {{-- Toggle destacado --}}
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    x-data="{ destacado: {{ json_encode((bool) $producto->destacado) }} }"
                                    @click="
                                        fetch('{{ route('admin.productos.toggle-destacado', $producto) }}', {
                                            method: 'PATCH',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            }
                                        })
                                        .then(r => r.json())
                                        .then(d => { destacado = d.destacado })
                                    "
                                    :title="destacado ? 'Quitar destacado' : 'Marcar como destacado'"
                                    class="transition-colors"
                                >
                                    <flux:icon
                                        name="star"
                                        x-bind:class="destacado ? 'size-5 text-yellow-400 fill-yellow-400' : 'size-5 text-zinc-300 hover:text-yellow-300'"
                                    />
                                </button>
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
                                        href="{{ route('admin.productos.show', $producto) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        title="Ver"
                                        wire:navigate
                                    />
                                    <flux:button
                                        href="{{ route('admin.productos.edit', $producto) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        title="Editar"
                                        wire:navigate
                                    />
                                    <form
                                        method="POST"
                                        action="{{ route('admin.productos.destroy', $producto) }}"
                                        onsubmit="return confirm('¿Eliminar este producto?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <flux:button
                                            type="submit"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            title="Eliminar"
                                            class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950"
                                        />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <flux:icon name="shopping-bag" class="size-10 mx-auto mb-3 text-zinc-300" />
                                <flux:text class="text-zinc-400">No hay productos creados aún.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    @if($productos->hasPages())
        <div>{{ $productos->links() }}</div>
    @endif

</div>
</x-layouts::app>