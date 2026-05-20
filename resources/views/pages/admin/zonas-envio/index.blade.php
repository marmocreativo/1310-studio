<x-layouts::app title="Zonas de envío">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="'Zonas de envío'"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Zonas de envío'],
        ]"
        :accion="['label' => 'Nueva zona', 'route' => 'admin.zonas-envio.create']"
    />

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
        @if($zonas->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <flux:icon name="map-pin" class="size-10 text-zinc-300 mb-3" />
                <flux:text class="text-zinc-500 text-sm">No hay zonas de envío configuradas.</flux:text>
                <flux:button href="{{ route('admin.zonas-envio.create') }}" variant="primary" class="mt-4" wire:navigate>
                    Crear primera zona
                </flux:button>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Zona</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Alcaldías / Municipios</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide w-28">Precio</th>
                        <th class="px-5 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wide w-24">Estado</th>
                        <th class="px-5 py-3 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($zonas as $zona)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">

                            <td class="px-5 py-4">
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $zona->nombre }}</p>
                                @if($zona->descripcion)
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ $zona->descripcion }}</p>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($zona->alcaldias as $alcaldia)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            {{ $alcaldia->nombre }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-zinc-400">Sin alcaldías</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                    ${{ number_format($zona->precio, 2) }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if($zona->activa)
                                    <flux:badge color="green" size="sm">Activa</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Inactiva</flux:badge>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right">
                                <flux:button
                                    href="{{ route('admin.zonas-envio.edit', $zona) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil"
                                    wire:navigate
                                />
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
</x-layouts::app>