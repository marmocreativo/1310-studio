<x-layouts::app :title="$producto->nombre">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <x-admin.page-header
            :titulo="$producto->nombre"
            :breadcrumbs="[
                ['label' => 'Dashboard',  'route' => 'admin.dashboard'],
                ['label' => 'Productos',  'route' => 'admin.productos.index'],
                ['label' => $producto->nombre],
            ]"
            :accion="['label' => 'Editar producto', 'route' => 'admin.productos.edit', 'param' => $producto]"
        >
            {{ $producto->slug }}
        </x-admin.page-header>

        {{-- Métricas rápidas --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Precio venta</flux:text>
                <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">
                    ${{ number_format($producto->precio_venta, 2) }}
                </p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Precio lista</flux:text>
                @if($producto->tiene_descuento)
                    <p class="mt-1 text-2xl font-semibold text-zinc-400 line-through">
                        ${{ number_format($producto->precio_lista, 2) }}
                    </p>
                    <flux:badge color="red" size="sm" class="mt-1">-{{ $producto->porcentaje_descuento }}%</flux:badge>
                @else
                    <p class="mt-1 text-2xl font-semibold text-zinc-300 dark:text-zinc-600">—</p>
                @endif
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Estado</flux:text>
                <div class="mt-2">
                    @if($producto->estado)
                        <flux:badge color="green" icon="check-circle">Activo</flux:badge>
                    @else
                        <flux:badge color="zinc" icon="x-circle">Inactivo</flux:badge>
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Destacado</flux:text>
                <div class="mt-2">
                    @if($producto->destacado)
                        <flux:badge color="yellow" icon="star">Destacado</flux:badge>
                    @else
                        <flux:badge color="zinc">No destacado</flux:badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex gap-6 items-start">

            {{-- Columna principal --}}
            <div class="flex-1 space-y-6">

                {{-- Galería --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                    <flux:heading size="sm" class="mb-4">Galería</flux:heading>

                    @if($producto->galeria->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700">
                            <flux:icon name="photo" class="size-10 text-zinc-300 mb-2" />
                            <flux:text class="text-zinc-400">Este producto no tiene imágenes.</flux:text>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($producto->galeria as $imagen)
                                <div class="group relative aspect-square rounded-lg overflow-hidden ring-1 ring-zinc-200 dark:ring-zinc-700">
                                    <img
                                        src="{{ Storage::url($imagen->imagen) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="h-full w-full object-cover transition group-hover:scale-105"
                                    >
                                    @if($loop->first)
                                        <div class="absolute top-2 left-2">
                                            <flux:badge color="zinc" size="sm">Portada</flux:badge>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Descripción --}}
                @if($producto->descripcion)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                        <flux:heading size="sm" class="mb-3">Descripción</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ $producto->descripcion }}
                        </flux:text>
                    </div>
                @endif

                {{-- Detalles --}}
                @if($producto->detalles)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                        <flux:heading size="sm" class="mb-3">Detalles</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ $producto->detalles }}
                        </flux:text>
                    </div>
                @endif

            </div>

            {{-- Columna lateral --}}
            <div class="w-72 shrink-0 space-y-4">

                {{-- Categorías --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <flux:heading size="sm">Categorías</flux:heading>
                        <flux:badge color="zinc" size="sm">{{ $producto->categorias->count() }}</flux:badge>
                    </div>
                    @if($producto->categorias->isEmpty())
                        <flux:text class="text-sm text-zinc-400">Sin categorías asignadas.</flux:text>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($producto->categorias as $cat)
                                <flux:badge color="blue" size="sm">{{ $cat->titulo }}</flux:badge>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Flores --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <flux:heading size="sm">Flores</flux:heading>
                        <flux:badge color="zinc" size="sm">{{ $producto->flores->count() }}</flux:badge>
                    </div>
                    @if($producto->flores->isEmpty())
                        <flux:text class="text-sm text-zinc-400">Sin flores asignadas.</flux:text>
                    @else
                        <div class="space-y-2">
                            @foreach($producto->flores as $flor)
                                <div class="flex items-center gap-2">
                                    @if($flor->imagen)
                                        <img
                                            src="{{ Storage::url($flor->imagen) }}"
                                            alt="{{ $flor->nombre }}"
                                            class="h-7 w-7 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                        >
                                    @else
                                        <div class="h-7 w-7 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center ring-1 ring-zinc-200 dark:ring-zinc-700">
                                            <flux:icon name="sparkles" class="size-3 text-zinc-400" />
                                        </div>
                                    @endif
                                    <flux:text class="text-sm text-zinc-700 dark:text-zinc-300">{{ $flor->nombre }}</flux:text>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Slug --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                    <flux:heading size="sm" class="mb-2">Slug</flux:heading>
                    <p class="text-xs font-mono text-zinc-500 dark:text-zinc-400 break-all">/{{ $producto->slug }}</p>
                </div>

                {{-- Fechas --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Registro</flux:heading>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Creado</flux:text>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                            {{ $producto->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizado</flux:text>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                            {{ $producto->updated_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts::app>