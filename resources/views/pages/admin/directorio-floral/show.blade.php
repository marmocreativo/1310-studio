<x-layouts::app :title="$directorioFloral->nombre">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="$directorioFloral->nombre"
        :breadcrumbs="[
            ['label' => 'Dashboard',         'route' => 'admin.dashboard'],
            ['label' => 'Directorio Floral', 'route' => 'admin.directorio-floral.index'],
            ['label' => $directorioFloral->nombre],
        ]"
        :accion="['label' => 'Editar', 'route' => 'admin.directorio-floral.edit', 'param' => $directorioFloral]"
    >
        {{ $directorioFloral->slug }}
    </x-admin.page-header>

    {{-- Métricas rápidas --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Estado</flux:text>
            <div class="mt-2">
                @if($directorioFloral->estado)
                    <flux:badge color="green" icon="check-circle">Activa</flux:badge>
                @else
                    <flux:badge color="zinc" icon="x-circle">Inactiva</flux:badge>
                @endif
            </div>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Categoría</flux:text>
            <div class="mt-2">
                @if($directorioFloral->categoria)
                    <flux:badge color="blue">{{ $directorioFloral->categoria }}</flux:badge>
                @else
                    <span class="text-sm text-zinc-400">Sin categoría</span>
                @endif
            </div>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Imágenes</flux:text>
            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $directorioFloral->galeria->count() }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Productos</flux:text>
            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $directorioFloral->productos->count() }}</p>
        </div>
    </div>

    <div class="flex gap-6 items-start">

        {{-- Columna principal --}}
        <div class="flex-1 space-y-6">

            {{-- Galería --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                <flux:heading size="sm" class="mb-4">Galería</flux:heading>

                @if($directorioFloral->galeria->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                        <flux:icon name="photo" class="size-10 text-zinc-300 mb-2" />
                        <flux:text class="text-zinc-400">Esta flor no tiene imágenes en galería.</flux:text>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        {{-- Imagen principal --}}
                        @if($directorioFloral->imagen)
                            <div class="relative aspect-square rounded-lg overflow-hidden ring-2 ring-primary/40">
                                <img
                                    src="{{ Storage::url($directorioFloral->imagen) }}"
                                    alt="{{ $directorioFloral->nombre }}"
                                    class="h-full w-full object-cover"
                                >
                                <div class="absolute top-2 left-2">
                                    <flux:badge color="zinc" size="sm">Principal</flux:badge>
                                </div>
                            </div>
                        @endif
                        {{-- Galería --}}
                        @foreach($directorioFloral->galeria as $img)
                            <div class="group relative aspect-square rounded-lg overflow-hidden ring-1 ring-zinc-200 dark:ring-zinc-700">
                                <img
                                    src="{{ Storage::url($img->imagen) }}"
                                    alt="{{ $directorioFloral->nombre }}"
                                    class="h-full w-full object-cover transition group-hover:scale-105"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Descripción --}}
            @if($directorioFloral->descripcion)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                    <flux:heading size="sm" class="mb-3">Descripción</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {{ $directorioFloral->descripcion }}
                    </flux:text>
                </div>
            @endif

            {{-- Contenido --}}
            @if($directorioFloral->contenido)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                    <flux:heading size="sm" class="mb-3">Contenido</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {{ $directorioFloral->contenido }}
                    </flux:text>
                </div>
            @endif

            {{-- Productos que usan esta flor --}}
            @if($directorioFloral->productos->isNotEmpty())
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                    <flux:heading size="sm" class="mb-4">
                        Productos que incluyen esta flor
                        <span class="text-zinc-400 font-normal text-xs ml-1">({{ $directorioFloral->productos->count() }})</span>
                    </flux:heading>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($directorioFloral->productos as $producto)
                            <div class="flex items-center justify-between py-3">
                                <div class="flex items-center gap-3">
                                    @if($producto->galeria->first())
                                        <img
                                            src="{{ Storage::url($producto->galeria->first()->imagen) }}"
                                            alt="{{ $producto->nombre }}"
                                            class="h-9 w-9 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                        >
                                    @else
                                        <div class="h-9 w-9 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center ring-1 ring-zinc-200 dark:ring-zinc-700">
                                            <flux:icon name="shopping-bag" class="size-4 text-zinc-400" />
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $producto->nombre }}</p>
                                        <p class="text-xs text-zinc-400">${{ number_format($producto->precio_venta, 2) }}</p>
                                    </div>
                                </div>
                                <flux:button
                                    href="{{ route('admin.productos.edit', $producto) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil-square"
                                    wire:navigate
                                />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Columna lateral --}}
        <div class="w-72 shrink-0 space-y-4">

            {{-- Imagen principal --}}
            @if($directorioFloral->imagen)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <img
                        src="{{ Storage::url($directorioFloral->imagen) }}"
                        alt="{{ $directorioFloral->nombre }}"
                        class="w-full aspect-[4/3] object-cover"
                    >
                </div>
            @endif

            {{-- Detalles --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                <flux:heading size="sm">Detalles</flux:heading>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Slug</flux:text>
                    <p class="text-xs font-mono text-zinc-500 mt-0.5 break-all">/{{ $directorioFloral->slug }}</p>
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Orden</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $directorioFloral->orden }}</p>
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Creado</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $directorioFloral->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizado</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $directorioFloral->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            {{-- Ver en sitio --}}
            <flux:button
                href="{{ route('directorio-floral.show', $directorioFloral) }}"
                variant="ghost"
                icon="arrow-top-right-on-square"
                class="w-full"
                target="_blank"
            >
                Ver en el sitio
            </flux:button>

        </div>
    </div>

</div>
</x-layouts::app>