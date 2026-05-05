<x-layouts::app :title="__('Talleres')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Talleres"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Talleres'],
        ]"
        :accion="['label' => 'Nuevo taller', 'route' => 'admin.talleres.create']"
    >
        Gestiona los talleres y eventos del estudio.
    </x-admin.page-header>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.talleres.index') }}" class="flex flex-wrap gap-3">
        <flux:input
            name="busqueda"
            value="{{ request('busqueda') }}"
            placeholder="Buscar por nombre…"
            icon="magnifying-glass"
            class="w-64"
        />
        <flux:select name="periodo" class="w-40">
            <flux:select.option value="">Cualquier fecha</flux:select.option>
            <flux:select.option value="proximos"  :selected="request('periodo') === 'proximos'">Próximos</flux:select.option>
            <flux:select.option value="pasados"   :selected="request('periodo') === 'pasados'">Pasados</flux:select.option>
            <flux:select.option value="sin_fecha" :selected="request('periodo') === 'sin_fecha'">Sin fecha</flux:select.option>
        </flux:select>
        <flux:select name="estado" class="w-36">
            <flux:select.option value="">Cualquier estado</flux:select.option>
            <flux:select.option value="1" :selected="request('estado') === '1'">Activo</flux:select.option>
            <flux:select.option value="0" :selected="request('estado') === '0'">Inactivo</flux:select.option>
        </flux:select>
        <flux:button type="submit" variant="filled" icon="funnel" size="sm">Filtrar</flux:button>
        @if(request()->hasAny(['busqueda', 'periodo', 'estado']))
            <flux:button href="{{ route('admin.talleres.index') }}" variant="ghost" icon="x-mark" size="sm">
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
                <span x-text="seleccionados.length"></span> seleccionado(s)
            </flux:text>

            <flux:separator vertical class="h-4" />

            <form method="POST" action="{{ route('admin.talleres.lote') }}" x-ref="formLote" class="flex items-center gap-3">
                @csrf
                <template x-for="id in seleccionados" :key="id">
                    <input type="hidden" name="ids[]" :value="id" />
                </template>

                <flux:select name="accion" x-model="accionLote" class="w-44" required>
                    <flux:select.option value="">Elegir acción…</flux:select.option>
                    <flux:select.option value="activar">Activar</flux:select.option>
                    <flux:select.option value="desactivar">Desactivar</flux:select.option>
                    <flux:select.option value="eliminar">Eliminar</flux:select.option>
                </flux:select>

                <flux:button
                    type="button"
                    variant="primary"
                    size="sm"
                    icon="bolt"
                    @click="
                        if (!accionLote) return;
                        if (accionLote === 'eliminar' && !confirm('¿Eliminar los talleres seleccionados?')) return;
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
                                @change="toggleTodos({{ $talleres->pluck('id') }})"
                                class="rounded border-zinc-300 text-primary focus:ring-primary"
                            />
                        </th>
                        <th class="px-4 py-3 w-14"></th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Nombre</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Fecha</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($talleres as $taller)
                        <tr
                            class="transition-colors"
                            :class="seleccionados.includes({{ $taller->id }})
                                ? 'bg-primary/5'
                                : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/50'"
                        >
                            {{-- Checkbox --}}
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $taller->id }}"
                                    x-model="seleccionados"
                                    class="rounded border-zinc-300 text-primary focus:ring-primary"
                                />
                            </td>

                            {{-- Imagen --}}
                            <td class="px-4 py-3">
                                @if($taller->imagen)
                                    <img
                                        src="{{ Storage::url($taller->imagen) }}"
                                        alt="{{ $taller->nombre }}"
                                        class="h-10 w-10 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                    >
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center ring-1 ring-zinc-200 dark:ring-zinc-700">
                                        <flux:icon name="academic-cap" class="size-4 text-zinc-400" />
                                    </div>
                                @endif
                            </td>

                            {{-- Nombre + slug --}}
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $taller->nombre }}</p>
                                <p class="text-xs text-zinc-400 font-mono mt-0.5">/{{ $taller->slug }}</p>
                            </td>

                            {{-- Fecha --}}
                            <td class="px-4 py-3">
                                @if($taller->fecha)
                                    @if($taller->fecha->isFuture())
                                        <flux:badge color="blue" size="sm" icon="calendar">
                                            {{ $taller->fecha->format('d M Y · H:i') }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm" icon="clock">
                                            {{ $taller->fecha->format('d M Y · H:i') }}
                                        </flux:badge>
                                    @endif
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-3">
                                @if($taller->estado)
                                    <flux:badge color="green" size="sm" icon="check-circle">Activo</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" icon="x-circle">Inactivo</flux:badge>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button
                                        href="{{ route('admin.talleres.show', $taller) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        title="Ver"
                                        wire:navigate
                                    />
                                    <flux:button
                                        href="{{ route('admin.talleres.edit', $taller) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        title="Editar"
                                        wire:navigate
                                    />
                                    <form
                                        method="POST"
                                        action="{{ route('admin.talleres.destroy', $taller) }}"
                                        onsubmit="return confirm('¿Eliminar este taller?')"
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
                            <td colspan="6" class="px-4 py-16 text-center">
                                <flux:icon name="academic-cap" class="size-10 mx-auto mb-3 text-zinc-300" />
                                <flux:text class="text-zinc-400">No hay talleres creados aún.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    @if($talleres->hasPages())
        <div>{{ $talleres->links() }}</div>
    @endif

</div>
</x-layouts::app>