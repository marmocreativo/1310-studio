<x-layouts::app title="Nueva zona de envío">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="'Nueva zona de envío'"
        :breadcrumbs="[
            ['label' => 'Dashboard',      'route' => 'admin.dashboard'],
            ['label' => 'Zonas de envío', 'route' => 'admin.zonas-envio.index'],
            ['label' => 'Nueva zona'],
        ]"
    />

    <div class="flex gap-6 items-start">

        <div class="flex-1">
            <form id="form-zona" method="POST" action="{{ route('admin.zonas-envio.store') }}">
                @csrf

                <div class="space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

                    <flux:field>
                        <flux:label>Nombre *</flux:label>
                        <flux:input name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Zona Centro" />
                        <flux:error name="nombre" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Descripción</flux:label>
                        <flux:textarea name="descripcion" rows="2" placeholder="Descripción breve de la zona">{{ old('descripcion') }}</flux:textarea>
                        <flux:error name="descripcion" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Precio de envío *</flux:label>
                        <flux:input type="number" name="precio" value="{{ old('precio', 0) }}" min="0" step="0.01" required />
                        <flux:description>En pesos mexicanos. Usa 0 para envío gratis.</flux:description>
                        <flux:error name="precio" />
                    </flux:field>

                    {{-- Alcaldías dinámicas --}}
                    <div
                        x-data="{
                            alcaldias: {{ old('alcaldias') ? json_encode(old('alcaldias')) : '[\'\']' }},
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
                            Crear zona
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
                        <flux:select.option value="1" :selected="old('activa', '1') == '1'">Activa</flux:select.option>
                        <flux:select.option value="0" :selected="old('activa') == '0'">Inactiva</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Orden</flux:label>
                    <flux:input type="number" name="orden" value="{{ old('orden', 0) }}" min="0" form="form-zona" />
                    <flux:description>Menor número aparece primero.</flux:description>
                </flux:field>
            </div>
        </div>

    </div>

</div>
</x-layouts::app>