<x-layouts::public :title="__('Categorías')">
    <div class="py-12">
        <flux:heading size="xl" level="1" class="mb-8">Categorías</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($categorias as $categoria)
                <a href="{{ route('categorias.show', $categoria->slug) }}"
                   class="group block border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden hover:shadow-md transition">
                    <img src="{{ $categoria->imagen_url }}"
                         alt="{{ $categoria->titulo }}"
                         class="w-full h-48 object-cover group-hover:opacity-90 transition">
                    <div class="p-4">
                        <flux:heading level="2">{{ $categoria->titulo }}</flux:heading>
                        @if ($categoria->resumen)
                            <flux:text class="mt-2 line-clamp-2">{{ $categoria->resumen }}</flux:text>
                        @endif
                        @if ($categoria->hijos->count())
                            <flux:text class="mt-2 text-sm text-zinc-400">
                                {{ $categoria->hijos->count() }} subcategorías
                            </flux:text>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3">
                    <flux:text>No hay categorías disponibles.</flux:text>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts::public>