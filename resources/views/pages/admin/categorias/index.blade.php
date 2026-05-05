<x-layouts::app :title="__('Categorías')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        {{-- Page Header --}}
        <x-admin.page-header
            titulo="Categorías"
            :breadcrumbs="[
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Categorías'],
            ]"
            :accion="['label' => 'Nueva categoría', 'route' => 'admin.categorias.create']"
        >
            Gestiona las categorías del catálogo de productos.
        </x-admin.page-header>

        {{-- Flash --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('admin.categorias.index') }}"
              class="flex flex-wrap gap-3">
            <flux:input
                name="busqueda"
                value="{{ request('busqueda') }}"
                placeholder="Buscar por título…"
                icon="magnifying-glass"
                class="w-64"
            />
            <flux:select name="padre" class="w-52">
                <flux:select.option value="">Todas las categorías</flux:select.option>
                @foreach($padres as $padre)
                    <flux:select.option
                        value="{{ $padre->id }}"
                        :selected="request('padre') == $padre->id"
                    >
                        {{ $padre->titulo }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            <flux:button type="submit" variant="filled" icon="funnel" size="sm">Filtrar</flux:button>
            @if(request()->hasAny(['busqueda', 'padre']))
                <flux:button href="{{ route('admin.categorias.index') }}" variant="ghost" icon="x-mark" size="sm">
                    Limpiar
                </flux:button>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-left">
                        <th class="px-4 py-3 w-14"></th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Título</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Categoría padre</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-center">Productos</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                        <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($categorias as $categoria)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">

                            {{-- Imagen --}}
                            <td class="px-4 py-3">
                                <img
                                    src="{{ $categoria->imagen_url }}"
                                    alt="{{ $categoria->titulo }}"
                                    class="h-10 w-10 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                >
                            </td>

                            {{-- Título + slug --}}
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $categoria->titulo }}</p>
                                <p class="text-xs text-zinc-400 font-mono mt-0.5">/{{ $categoria->slug }}</p>
                            </td>

                            {{-- Padre --}}
                            <td class="px-4 py-3">
                                @if($categoria->padre)
                                    <flux:badge color="blue" size="sm">{{ $categoria->padre->titulo }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Raíz</flux:badge>
                                @endif
                            </td>

                            {{-- Cantidad de productos --}}
                            <td class="px-4 py-3 text-center">
                                <flux:badge color="zinc" size="sm">
                                    {{ $categoria->productos_count }}
                                </flux:badge>
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-3">
                                @if($categoria->estado === 'publicado')
                                    <flux:badge color="green" size="sm" icon="check-circle">Publicado</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" icon="pencil">Borrador</flux:badge>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button
                                        href="{{ route('admin.categorias.show', $categoria) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        title="Ver"
                                        wire:navigate
                                    />
                                    <flux:button
                                        href="{{ route('admin.categorias.edit', $categoria) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        title="Editar"
                                        wire:navigate
                                    />
                                    <form
                                        method="POST"
                                        action="{{ route('admin.categorias.destroy', $categoria) }}"
                                        onsubmit="return confirm('¿Eliminar esta categoría?')"
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
                                <flux:icon name="tag" class="size-10 mx-auto mb-3 text-zinc-300" />
                                <flux:text class="text-zinc-400">No hay categorías creadas aún.</flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($categorias->hasPages())
            <div>{{ $categorias->links() }}</div>
        @endif

    </div>
</x-layouts::app>