<x-layouts::app :title="'Editar: ' . $directorioFloral->nombre">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="'Editar: ' . $directorioFloral->nombre"
        :breadcrumbs="[
            ['label' => 'Dashboard',         'route' => 'admin.dashboard'],
            ['label' => 'Directorio Floral', 'route' => 'admin.directorio-floral.index'],
            ['label' => $directorioFloral->nombre, 'route' => 'admin.directorio-floral.show', 'param' => $directorioFloral],
            ['label' => 'Editar'],
        ]"
    />

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

    <form id="form-datos" method="POST" action="{{ route('admin.directorio-floral.update', $directorioFloral) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex gap-6 items-start">

            {{-- Columna principal --}}
            <div class="flex-1 space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Nombre *</flux:label>
                        <flux:input name="nombre" value="{{ old('nombre', $directorioFloral->nombre) }}" required />
                        <flux:error name="nombre" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Categoría</flux:label>
                        <flux:input name="categoria" value="{{ old('categoria', $directorioFloral->categoria) }}" placeholder="Ej: Tropical, Silvestre…" />
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
                    <textarea id="editor-contenido" name="contenido" class="sr-only">{{ old('contenido', $directorioFloral->contenido) }}</textarea>
                    <div id="editor-contenido-container" class="rounded-lg border border-zinc-200 dark:border-zinc-700 min-h-40"></div>
                    <flux:error name="contenido" />
                </flux:field>

                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition-opacity"
                    >
                        Guardar cambios
                    </button>
                    <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" wire:navigate>
                        Cancelar
                    </flux:button>
                </div>
            </div>

            {{-- Columna lateral --}}
            <div class="w-80 shrink-0 space-y-4">

                {{-- Publicación --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
                    <flux:heading size="sm">Publicación</flux:heading>

                    <flux:field>
                        <flux:label>Estado</flux:label>
                        <flux:select name="estado" form="form-datos">
                            <flux:select.option value="1" :selected="old('estado', $directorioFloral->estado) == '1'">Activa</flux:select.option>
                            <flux:select.option value="0" :selected="old('estado', $directorioFloral->estado) == '0'">Inactiva</flux:select.option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Orden</flux:label>
                        <flux:input type="number" name="orden" value="{{ old('orden', $directorioFloral->orden) }}" min="0" form="form-datos" />
                        <flux:description>Número menor aparece primero.</flux:description>
                        <flux:error name="orden" />
                    </flux:field>
                </div>

                {{-- Imagen principal --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Imagen principal</flux:heading>

                    @if($directorioFloral->imagen)
                        <div id="preview-container">
                            <img
                                id="preview-img"
                                src="{{ Storage::url($directorioFloral->imagen) }}"
                                alt="{{ $directorioFloral->nombre }}"
                                class="w-full rounded-lg object-cover aspect-[4/3]"
                            />
                            <p id="preview-label" class="text-xs text-center text-zinc-400 mt-2">Imagen actual</p>
                        </div>
                    @else
                        <div id="preview-container" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="w-full rounded-lg object-cover aspect-[4/3]" />
                            <p id="preview-label" class="text-xs text-center text-primary mt-2"></p>
                        </div>
                    @endif

                    <input
                        type="file"
                        name="imagen"
                        accept="image/*"
                        form="form-datos"
                        class="block w-full text-sm text-zinc-500
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                               file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                               hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300"
                        onchange="
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = e => {
                                    document.getElementById('preview-img').src = e.target.result;
                                    document.getElementById('preview-label').textContent = file.name;
                                    document.getElementById('preview-label').className = 'text-xs text-center text-primary mt-2';
                                    document.getElementById('preview-container').classList.remove('hidden');
                                };
                                reader.readAsDataURL(file);
                            }
                        "
                    />

                    <flux:description>Deja vacío para mantener la imagen actual.</flux:description>
                    <flux:error name="imagen" />
                </div>

                {{-- Galería dinámica --}}
                <div
                    class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4"
                    x-data="galeriaUploader({
                        uploadUrl: '{{ route('admin.directorio-floral.galeria.store', $directorioFloral) }}',
                        csrfToken: '{{ csrf_token() }}',
                        inicial: [
                            @foreach($directorioFloral->galeria as $img)
                            {
                                id: {{ $img->id }},
                                url: '{{ Storage::url($img->imagen) }}',
                                deleteUrl: '{{ route('admin.directorio-floral.galeria.destroy', [$directorioFloral, $img]) }}',
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

                    {{-- Grid imágenes --}}
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
                        <p class="text-xs text-zinc-500">Arrastra o haz clic · se suben automáticamente</p>
                    </div>

                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                </div>

                {{-- Info --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Información</flux:heading>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Slug</flux:text>
                        <p class="text-xs font-mono text-zinc-500 mt-0.5 break-all">/{{ $directorioFloral->slug }}</p>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-400 uppercase tracking-wide">Actualizado</flux:text>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $directorioFloral->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                {{-- Ver en sitio --}}
                <flux:button
                    href="{{ route('directorio-floral.show', $directorioFloral) }}"
                    variant="ghost"
                    icon="arrow-top-right-on-square"
                    class="w-full"
                    target="_blank"
                >
                    Ver en el sitio
                </flux:button>

            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const script = document.createElement('script');
    script.src = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js';
    script.onload = () => {
        const link = document.createElement('link');
        link.rel  = 'stylesheet';
        link.href = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css';
        document.head.appendChild(link);

        const {
            ClassicEditor, Essentials, Bold, Italic, Link, Paragraph,
            Heading, List, BlockQuote, Indent, IndentBlock, Undo,
        } = CKEDITOR;

        ClassicEditor.create(document.getElementById('editor-contenido-container'), {
            plugins: [Essentials, Bold, Italic, Link, Paragraph,
                      Heading, List, BlockQuote, Indent, IndentBlock, Undo],
            toolbar: ['heading', '|', 'bold', 'italic', 'link', '|',
                      'bulletedList', 'numberedList', 'blockQuote', '|',
                      'indent', 'outdent', '|', 'undo', 'redo'],
            initialData: document.getElementById('editor-contenido').value,
        }).then(editor => {
            document.getElementById('form-datos').addEventListener('submit', () => {
                document.getElementById('editor-contenido').value = editor.getData();
            });
        }).catch(err => console.error(err));
    };
    document.head.appendChild(script);
});
</script>
@endpush

</x-layouts::app>