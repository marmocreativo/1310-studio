<x-layouts::app :title="'Editar: ' . $zona->nombre">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="'Editar: ' . $zona->nombre"
        :breadcrumbs="[
            ['label' => 'Dashboard',      'route' => 'admin.dashboard'],
            ['label' => 'Zonas de envío', 'route' => 'admin.zonas-envio.index'],
            ['label' => $zona->nombre],
        ]"
    />

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
            <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    <div class="flex gap-6 items-start">

        <div class="flex-1">
            <form id="form-zona" method="POST" action="{{ route('admin.zonas-envio.update', $zona) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

                    <flux:field>
                        <flux:label>Nombre *</flux:label>
                        <flux:input name="nombre" value="{{ old('nombre', $zona->nombre) }}" required />
                        <flux:error name="nombre" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Descripción</flux:label>
                        <flux:textarea name="descripcion" rows="2">{{ old('descripcion', $zona->descripcion) }}</flux:textarea>
                        <flux:error name="descripcion" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Precio de envío *</flux:label>
                        <flux:input type="number" name="precio" value="{{ old('precio', $zona->precio) }}" min="0" step="0.01" required />
                        <flux:description>En pesos mexicanos. Usa 0 para envío gratis.</flux:description>
                        <flux:error name="precio" />
                    </flux:field>

                    {{-- Alcaldías dinámicas --}}
                    <div
                        x-data="{
                            alcaldias: {{ old('alcaldias')
                                ? json_encode(old('alcaldias'))
                                : json_encode($zona->alcaldias->pluck('nombre')->toArray() ?: ['']) }},
                            agregar() { this.alcaldias.push('') },
                            quitar(i) { this.alcaldias.splice(i, 1) },
                        }"
                        class="space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <flux:label>Alcaldías / Municipios que cubre</flux:label>
                            <button
                                type="button"
                                @click="agregar()"
                                class="text-xs text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 underline">
                                + Agregar
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(alcaldia, i) in alcaldias" :key="i">
                                <div class="flex gap-2">
                                    <flux:input
                                        x-model="alcaldias[i]"
                                        ::name="'alcaldias[' + i + ']'"
                                        placeholder="Ej. Cuauhtémoc"
                                        class="flex-1"
                                    />
                                    <button
                                        type="button"
                                        @click="quitar(i)"
                                        x-show="alcaldias.length > 1"
                                        class="p-2 text-zinc-400 hover:text-red-500 transition-colors">
                                        <flux:icon name="x-mark" class="size-4" />
                                    </button>
                                </div>
                            </template>
                        </div>
                        <flux:error name="alcaldias" />
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                            Guardar cambios
                        </button>
                        <flux:button href="{{ route('admin.zonas-envio.index') }}" variant="ghost" wire:navigate>
                            Cancelar
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Sidebar --}}
        <div class="w-72 shrink-0 space-y-4">

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
                <flux:heading size="sm">Publicación</flux:heading>
                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="activa" form="form-zona">
                        <flux:select.option value="1" :selected="old('activa', $zona->activa ? '1' : '0') == '1'">Activa</flux:select.option>
                        <flux:select.option value="0" :selected="old('activa', $zona->activa ? '1' : '0') == '0'">Inactiva</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Orden</flux:label>
                    <flux:input type="number" name="orden" value="{{ old('orden', $zona->orden) }}" min="0" form="form-zona" />
                    <flux:description>Menor número aparece primero.</flux:description>
                </flux:field>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                <flux:heading size="sm">Información</flux:heading>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Creada</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $zona->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizada</flux:text>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $zona->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            {{-- Eliminar --}}
            <form method="POST" action="{{ route('admin.zonas-envio.destroy', $zona) }}"
                  onsubmit="return confirm('¿Eliminar esta zona? Los pedidos existentes no se verán afectados.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full text-xs text-red-500 hover:text-red-700 transition-colors py-2 text-center">
                    Eliminar zona
                </button>
            </form>

        </div>
    </div>

</div>
</x-layouts::app>