<x-layouts::app :title="__('Editar Flor')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 max-w-2xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Editar: {{ $directorioFloral->nombre }}</flux:heading>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li><flux:text class="text-red-700 dark:text-red-400 text-sm">{{ $error }}</flux:text></li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Datos principales --}}
        <form method="POST" action="{{ route('admin.directorio-floral.update', $directorioFloral) }}"
              enctype="multipart/form-data"
              class="flex flex-col gap-6 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Nombre *</flux:label>
                    <flux:input name="nombre" value="{{ old('nombre', $directorioFloral->nombre) }}" required />
                    <flux:error name="nombre" />
                </flux:field>

                <flux:field>
                    <flux:label>Categoría</flux:label>
                    <flux:input name="categoria" value="{{ old('categoria', $directorioFloral->categoria) }}" placeholder="Ej: Tropical, Silvestre..." />
                    <flux:error name="categoria" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Descripción corta</flux:label>
                <flux:textarea name="descripcion" rows="3">{{ old('descripcion', $directorioFloral->descripcion) }}</flux:textarea>
                <flux:error name="descripcion" />
            </flux:field>

            <flux:field>
                <flux:label>Contenido</flux:label>
                <flux:textarea name="contenido" rows="6">{{ old('contenido', $directorioFloral->contenido) }}</flux:textarea>
                <flux:error name="contenido" />
            </flux:field>

            <flux:field>
                <flux:label>Imagen principal</flux:label>
                @if ($directorioFloral->imagen)
                    <div class="mb-3">
                        <img src="{{ Storage::url($directorioFloral->imagen) }}"
                             alt="{{ $directorioFloral->nombre }}"
                             class="w-32 h-32 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <flux:text class="text-xs text-zinc-400 mt-1">Imagen actual — sube una nueva para reemplazarla</flux:text>
                    </div>
                @endif
                <input type="file" name="imagen" accept="image/*"
                       class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-300">
                <flux:error name="imagen" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Orden</flux:label>
                    <flux:input type="number" name="orden" value="{{ old('orden', $directorioFloral->orden) }}" min="0" />
                    <flux:error name="orden" />
                </flux:field>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="1" :selected="old('estado', $directorioFloral->estado) == '1'">Activa</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado', $directorioFloral->estado) == '0'">Inactiva</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>
            </div>

            <div class="flex gap-3 pt-2">
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            </div>
        </form>

        {{-- Galería --}}
        <div class="flex flex-col gap-4 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <flux:heading size="lg">Galería de imágenes</flux:heading>

            @if ($directorioFloral->galeria->count())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach ($directorioFloral->galeria as $img)
                        <div class="relative group">
                            <img src="{{ Storage::url($img->imagen) }}"
                                 alt="Imagen galería"
                                 class="w-full aspect-square object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <form method="POST"
                                  action="{{ route('admin.directorio-floral.galeria.destroy', [$directorioFloral, $img]) }}"
                                  class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('¿Eliminar imagen?')"
                                        class="bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs leading-none">
                                    ✕
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <flux:text class="text-zinc-400">No hay imágenes en la galería aún.</flux:text>
            @endif

            {{-- Subir nuevas imágenes --}}
            <form method="POST"
                  action="{{ route('admin.directorio-floral.galeria.store', $directorioFloral) }}"
                  enctype="multipart/form-data"
                  class="flex flex-col gap-3 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                @csrf
                <flux:label>Agregar imágenes</flux:label>
                <input type="file" name="imagenes[]" accept="image/*" multiple
                       class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-300">
                <div>
                    <flux:button type="submit" variant="ghost" size="sm">Subir imágenes</flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts::app>