<x-layouts::app :title="__('Páginas')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Páginas"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Páginas'],
        ]"
        :accion="['label' => 'Nueva página', 'route' => 'admin.paginas.create']"
    >
        Gestiona las páginas de contenido del sitio.
    </x-admin.page-header>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.paginas.index') }}" class="flex flex-wrap gap-3">
        <flux:input
            name="busqueda"
            value="{{ request('busqueda') }}"
            placeholder="Buscar por título…"
            icon="magnifying-glass"
            class="w-64"
        />
        <flux:select name="categoria" class="w-40">
            <flux:select.option value="">Todas</flux:select.option>
            <flux:select.option value="general" :selected="request('categoria') === 'general'">General</flux:select.option>
            <flux:select.option value="legal"   :selected="request('categoria') === 'legal'">Legal</flux:select.option>
        </flux:select>
        <flux:select name="estado" class="w-40">
            <flux:select.option value="">Cualquier estado</flux:select.option>
            <flux:select.option value="publicado" :selected="request('estado') === 'publicado'">Publicado</flux:select.option>
            <flux:select.option value="borrador"  :selected="request('estado') === 'borrador'">Borrador</flux:select.option>
        </flux:select>
        <flux:button type="submit" variant="filled" icon="funnel" size="sm">Filtrar</flux:button>
        @if(request()->hasAny(['busqueda', 'categoria', 'estado']))
            <flux:button href="{{ route('admin.paginas.index') }}" variant="ghost" icon="x-mark" size="sm">
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
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Categoría</th>
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($paginas as $pagina)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">

                        {{-- Imagen --}}
                        <td class="px-4 py-3">
                            <img
                                src="{{ $pagina->imagen_url }}"
                                alt="{{ $pagina->titulo }}"
                                class="h-10 w-10 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                            >
                        </td>

                        {{-- Título + slug --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-zinc-900 dark:text-white">{{ $pagina->titulo }}</p>
                            <p class="text-xs text-zinc-400 font-mono mt-0.5">/{{ $pagina->slug }}</p>
                        </td>

                        {{-- Categoría --}}
                        <td class="px-4 py-3">
                            <flux:badge color="{{ $pagina->categoria === 'legal' ? 'yellow' : 'blue' }}" size="sm">
                                {{ ucfirst($pagina->categoria) }}
                            </flux:badge>
                        </td>

                        {{-- Estado --}}
                        <td class="px-4 py-3">
                            @if($pagina->estado === 'publicado')
                                <flux:badge color="green" size="sm" icon="check-circle">Publicado</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" icon="pencil">Borrador</flux:badge>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button
                                    href="{{ route('paginas.show', $pagina->slug) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="arrow-top-right-on-square"
                                    title="Ver en el sitio"
                                    target="_blank"
                                />
                                <flux:button
                                    href="{{ route('admin.paginas.edit', $pagina) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil-square"
                                    title="Editar"
                                    wire:navigate
                                />
                                <form
                                    method="POST"
                                    action="{{ route('admin.paginas.destroy', $pagina) }}"
                                    onsubmit="return confirm('¿Eliminar esta página?')"
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
                        <td colspan="5" class="px-4 py-16 text-center">
                            <flux:icon name="document-text" class="size-10 mx-auto mb-3 text-zinc-300" />
                            <flux:text class="text-zinc-400">No hay páginas creadas aún.</flux:text>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($paginas->hasPages())
        <div>{{ $paginas->links() }}</div>
    @endif

</div>
</x-layouts::app>