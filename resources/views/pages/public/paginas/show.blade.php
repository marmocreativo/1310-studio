<x-layouts::public :title="$pagina->titulo">
    <div class="py-12 max-w-3xl mx-auto">
        <div class="mb-6">
            <flux:badge size="sm" class="mb-3">{{ $pagina->categoria }}</flux:badge>
            <flux:heading size="xl" level="1">{{ $pagina->titulo }}</flux:heading>
            @if ($pagina->resumen)
                <flux:text class="mt-3 text-lg text-zinc-500">{{ $pagina->resumen }}</flux:text>
            @endif
        </div>

        <img src="{{ $pagina->imagen_url }}"
             alt="{{ $pagina->titulo }}"
             class="w-full rounded-lg object-cover max-h-80 mb-8">

        <div class="prose dark:prose-invert max-w-none">
            {!! nl2br(e($pagina->contenido)) !!}
        </div>

        <div class="mt-8">
            <flux:button href="{{ route('paginas.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                Volver
            </flux:button>
        </div>
    </div>
</x-layouts::public>