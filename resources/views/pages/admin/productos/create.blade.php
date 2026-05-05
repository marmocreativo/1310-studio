<x-layouts::app :title="__('Nuevo Producto')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 max-w-2xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Nuevo Producto</flux:heading>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li><flux:text class="text-red-700 dark:text-red-400 text-sm">{{ $error }}</flux:text></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.productos.store') }}"
              enctype="multipart/form-data"
              class="flex flex-col gap-6 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @csrf

            <flux:field>
                <flux:label>Nombre *</flux:label>
                <flux:input name="nombre" value="{{ old('nombre') }}" required />
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label>Descripción corta</flux:label>
                <flux:textarea name="descripcion" rows="3">{{ old('descripcion') }}</flux:textarea>
                <flux:error name="descripcion" />
            </flux:field>

            <flux:field>
                <flux:label>Detalles</flux:label>
                <flux:textarea name="detalles" rows="5" placeholder="Composición, cuidados, tamaños disponibles...">{{ old('detalles') }}</flux:textarea>
                <flux:error name="detalles" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Precio lista</flux:label>
                    <flux:input type="number" name="precio_lista" value="{{ old('precio_lista') }}" min="0" step="0.01" placeholder="0.00" />
                    <flux:description>Precio original (tachado). Dejar vacío si no aplica.</flux:description>
                    <flux:error name="precio_lista" />
                </flux:field>

                <flux:field>
                    <flux:label>Precio venta *</flux:label>
                    <flux:input type="number" name="precio_venta" value="{{ old('precio_venta') }}" min="0" step="0.01" required />
                    <flux:error name="precio_venta" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="1" :selected="old('estado', '1') === '1'">Activo</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado') === '0'">Inactivo</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <flux:field>
                    <flux:label>Destacado</flux:label>
                    <flux:select name="destacado">
                        <flux:select.option value="0" :selected="old('destacado', '0') === '0'">No</flux:select.option>
                        <flux:select.option value="1" :selected="old('destacado') === '1'">Sí</flux:select.option>
                    </flux:select>
                    <flux:error name="destacado" />
                </flux:field>
            </div>

            {{-- Categorías --}}
            <flux:field>
                <flux:label>Categorías</flux:label>
                <div class="grid grid-cols-2 gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    @foreach ($categorias as $categoria)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <flux:checkbox name="categorias[]" value="{{ $categoria->id }}"
                                :checked="in_array($categoria->id, old('categorias', []))" />
                            <span class="text-sm">{{ $categoria->nombre }}</span>
                        </label>
                    @endforeach
                </div>
                <flux:error name="categorias" />
            </flux:field>

            {{-- Flores --}}
            <flux:field>
                <flux:label>Flores incluidas</flux:label>
                <div class="grid grid-cols-2 gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 max-h-48 overflow-y-auto">
                    @foreach ($flores as $flor)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <flux:checkbox name="flores[]" value="{{ $flor->id }}"
                                :checked="in_array($flor->id, old('flores', []))" />
                            <span class="text-sm">{{ $flor->nombre }}</span>
                        </label>
                    @endforeach
                </div>
                <flux:error name="flores" />
            </flux:field>

            <div class="flex gap-3 pt-2">
                <flux:button type="submit" variant="primary">Crear producto</flux:button>
                <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>