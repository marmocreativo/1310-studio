<x-layouts::app :title="__('Directorio Floral')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Directorio Floral"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Directorio Floral'],
        ]"
        :accion="['label' => 'Nueva flor', 'route' => 'admin.directorio-floral.create']"
    >
        Catálogo de flores disponibles en el estudio.
    </x-admin.page-header>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.directorio-floral.index') }}" class="flex flex-wrap gap-3">
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
                <flux:select.option value="{{ $cat }}" :selected="request('categoria') === $cat">
                    {{ $cat }}
                </flux:select.option>
            @endforeach
        </flux:select>
        <flux:select name="estado" class="w-36">
            <flux:select.option value="">Cualquier estado</flux:select.option>
            <flux:select.option value="1" :selected="request('estado') === '1'">Activas</flux:select.option>
            <flux:select.option value="0" :selected="request('estado') === '0'">Inactivas</flux:select.option>
        </flux:select>
        <flux:button type="submit" variant="filled" icon="funnel" size="sm">Filtrar</flux:button>
        @if(request()->hasAny(['busqueda', 'categoria', 'estado']))
            <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" icon="x-mark" size="sm">
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
                this.seleccionados = this.todos ? ids : [];
            },
        }"
    >
        {{-- Barra acciones en lote --}}
        <div
            x-show="seleccionados.length > 0"
            x-transition
            class="mb-3 flex items-center gap-3 rounded-xl border border-primary/30 bg-primary/5 px-4 py-3"
        >
            <flux:text class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                <span x-text="seleccionados.length"></span> seleccionada(s)
            </flux:text>

            <flux:separator vertical class="h-4" />

            <form method="POST" action="{{ route('admin.directorio-floral.lote') }}" x-ref="formLote" class="flex items-center gap-3">
                @csrf
                <template x-for="id in seleccionados" :key="id">
                    <input type="hidden" name="ids[]" :value="id" />
                </template>

                <flux:select name="accion" x-model="accionLote" class="w-48" required>
                    <flux:select.option value="">Elegir acción…</flux:select.option>
                    <flux:select.option value="activar">Activar</flux:select.option>
                    <flux:select.option value="desactivar">Desactivar</flux:select.option>
                    <flux:select.option value="categoria">Cambiar categoría</flux:select.option>
                    <flux:select.option value="eliminar">Eliminar</flux:select.option>
                </flux:select>

                {{-- Input de categoría libre --}}
                <div x-show="accionLote === 'categoria'" x-transition>
                    <flux:input
                        name="categoria"
                        x-model="categoriaLote"
                        placeholder="Nombre de categoría…"
                        class="w-52"
                    />
                </div>

                <flux:button
                    type="button"
                    variant="primary"
                    size="sm"
                    icon="bolt"
                    @click="
                        if (!accionLote) return;
                        if (accionLote === 'categoria' && !categoriaLote) return;
                        if (accionLote === 'eliminar' && !confirm('¿Eliminar las flores seleccionadas? Esta acción no se puede deshacer.')) return;
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
                                @change="toggleTodos({{ $flores->pluck('id') }})"
                                class="rounded border-zinc-300 text-primary focus:ring-primary"
                            />
                        </th>
                        <th class="px-4 py-3 w-14"></th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Nombre</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Categoría</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-center">Orden</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($flores as $flor)
                        <tr
                            class="transition-colors"
                            :class="seleccionados.includes({{ $flor->id }})
                                ? 'bg-primary/5'
                                : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/50'"
                        >
                            {{-- Checkbox --}}
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $flor->id }}"
                                    x-model="seleccionados"
                                    class="rounded border-zinc-300 text-primary focus:ring-primary"
                                />
                            </td>

                            {{-- Imagen --}}
                            <td class="px-4 py-3">
                                @if($flor->imagen)
                                    <img
                                        src="{{ Storage::url($flor->imagen) }}"
                                        alt="{{ $flor->nombre }}"
                                        class="h-10 w-10 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                    >
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center ring-1 ring-zinc-200 dark:ring-zinc-700">
                                        <flux:icon name="sparkles" class="size-4 text-zinc-400" />
                                    </div>
                                @endif
                            </td>

                            {{-- Nombre + slug --}}
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $flor->nombre }}</p>
                                <p class="text-xs text-zinc-400 font-mono mt-0.5">/{{ $flor->slug }}</p>
                            </td>

                            {{-- Categoría --}}
                            <td class="px-4 py-3">
                                @if($flor->categoria)
                                    <flux:badge color="blue" size="sm">{{ $flor->categoria }}</flux:badge>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>

                            {{-- Orden --}}
                            <td class="px-4 py-3 text-center">
                                <flux:badge color="zinc" size="sm">{{ $flor->orden }}</flux:badge>
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-3">
                                @if($flor->estado)
                                    <flux:badge color="green" size="sm" icon="check-circle">Activa</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" icon="x-circle">Inactiva</flux:badge>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button
                                        href="{{ route('admin.directorio-floral.show', $flor) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        title="Ver"
                                        wire:navigate
                                    />
                                    <flux:button
                                        href="{{ route('admin.directorio-floral.edit', $flor) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        title="Editar"
                                        wire:navigate
                                    />
                                    <form
                                        method="POST"
                                        action="{{ route('admin.directorio-floral.destroy', $flor) }}"
                                        onsubmit="return confirm('¿Eliminar esta flor?')"
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
                            <td colspan="7" class="px-4 py-16 text-center">
                                <flux:icon name="sparkles" class="size-10 mx-auto mb-3 text-zinc-300" />
                                <flux:text class="text-zinc-400">No hay flores en el directorio aún.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    @if($flores->hasPages())
        <div>{{ $flores->links() }}</div>
    @endif

</div>
</x-layouts::app>