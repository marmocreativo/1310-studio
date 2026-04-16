<x-layouts::app :title="__('Editar página')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.paginas.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Editar página</flux:heading>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <form method="POST" action="{{ route('admin.paginas.update', $pagina) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
                @csrf
                @method('PUT')

                <flux:field>
                    <flux:label>Título</flux:label>
                    <flux:input name="titulo" value="{{ old('titulo', $pagina->titulo) }}" required />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Categoría</flux:label>
                    <flux:select name="categoria">
                        <flux:select.option value="general" :selected="old('categoria', $pagina->categoria) === 'general'">General</flux:select.option>
                        <flux:select.option value="legal" :selected="old('categoria', $pagina->categoria) === 'legal'">Legal</flux:select.option>
                    </flux:select>
                    <flux:error name="categoria" />
                </flux:field>

                <flux:field>
                    <flux:label>Resumen</flux:label>
                    <flux:textarea name="resumen" rows="3">{{ old('resumen', $pagina->resumen) }}</flux:textarea>
                    <flux:error name="resumen" />
                </flux:field>

                <flux:field>
                    <flux:label>Contenido</flux:label>
                    <flux:textarea name="contenido" rows="10">{{ old('contenido', $pagina->contenido) }}</flux:textarea>
                    <flux:error name="contenido" />
                </flux:field>

                <flux:field>
                    <flux:label>Imagen principal</flux:label>
                    <div class="mb-3">
                        <img src="{{ $pagina->imagen_url }}" alt="{{ $pagina->titulo }}"
                             class="w-40 h-28 object-cover rounded">
                    </div>
                    <input type="file" name="imagen" accept="image/*"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200" />
                    <flux:description>Deja vacío para mantener la imagen actual.</flux:description>
                    <flux:error name="imagen" />
                </flux:field>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="borrador" :selected="old('estado', $pagina->estado) === 'borrador'">Borrador</flux:select.option>
                        <flux:select.option value="publicado" :selected="old('estado', $pagina->estado) === 'publicado'">Publicado</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                    <flux:button href="{{ route('admin.paginas.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts::app>