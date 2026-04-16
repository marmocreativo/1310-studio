<x-layouts::app :title="__('Nueva página')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.paginas.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Nueva página</flux:heading>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <form method="POST" action="{{ route('admin.paginas.store') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
                @csrf

                <flux:field>
                    <flux:label>Título</flux:label>
                    <flux:input name="titulo" value="{{ old('titulo') }}" required />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Categoría</flux:label>
                    <flux:select name="categoria">
                        <flux:select.option value="general" :selected="old('categoria') === 'general'">General</flux:select.option>
                        <flux:select.option value="legal" :selected="old('categoria') === 'legal'">Legal</flux:select.option>
                    </flux:select>
                    <flux:error name="categoria" />
                </flux:field>

                <flux:field>
                    <flux:label>Resumen</flux:label>
                    <flux:textarea name="resumen" rows="3">{{ old('resumen') }}</flux:textarea>
                    <flux:error name="resumen" />
                </flux:field>

                <flux:field>
                    <flux:label>Contenido</flux:label>
                    <flux:textarea name="contenido" rows="10">{{ old('contenido') }}</flux:textarea>
                    <flux:error name="contenido" />
                </flux:field>

                <flux:field>
                    <flux:label>Imagen principal</flux:label>
                    <input type="file" name="imagen" accept="image/*"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200" />
                    <flux:description>Máximo 5MB. Se redimensionará a 1200px. Si no subes imagen se usará la imagen por defecto.</flux:description>
                    <flux:error name="imagen" />
                </flux:field>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="borrador" :selected="old('estado', 'borrador') === 'borrador'">Borrador</flux:select.option>
                        <flux:select.option value="publicado" :selected="old('estado') === 'publicado'">Publicado</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">Crear página</flux:button>
                    <flux:button href="{{ route('admin.paginas.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts::app>