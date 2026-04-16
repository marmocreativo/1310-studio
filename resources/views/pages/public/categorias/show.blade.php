<x-layouts::public :title="$categoria->titulo">
    <div class="py-12">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 mb-6 text-sm text-zinc-500">
            <a href="{{ route('categorias.index') }}" class="hover:underline">Categorías</a>
            @if ($categoria->padre)
                <span>/</span>
                <span>{{ $categoria->padre->titulo }}</span>
            @endif
            <span>/</span>
            <span class="text-zinc-800 dark:text-zinc-200">{{ $categoria->titulo }}</span>
        </div>

        <div class="mb-8">
            <img src="{{ $categoria->imagen_url }}" alt="{{ $categoria->titulo }}"
                 class="w-full rounded-lg object-cover max-h-64 mb-6">
            <flux:heading size="xl" level="1">{{ $categoria->titulo }}</flux:heading>
            @if ($categoria->resumen)
                <flux:text class="mt-3 text-zinc-500">{{ $categoria->resumen }}</flux:text>
            @endif
        </div>

        {{-- Subcategorías --}}
        @if ($categoria->hijos->count())
            <flux:heading level="2" class="mb-4">Subcategorías</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                @foreach ($categoria->hijos as $hijo)
                    <a href="{{ route('categorias.show', $hijo->slug) }}"
                       class="group block border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden hover:shadow-md transition">
                        <img src="{{ $hijo->imagen_url }}" alt="{{ $hijo->titulo }}"
                             class="w-full h-32 object-cover group-hover:opacity-90 transition">
                        <div class="p-3">
                            <flux:heading level="3">{{ $hijo->titulo }}</flux:heading>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Productos (próximamente) --}}
        <flux:text class="text-zinc-400">Los productos aparecerán aquí próximamente.</flux:text>

        <div class="mt-8">
            <flux:button href="{{ route('categorias.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                Volver
            </flux:button>
        </div>

    </div>
</x-layouts::public>