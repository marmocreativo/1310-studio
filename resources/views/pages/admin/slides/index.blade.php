<x-layouts::app :title="__('Slides Hero')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Slides Hero"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Slides Hero'],
        ]"
        :accion="['label' => 'Nuevo slide', 'route' => 'admin.slides.create']"
    >
        Gestiona los slides del hero de la página de inicio.
    </x-admin.page-header>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-left">
                    <th class="px-4 py-3 w-10 font-medium text-zinc-500 dark:text-zinc-400">#</th>
                    <th class="px-4 py-3 w-20"></th>
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Título / Caption</th>
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Tipo</th>
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Estado</th>
                    <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($slides as $slide)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">

                        {{-- Orden --}}
                        <td class="px-4 py-3 text-zinc-400 font-mono text-xs">
                            {{ $slide->orden }}
                        </td>

                        {{-- Miniatura --}}
                        <td class="px-4 py-3">
                            @if($slide->imagen_fondo_url)
                                <img
                                    src="{{ $slide->imagen_fondo_url }}"
                                    alt="{{ $slide->titulo }}"
                                    class="h-10 w-16 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                >
                            @else
                                <div class="h-10 w-16 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                    <flux:icon name="photo" class="size-5 text-zinc-300" />
                                </div>
                            @endif
                        </td>

                        {{-- Título + Caption --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-zinc-900 dark:text-white">
                                {{ $slide->titulo ?: '(sin título)' }}
                            </p>
                            @if($slide->caption)
                                <p class="text-xs text-zinc-400 mt-0.5">{{ $slide->caption }}</p>
                            @endif
                        </td>

                        {{-- Tipo --}}
                        <td class="px-4 py-3">
                            @php
                                $tipoConfig = match($slide->tipo->value) {
                                    'video' => ['color' => 'purple', 'icon' => 'film',        'label' => 'Video'],
                                    'capas' => ['color' => 'blue',   'icon' => 'square-2-stack', 'label' => 'Capas'],
                                    default => ['color' => 'zinc',   'icon' => 'photo',       'label' => 'Imagen'],
                                };
                            @endphp
                            <flux:badge
                                color="{{ $tipoConfig['color'] }}"
                                icon="{{ $tipoConfig['icon'] }}"
                                size="sm"
                            >
                                {{ $tipoConfig['label'] }}
                            </flux:badge>
                        </td>

                        {{-- Estado --}}
                        <td class="px-4 py-3">
                            @if($slide->estado)
                                <flux:badge color="green" size="sm" icon="check-circle">Activo</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" icon="eye-slash">Inactivo</flux:badge>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button
                                    href="{{ route('admin.slides.edit', $slide) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil-square"
                                    title="Editar"
                                    wire:navigate
                                />
                                <form
                                    method="POST"
                                    action="{{ route('admin.slides.destroy', $slide) }}"
                                    onsubmit="return confirm('¿Eliminar este slide?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <flux:button
                                        type="submit"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        title="Eliminar"
                                        class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950"
                                    />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <flux:icon name="photo" class="size-10 mx-auto mb-3 text-zinc-300" />
                            <flux:text class="text-zinc-400">No hay slides creados aún.</flux:text>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($slides->hasPages())
        <div>{{ $slides->links() }}</div>
    @endif

</div>
</x-layouts::app>