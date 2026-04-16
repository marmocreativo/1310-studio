<x-layouts::app :title="__('Editar categoría')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.categorias.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Editar categoría</flux:heading>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <form method="POST" action="{{ route('admin.categorias.update', $categoria) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
                @csrf
                @method('PUT')

                <flux:field>
                    <flux:label>Título</flux:label>
                    <flux:input name="titulo" value="{{ old('titulo', $categoria->titulo) }}" required />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Categoría padre</flux:label>
                    <flux:select name="id_padre">
                        <flux:select.option value="">Sin categoría padre (raíz)</flux:select.option>
                        @foreach ($padres as $padre)
                            <flux:select.option value="{{ $padre->id }}" :selected="old('id_padre', $categoria->id_padre) == $padre->id">
                                {{ $padre->titulo }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="id_padre" />
                </flux:field>

                <flux:field>
                    <flux:label>Resumen</flux:label>
                    <flux:textarea name="resumen" rows="3">{{ old('resumen', $categoria->resumen) }}</flux:textarea>
                    <flux:error name="resumen" />
                </flux:field>

                <flux:field>
                    <flux:label>Imagen</flux:label>
                    <div class="mb-3">
                        <img src="{{ $categoria->imagen_url }}" alt="{{ $categoria->titulo }}"
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
                        <flux:select.option value="borrador" :selected="old('estado', $categoria->estado) === 'borrador'">Borrador</flux:select.option>
                        <flux:select.option value="publicado" :selected="old('estado', $categoria->estado) === 'publicado'">Publicado</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                    <flux:button href="{{ route('admin.categorias.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts::app>