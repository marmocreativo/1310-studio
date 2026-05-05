<x-layouts::app :title="'Editar: ' . $producto->nombre">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <x-admin.page-header
            :titulo="'Editar: ' . $producto->nombre"
            :breadcrumbs="[
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Productos', 'route' => 'admin.productos.index'],
                ['label' => $producto->nombre, 'route' => 'admin.productos.show', 'param' => $producto],
                ['label' => 'Editar'],
            ]"
        />

        {{-- Flash --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li><flux:text class="text-red-700 dark:text-red-400 text-sm">{{ $error }}</flux:text></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $categoriasActivas = $producto->categorias->pluck('id')->toArray();
            $floresActivas     = $producto->flores->pluck('id')->toArray();
        @endphp

        <div class="flex gap-6 items-start">

            {{-- Columna principal --}}
            <div class="flex-1 space-y-6">
                <form id="form-datos" method="POST" action="{{ route('admin.productos.update', $producto) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

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
                            <textarea
                                id="editor-detalles"
                                name="detalles"
                                class="sr-only"
                            >{{ old('detalles', $producto->detalles) }}</textarea>
                            <div id="editor-detalles-container" class="rounded-lg border border-zinc-200 dark:border-zinc-700 min-h-40"></div>
                            <flux:error name="detalles" />
                        </flux:field>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Precio lista</flux:label>
                                <flux:input type="number" name="precio_lista" value="{{ old('precio_lista', $producto->precio_lista) }}" min="0" step="0.01" placeholder="0.00" />
                                <flux:description>Precio tachado. Vacío si no aplica.</flux:description>
                                <flux:error name="precio_lista" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Precio venta *</flux:label>
                                <flux:input type="number" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" min="0" step="0.01" required />
                                <flux:error name="precio_venta" />
                            </flux:field>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                            <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Columna lateral --}}
            <div class="w-80 shrink-0 space-y-4">

                {{-- Publicación --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
                    <flux:heading size="sm">Publicación</flux:heading>
                    <flux:field>
                        <flux:label>Estado</flux:label>
                        <flux:select name="estado" form="form-datos">
                            <flux:select.option value="1" :selected="old('estado', $producto->estado) == 1">Activo</flux:select.option>
                            <flux:select.option value="0" :selected="old('estado', $producto->estado) == 0">Inactivo</flux:select.option>
                        </flux:select>
                    </flux:field>
                    <flux:field>
                        <flux:label>Destacado</flux:label>
                        <flux:select name="destacado" form="form-datos">
                            <flux:select.option value="0" :selected="old('destacado', $producto->destacado) == 0">No</flux:select.option>
                            <flux:select.option value="1" :selected="old('destacado', $producto->destacado) == 1">Sí</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                {{-- Galería --}}
                <div
                    class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4"
                    x-data="galeriaUploader({
                        uploadUrl: '{{ route('admin.productos.galeria.store', $producto) }}',
                        csrfToken: '{{ csrf_token() }}',
                        inicial: [
                            @foreach($producto->galeria as $img)
                            {
                                id: {{ $img->id }},
                                url: '{{ Storage::url($img->imagen) }}',
                                deleteUrl: '{{ route('admin.productos.galeria.destroy', [$producto, $img]) }}',
                                portada: {{ $loop->first ? 'true' : 'false' }},
                            },
                            @endforeach
                        ]
                    })"
                >
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">
                            Galería
                            <span class="text-zinc-400 font-normal text-xs ml-1">(<span x-text="imagenes.length"></span>)</span>
                        </flux:heading>
                        <span x-show="subiendo" class="text-xs text-zinc-400 animate-pulse">Subiendo…</span>
                    </div>

                    {{-- Grid de imágenes --}}
                    <div class="grid grid-cols-3 gap-2" x-show="imagenes.length > 0">
                        <template x-for="img in imagenes" :key="img.id">
                            <div class="relative group">
                                <img
                                    :src="img.url"
                                    class="w-full aspect-square object-cover rounded-lg ring-1 ring-zinc-200 dark:ring-zinc-700"
                                >
                                <div x-show="img.portada" class="absolute top-1 left-1">
                                    <span class="text-xs bg-zinc-800/70 text-white px-1.5 py-0.5 rounded">Portada</span>
                                </div>
                                <button
                                    type="button"
                                    @click="eliminar(img)"
                                    class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 text-white shadow opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <flux:icon name="x-mark" class="size-3" />
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Empty state --}}
                    <div
                        x-show="imagenes.length === 0"
                        class="flex flex-col items-center justify-center py-6 rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center"
                    >
                        <flux:icon name="photo" class="size-8 text-zinc-300 mb-1" />
                        <flux:text class="text-sm text-zinc-400">Sin imágenes aún.</flux:text>
                    </div>

                    {{-- Dropzone --}}
                    <div
                        @dragover.prevent="drag = true"
                        @dragleave.prevent="drag = false"
                        @drop.prevent="drop($event)"
                        :class="drag ? 'border-primary bg-primary/5' : 'border-zinc-300 dark:border-zinc-600 hover:border-zinc-400'"
                        class="rounded-lg border-2 border-dashed p-4 transition-colors cursor-pointer text-center"
                        @click="$refs.fileInput.click()"
                    >
                        <input
                            x-ref="fileInput"
                            type="file"
                            multiple
                            accept="image/*"
                            class="sr-only"
                            @change="subir($event.target.files)"
                        />
                        <flux:icon name="arrow-up-tray" class="size-6 text-zinc-400 mx-auto mb-1" />
                        <p class="text-xs text-zinc-500">Arrastra o haz clic · las imágenes se suben automáticamente</p>
                    </div>

                    {{-- Error de subida --}}
                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                </div>

                {{-- Categorías --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Categorías</flux:heading>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($categorias as $categoria)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <flux:checkbox
                                    name="categorias[]"
                                    value="{{ $categoria->id }}"
                                    :checked="in_array($categoria->id, old('categorias', $categoriasActivas))"
                                    form="form-datos"
                                />
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $categoria->titulo }}</span>
                            </label>
                        @endforeach
                    </div>
                    <flux:error name="categorias" />
                </div>

                {{-- Flores --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Flores incluidas</flux:heading>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($flores as $flor)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <flux:checkbox
                                    name="flores[]"
                                    value="{{ $flor->id }}"
                                    :checked="in_array($flor->id, old('flores', $floresActivas))"
                                    form="form-datos"
                                />
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $flor->nombre }}</span>
                            </label>
                        @endforeach
                    </div>
                    <flux:error name="flores" />
                </div>

                {{-- Info --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Información</flux:heading>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Slug</flux:text>
                        <p class="text-xs font-mono text-zinc-500 mt-0.5 break-all">/{{ $producto->slug }}</p>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizado</flux:text>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $producto->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                {{-- Ver en sitio --}}
                <flux:button
                    href="{{ route('productos.show', $producto) }}"
                    variant="ghost"
                    icon="arrow-top-right-on-square"
                    class="w-full"
                    target="_blank"
                >
                    Ver en el sitio
                </flux:button>

            </div>
        </div>

    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('editor-detalles-container');
        if (!container) return;

        const script = document.createElement('script');
        script.src = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js';
        script.onload = () => {
            const link = document.createElement('link');
            link.rel  = 'stylesheet';
            link.href = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css';
            document.head.appendChild(link);

            const {
                ClassicEditor,
                Essentials,
                Bold,
                Italic,
                Link,
                Paragraph,
                Heading,
                List,
                BlockQuote,
                Indent,
                IndentBlock,
                Undo,
            } = CKEDITOR;

            ClassicEditor
                .create(container, {
                    plugins: [
                        Essentials, Bold, Italic, Link, Paragraph,
                        Heading, List, BlockQuote, Indent, IndentBlock, Undo,
                    ],
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', 'blockQuote', '|',
                        'indent', 'outdent', '|',
                        'undo', 'redo',
                    ],
                    initialData: document.getElementById('editor-detalles').value,
                })
                .then(editor => {
                    const form = document.getElementById('form-datos') ?? document.querySelector('form');
                    form.addEventListener('submit', () => {
                        document.getElementById('editor-detalles').value = editor.getData();
                    });
                })
                .catch(err => console.error('CKEditor error:', err));
        };
        document.head.appendChild(script);
    });
    </script>
    @endpush

</x-layouts::app>