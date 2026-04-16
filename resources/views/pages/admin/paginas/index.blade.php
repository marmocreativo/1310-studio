<x-layouts::app :title="__('Páginas')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <flux:heading size="xl">Páginas</flux:heading>
            <flux:button href="{{ route('admin.paginas.create') }}" variant="primary" wire:navigate>
                Nueva página
            </flux:button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            @forelse ($paginas as $pagina)
                <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800">
                    <div class="flex items-center gap-4">
                        <img src="{{ $pagina->imagen_url }}"
                             alt="{{ $pagina->titulo }}"
                             class="w-12 h-12 rounded object-cover">
                        <div>
                            <flux:heading>{{ $pagina->titulo }}</flux:heading>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:badge size="sm">{{ $pagina->categoria }}</flux:badge>
                                <flux:badge size="sm" color="{{ $pagina->estado === 'publicado' ? 'green' : 'zinc' }}">
                                    {{ $pagina->estado }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button href="{{ route('admin.paginas.edit', $pagina) }}" size="sm" variant="ghost" wire:navigate>
                            Editar
                        </flux:button>
                        <form method="POST" action="{{ route('admin.paginas.destroy', $pagina) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" size="sm" variant="danger"
                                onclick="return confirm('¿Eliminar esta página?')">
                                Eliminar
                            </flux:button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <flux:text>No hay páginas creadas aún.</flux:text>
                </div>
            @endforelse
        </div>

        <div>
            {{ $paginas->links() }}
        </div>

    </div>
</x-layouts::app>