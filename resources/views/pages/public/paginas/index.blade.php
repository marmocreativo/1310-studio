<x-layouts::public :title="__('Páginas')">
    <div class="py-12">
        <flux:heading size="xl" level="1" class="mb-8">Páginas</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($paginas as $pagina)
                <a href="{{ route('paginas.show', $pagina->slug) }}"
                   class="group block border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden hover:shadow-md transition">
                    <img src="{{ $pagina->imagen_url }}"
                         alt="{{ $pagina->titulo }}"
                         class="w-full h-48 object-cover group-hover:opacity-90 transition">
                    <div class="p-4">
                        <flux:badge size="sm" class="mb-2">{{ $pagina->categoria }}</flux:badge>
                        <flux:heading level="2">{{ $pagina->titulo }}</flux:heading>
                        @if ($pagina->resumen)
                            <flux:text class="mt-2 line-clamp-3">{{ $pagina->resumen }}</flux:text>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3">
                    <flux:text>No hay páginas publicadas aún.</flux:text>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $paginas->links() }}
        </div>
    </div>
</x-layouts::public>