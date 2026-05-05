<x-layouts::app :title="__('Directorio Floral')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <flux:heading size="xl">Directorio Floral</flux:heading>
            <flux:button href="{{ route('admin.directorio-floral.create') }}" variant="primary" wire:navigate>
                Nueva flor
            </flux:button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('admin.directorio-floral.index') }}"
              class="flex flex-wrap gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="flex-1 min-w-48">
                <flux:input name="busqueda" placeholder="Buscar por nombre..." value="{{ request('busqueda') }}" />
            </div>
            <div class="w-48">
                <flux:select name="estado">
                    <flux:select.option value="">Todos los estados</flux:select.option>
                    <flux:select.option value="1" :selected="request('estado') === '1'">Activos</flux:select.option>
                    <flux:select.option value="0" :selected="request('estado') === '0'">Inactivos</flux:select.option>
                </flux:select>
            </div>
            <flux:button type="submit" variant="ghost">Filtrar</flux:button>
            <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" wire:navigate>Limpiar</flux:button>
        </form>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            @forelse ($flores as $flor)
                <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        @if ($flor->imagen)
                            <img src="{{ Storage::url($flor->imagen) }}"
                                 alt="{{ $flor->nombre }}"
                                 class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                <flux:icon name="photo" class="w-5 h-5 text-zinc-400" />
                            </div>
                        @endif
                        <div>
                            <flux:heading>{{ $flor->nombre }}</flux:heading>
                            <div class="flex items-center gap-2 mt-1">
                                @if ($flor->categoria)
                                    <flux:badge size="sm" color="blue">{{ $flor->categoria }}</flux:badge>
                                @endif
                                <flux:badge size="sm" color="{{ $flor->estado ? 'green' : 'zinc' }}">
                                    {{ $flor->estado ? 'Activa' : 'Inactiva' }}
                                </flux:badge>
                                <flux:text class="text-xs text-zinc-400">Orden: {{ $flor->orden }}</flux:text>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('admin.directorio-floral.edit', $flor) }}" size="sm" variant="ghost" wire:navigate>
                            Editar
                        </flux:button>
                        <form method="POST" action="{{ route('admin.directorio-floral.destroy', $flor) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" size="sm" variant="danger"
                                onclick="return confirm('¿Eliminar esta flor?')">
                                Eliminar
                            </flux:button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <flux:text>No hay flores en el directorio aún.</flux:text>
                </div>
            @endforelse
        </div>

        <div>{{ $flores->links() }}</div>

    </div>
</x-layouts::app>