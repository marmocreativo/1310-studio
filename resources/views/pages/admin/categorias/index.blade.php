<x-layouts::app :title="__('Categorías')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <flux:heading size="xl">Categorías</flux:heading>
            <flux:button href="{{ route('admin.categorias.create') }}" variant="primary" wire:navigate>
                Nueva categoría
            </flux:button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('admin.categorias.index') }}"
              class="flex flex-wrap gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="flex-1 min-w-48">
                <flux:input name="busqueda" placeholder="Buscar por título..." value="{{ request('busqueda') }}" />
            </div>
            <div class="w-48">
                <flux:select name="padre">
                    <flux:select.option value="">Todas las categorías</flux:select.option>
                    @foreach ($padres as $padre)
                        <flux:select.option value="{{ $padre->id }}" :selected="request('padre') == $padre->id">
                            {{ $padre->titulo }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <flux:button type="submit" variant="ghost">Filtrar</flux:button>
            <flux:button href="{{ route('admin.categorias.index') }}" variant="ghost" wire:navigate>Limpiar</flux:button>
        </form>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            @forelse ($categorias as $categoria)
                <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        <img src="{{ $categoria->imagen_url }}"
                             alt="{{ $categoria->titulo }}"
                             class="w-12 h-12 rounded object-cover">
                        <div>
                            <flux:heading>{{ $categoria->titulo }}</flux:heading>
                            <div class="flex items-center gap-2 mt-1">
                                @if ($categoria->padre)
                                    <flux:badge size="sm" color="blue">{{ $categoria->padre->titulo }}</flux:badge>
                                @else
                                    <flux:badge size="sm">Raíz</flux:badge>
                                @endif
                                <flux:badge size="sm" color="{{ $categoria->estado === 'publicado' ? 'green' : 'zinc' }}">
                                    {{ $categoria->estado }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('admin.categorias.show', $categoria) }}" size="sm" variant="ghost" wire:navigate>
                            Ver
                        </flux:button>
                        <flux:button href="{{ route('admin.categorias.edit', $categoria) }}" size="sm" variant="ghost" wire:navigate>
                            Editar
                        </flux:button>
                        <form method="POST" action="{{ route('admin.categorias.destroy', $categoria) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" size="sm" variant="danger"
                                onclick="return confirm('¿Eliminar esta categoría?')">
                                Eliminar
                            </flux:button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <flux:text>No hay categorías creadas aún.</flux:text>
                </div>
            @endforelse
        </div>

        <div>{{ $categorias->links() }}</div>

    </div>
</x-layouts::app>