<x-layouts::app :title="$categoria->titulo">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.categorias.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">{{ $categoria->titulo }}</flux:heading>
            <flux:badge color="{{ $categoria->estado === 'publicado' ? 'green' : 'zinc' }}">
                {{ $categoria->estado }}
            </flux:badge>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <flux:text class="text-zinc-500">Los productos de esta categoría aparecerán aquí próximamente.</flux:text>
        </div>

    </div>
</x-layouts::app>