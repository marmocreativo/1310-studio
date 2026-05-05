<x-layouts::app :title="__('Nueva Flor')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 max-w-2xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Nueva Flor</flux:heading>
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

        <form method="POST" action="{{ route('admin.directorio-floral.store') }}"
              enctype="multipart/form-data"
              class="flex flex-col gap-6 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Nombre *</flux:label>
                    <flux:input name="nombre" value="{{ old('nombre') }}" required />
                    <flux:error name="nombre" />
                </flux:field>

                <flux:field>
                    <flux:label>Categoría</flux:label>
                    <flux:input name="categoria" value="{{ old('categoria') }}" placeholder="Ej: Tropical, Silvestre..." />
                    <flux:error name="categoria" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Descripción corta</flux:label>
                <flux:textarea name="descripcion" rows="3" placeholder="Breve descripción de la flor...">{{ old('descripcion') }}</flux:textarea>
                <flux:error name="descripcion" />
            </flux:field>

            <flux:field>
                <flux:label>Contenido</flux:label>
                <flux:textarea name="contenido" rows="6" placeholder="Información detallada, cuidados, temporadas...">{{ old('contenido') }}</flux:textarea>
                <flux:error name="contenido" />
            </flux:field>

            <flux:field>
                <flux:label>Imagen principal</flux:label>
                <input type="file" name="imagen" accept="image/*"
                       class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-300">
                <flux:error name="imagen" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Orden</flux:label>
                    <flux:input type="number" name="orden" value="{{ old('orden', 0) }}" min="0" />
                    <flux:error name="orden" />
                </flux:field>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="1" :selected="old('estado', '1') === '1'">Activa</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado') === '0'">Inactiva</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>
            </div>

            <div class="flex gap-3 pt-2">
                <flux:button type="submit" variant="primary">Crear flor</flux:button>
                <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            </div>

        </form>
    </div>
</x-layouts::app>