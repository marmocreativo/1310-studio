<x-layouts::app :title="__('Nuevo producto')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <x-admin.page-header
            titulo="Nuevo producto"
            :breadcrumbs="[
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Productos', 'route' => 'admin.productos.index'],
                ['label' => 'Nuevo producto'],
            ]"
        />

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li><flux:text class="text-red-700 dark:text-red-400 text-sm">{{ $error }}</flux:text></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="flex gap-6 items-start">

                {{-- Columna principal --}}
                <div class="flex-1 space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

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
                        <textarea id="editor-detalles" name="detalles" class="sr-only">{{ old('detalles') }}</textarea>
                        <div id="editor-detalles-container" class="rounded-lg border border-zinc-200 dark:border-zinc-700 min-h-80"></div>
                        <flux:error name="detalles" />
                    </flux:field>

                    {{-- Precios --}}
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Precio lista</flux:label>
                            <flux:input type="number" name="precio_lista" value="{{ old('precio_lista') }}" min="0" step="0.01" placeholder="0.00" />
                            <flux:description>Precio tachado. Vacío si no aplica.</flux:description>
                            <flux:error name="precio_lista" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Precio venta *</flux:label>
                            <flux:input type="number" name="precio_venta" value="{{ old('precio_venta') }}" min="0" step="0.01" required />
                            <flux:error name="precio_venta" />
                        </flux:field>
                    </div>

                    {{-- Categorías --}}
                    <flux:field>
                        <flux:label>Categorías</flux:label>
                        <div class="grid grid-cols-2 gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                            @foreach($categorias as $categoria)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="categorias[]"
                                        value="{{ $categoria->id }}"
                                        {{ in_array($categoria->id, old('categorias', [])) ? 'checked' : '' }}
                                        class="rounded border-zinc-300 text-primary focus:ring-primary"
                                    />
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $categoria->titulo }}</span>
                                </label>
                            @endforeach
                        </div>
                        <flux:error name="categorias" />
                    </flux:field>

                    {{-- Flores --}}
                    <flux:field>
                        <flux:label>Flores incluidas</flux:label>
                        <div class="grid grid-cols-2 gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 max-h-48 overflow-y-auto">
                            @foreach($flores as $flor)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="flores[]"
                                        value="{{ $flor->id }}"
                                        {{ in_array($flor->id, old('flores', [])) ? 'checked' : '' }}
                                        class="rounded border-zinc-300 text-primary focus:ring-primary"
                                    />
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $flor->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                        <flux:error name="flores" />
                    </flux:field>

                    <div class="flex gap-3 pt-2">
                        <flux:button type="submit" variant="primary">Crear producto</flux:button>
                        <flux:button href="{{ route('admin.productos.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
                    </div>
                </div>

                {{-- Columna lateral --}}
                <div class="w-72 shrink-0 space-y-4">

                    {{-- Publicación --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
                        <flux:heading size="sm">Publicación</flux:heading>

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

                    {{-- Nota imágenes --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-2">
                        <flux:heading size="sm">Imágenes</flux:heading>
                        <div class="flex items-start gap-2 text-sm text-zinc-400">
                            <flux:icon name="information-circle" class="size-4 mt-0.5 shrink-0" />
                            <span>Las imágenes se agregan desde la pantalla de edición, una vez creado el producto.</span>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>

    <style>
    .ck-editor__editable {
        min-height: 320px;
    }
    </style>

    @push('scripts')
<script>
function inicializarEditorDetalles() {
    if (typeof CKEDITOR === 'undefined') {
        console.error('CKEDITOR no se cargó desde el CDN.');
        return;
    }

    const container = document.getElementById('editor-detalles-container');
    const textarea  = document.getElementById('editor-detalles');

    if (!container || !textarea) return;
    if (container.dataset.ckInitialized) return;
    container.dataset.ckInitialized = 'true';

    const {
        ClassicEditor, Essentials, Bold, Italic, Link, Paragraph,
        Heading, List, BlockQuote, Indent, IndentBlock, Undo,
    } = CKEDITOR;

    ClassicEditor.create(container, {
        plugins: [Essentials, Bold, Italic, Link, Paragraph,
                  Heading, List, BlockQuote, Indent, IndentBlock, Undo],
        toolbar: ['heading', '|', 'bold', 'italic', 'link', '|',
                  'bulletedList', 'numberedList', 'blockQuote', '|',
                  'indent', 'outdent', '|', 'undo', 'redo'],
        initialData: textarea.value,
    }).then(editor => {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', () => {
                textarea.value = editor.getData();
            });
        }
    }).catch(err => console.error('CKEditor error:', err));
}

(function cargarCKEditor() {
    if (typeof CKEDITOR !== 'undefined') {
        inicializarEditorDetalles();
        return;
    }

    const scriptExistente = document.querySelector('script[data-ckeditor-loader]');
    if (scriptExistente) {
        let intentos = 0;
        const esperar = setInterval(() => {
            intentos++;
            if (typeof CKEDITOR !== 'undefined') {
                clearInterval(esperar);
                inicializarEditorDetalles();
            } else if (intentos > 50) {
                clearInterval(esperar);
                console.error('Tiempo de espera agotado para CKEDITOR.');
            }
        }, 100);
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js';
    script.dataset.ckeditorLoader = 'true';
    script.onload = () => {
        const link = document.createElement('link');
        link.rel  = 'stylesheet';
        link.href = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css';
        document.head.appendChild(link);
        inicializarEditorDetalles();
    };
    script.onerror = () => console.error('No se pudo cargar el script de CKEditor desde el CDN.');
    document.head.appendChild(script);
})();
</script>
@endpush
</x-layouts::app>