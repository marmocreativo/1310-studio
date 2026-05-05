<x-layouts::app :title="__('Talleres')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <flux:heading size="xl">Talleres</flux:heading>
            <flux:button href="{{ route('admin.talleres.create') }}" variant="primary" wire:navigate>
                Nuevo taller
            </flux:button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            @forelse ($talleres as $taller)
                <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        @if ($taller->imagen)
                            <img src="{{ Storage::url($taller->imagen) }}"
                                 alt="{{ $taller->nombre }}"
                                 class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                <flux:icon name="academic-cap" class="w-5 h-5 text-zinc-400" />
                            </div>
                        @endif
                        <div>
                            <flux:heading>{{ $taller->nombre }}</flux:heading>
                            <div class="flex items-center gap-2 mt-1">
                                @if ($taller->fecha)
                                    <flux:badge size="sm" color="{{ $taller->fecha->isFuture() ? 'blue' : 'zinc' }}">
                                        {{ $taller->fecha->format('d M Y · H:i') }}
                                    </flux:badge>
                                @endif
                                <flux:badge size="sm" color="{{ $taller->estado ? 'green' : 'zinc' }}">
                                    {{ $taller->estado ? 'Activo' : 'Inactivo' }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('admin.talleres.edit', $taller) }}" size="sm" variant="ghost" wire:navigate>
                            Editar
                        </flux:button>
                        <form method="POST" action="{{ route('admin.talleres.destroy', $taller) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" size="sm" variant="danger"
                                onclick="return confirm('¿Eliminar este taller?')">
                                Eliminar
                            </flux:button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <flux:text>No hay talleres creados aún.</flux:text>
                </div>
            @endforelse
        </div>

        <div>{{ $talleres->links() }}</div>

    </div>
</x-layouts::app>