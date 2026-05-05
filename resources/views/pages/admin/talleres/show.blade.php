<x-layouts::app :title="$taller->nombre">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="$taller->nombre"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Talleres',  'route' => 'admin.talleres.index'],
            ['label' => $taller->nombre],
        ]"
        :accion="['label' => 'Editar', 'route' => 'admin.talleres.edit', 'param' => $taller]"
    >
        {{ $taller->slug }}
    </x-admin.page-header>

    {{-- Métricas rápidas --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Estado</flux:text>
            <div class="mt-2">
                @if($taller->estado)
                    <flux:badge color="green" icon="check-circle">Activo</flux:badge>
                @else
                    <flux:badge color="zinc" icon="x-circle">Inactivo</flux:badge>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">Fecha</flux:text>
            <div class="mt-2">
                @if($taller->fecha)
                    <flux:badge color="{{ $taller->fecha->isFuture() ? 'blue' : 'zinc' }}" icon="calendar">
                        {{ $taller->fecha->format('d M Y · H:i') }}
                    </flux:badge>
                @else
                    <span class="text-sm text-zinc-400">Sin fecha asignada</span>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">
                @if($taller->fecha)
                    {{ $taller->fecha->isFuture() ? 'Tiempo restante' : 'Ocurrió hace' }}
                @else
                    Registro
                @endif
            </flux:text>
            <p class="mt-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                @if($taller->fecha)
                    {{ $taller->fecha->diffForHumans() }}
                @else
                    {{ $taller->created_at->format('d M Y') }}
                @endif
            </p>
        </div>

    </div>

    <div class="flex gap-6 items-start">

        {{-- Columna principal --}}
        <div class="flex-1 space-y-6">

            {{-- Imagen --}}
            @if($taller->imagen)
                <div class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                    <img
                        src="{{ Storage::url($taller->imagen) }}"
                        alt="{{ $taller->nombre }}"
                        class="w-full aspect-video object-cover"
                    >
                </div>
            @endif

            {{-- Detalles --}}
            @if($taller->detalles)
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                    <flux:heading size="sm" class="mb-3">Detalles</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400 leading-relaxed whitespace-pre-line">
                        {{ $taller->detalles }}
                    </flux:text>
                </div>
            @else
                <div class="rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 p-10 text-center">
                    <flux:icon name="document-text" class="size-10 mx-auto mb-2 text-zinc-300" />
                    <flux:text class="text-zinc-400">Este taller no tiene detalles redactados aún.</flux:text>
                    <flux:button
                        href="{{ route('admin.talleres.edit', $taller) }}"
                        variant="ghost"
                        size="sm"
                        class="mt-3"
                        wire:navigate
                    >
                        Agregar detalles
                    </flux:button>
                </div>
            @endif

        </div>

        {{-- Columna lateral --}}
        <div class="w-72 shrink-0 space-y-4">

            {{-- Información --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                <flux:heading size="sm">Información</flux:heading>

                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Slug</flux:text>
                    <p class="text-xs font-mono text-zinc-500 mt-0.5 break-all">/{{ $taller->slug }}</p>
                </div>

                @if($taller->fecha)
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Fecha completa</flux:text>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                            {{ $taller->fecha->isoFormat('dddd D [de] MMMM [de] YYYY, H:mm') }}
                        </p>
                    </div>
                @endif

                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Creado</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $taller->created_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizado</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $taller->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-2">
                <flux:heading size="sm" class="mb-3">Acciones</flux:heading>

                <flux:button
                    href="{{ route('admin.talleres.edit', $taller) }}"
                    variant="filled"
                    icon="pencil-square"
                    class="w-full"
                    wire:navigate
                >
                    Editar taller
                </flux:button>

                <flux:button
                    href="{{ route('talleres.show', $taller) }}"
                    variant="ghost"
                    icon="arrow-top-right-on-square"
                    class="w-full"
                    target="_blank"
                >
                    Ver en el sitio
                </flux:button>

                <form
                    method="POST"
                    action="{{ route('admin.talleres.destroy', $taller) }}"
                    onsubmit="return confirm('¿Eliminar este taller permanentemente?')"
                >
                    @csrf
                    @method('DELETE')
                    <flux:button
                        type="submit"
                        variant="ghost"
                        icon="trash"
                        class="w-full text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950"
                    >
                        Eliminar taller
                    </flux:button>
                </form>
            </div>

        </div>
    </div>

</div>
</x-layouts::app>