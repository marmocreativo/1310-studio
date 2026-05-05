<x-layouts::app :title="__('Editar Producto')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 max-w-2xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Editar: {{ $producto->nombre }}</flux:heading>
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
        <form method="POST" action="{{ route('admin.productos.update', $producto) }}"
              class="flex flex-col gap-6 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @csrf
            @method('PUT')

            <flux:field>
                <flux:label>Nombre *</flux:label>
                <flux:input name="nombre" value="{{ old('nombre', $producto->nombre) }}" required />
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label>Descripción corta</flux:label>
                <flux:textarea name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</flux:textarea>
                <flux:error name="descripcion" />
            </flux:field>

            <flux:field>
                <flux:label>Detalles</flux:label>
                <flux:textarea name="detalles" rows="5">{{ old('detalles', $producto->detalles) }}</flux:textarea>
                <flux:error name="detalles" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Precio lista</flux:label>
                    <flux:input type="number" name="precio_lista"
                                value="{{ old('precio_lista', $producto->precio_lista) }}"
                                min="0" step="0.01" />
                    <flux:description>Dejar vacío si no aplica descuento.</flux:description>
                    <flux:error name="precio_lista" />
                </flux:field>

                <flux:field>
                    <flux:label>Precio venta *</flux:label>
                    <flux:input type="number" name="precio_venta"
                                value="{{ old('precio_venta', $producto->precio_venta) }}"
                                min="0" step="0.01" required />
                    <flux:error name="precio_venta" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="1" :selected="old('estado', $producto->estado) == 1">Activo</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado', $producto->estado) == 0">Inactivo</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <flux:field>
                    <flux:label>Destacado</flux:label>
                    <flux:select name="destacado">
                        <flux:select.option value="0" :selected="old('destacado', $producto->destacado) == 0">No</flux:select.option>
                        <flux:select.option value="1" :selected="old('destacado', $producto->destacado) == 1">Sí</flux:select.option>
                    </flux:select>
                    <flux:error name="destacado" />
                </flux:field>
            </div>

            {{-- Categorías --}}
            @php $categoriasActivas = $producto->categorias->pluck('id')->toArray(); @endphp
            <flux:field>
                <flux:label>Categorías</flux:label>
                <div class="grid grid-cols-2 gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    @foreach ($categorias as $categoria)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <flux:checkbox name="categorias[]" value="{{ $categoria->id }}"
                                :checked="in_array($categoria->id, old('categorias', $categoriasActivas))" />
                            <span class="text-sm">{{ $categoria->titulo }}</span>
                        </label>
                    @endforeach
                </div>
                <flux:error name="categorias" />
            </flux:field>

            {{-- Flores --}}
            @php $floresActivas = $producto->flores->pluck('id')->toArray(); @endphp
            <flux:field>
                <flux:label>Flores incluidas</flux:label>
                <div class="grid grid-cols-2 gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 max-h-48 overflow-y-auto">
                    @foreach ($flores as $flor)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <flux:checkbox name="flores[]" value="{{ $flor->id }}"
                                :checked="in_array($flor->id, old('flores', $floresActivas))" />
                            <span class="text-sm">{{ $flor->nombre }}</span>
                        </label>
                    @endforeach
                </div>
                <flux:error name="flores" />
            </flux:field>

            <div class="flex gap-3 pt-2">
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            </div>
        </form>

        {{-- Galería --}}
        <div class="flex flex-col gap-4 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <flux:heading size="lg">Galería de imágenes</flux:heading>

            @if ($producto->galeria->count())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach ($producto->galeria as $img)
                        <div class="relative group">
                            <img src="{{ Storage::url($img->imagen) }}"
                                 alt="Imagen producto"
                                 class="w-full aspect-square object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <form method="POST"
                                  action="{{ route('admin.productos.galeria.destroy', [$producto, $img]) }}"
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

            <form method="POST"
                  action="{{ route('admin.productos.galeria.store', $producto) }}"
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