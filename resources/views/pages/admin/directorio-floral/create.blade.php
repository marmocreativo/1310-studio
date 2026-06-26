<x-layouts::app :title="__('Nueva flor')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Nueva flor"
        :breadcrumbs="[
            ['label' => 'Dashboard',         'route' => 'admin.dashboard'],
            ['label' => 'Directorio Floral', 'route' => 'admin.directorio-floral.index'],
            ['label' => 'Nueva flor'],
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

    <form method="POST" action="{{ route('admin.directorio-floral.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="flex gap-6 items-start">

            {{-- Columna principal --}}
            <div class="flex-1 space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Nombre *</flux:label>
                        <flux:input name="nombre" value="{{ old('nombre') }}" required />
                        <flux:error name="nombre" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Categoría</flux:label>
                        <flux:input name="categoria" value="{{ old('categoria') }}" placeholder="Ej: Tropical, Silvestre…" />
                        <flux:error name="categoria" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Descripción corta</flux:label>
                    <flux:textarea name="descripcion" rows="3" placeholder="Breve descripción de la flor…">{{ old('descripcion') }}</flux:textarea>
                    <flux:error name="descripcion" />
                </flux:field>

                <flux:field>
                    <flux:label>Contenido</flux:label>
                    <textarea id="editor-contenido" name="contenido" class="sr-only">{{ old('contenido') }}</textarea>
                    <div id="editor-contenido-container" class="rounded-lg border border-zinc-200 dark:border-zinc-700 min-h-80"></div>
                    <flux:error name="contenido" />
                </flux:field>

                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition-opacity"
                    >
                        Crear flor
                    </button>
                    <flux:button href="{{ route('admin.directorio-floral.index') }}" variant="ghost" wire:navigate>
                        Cancelar
                    </flux:button>
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
                            <flux:select.option value="1" :selected="old('estado', '1') === '1'">Activa</flux:select.option>
                            <flux:select.option value="0" :selected="old('estado') === '0'">Inactiva</flux:select.option>
                        </flux:select>
                        <flux:error name="estado" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Orden</flux:label>
                        <flux:input type="number" name="orden" value="{{ old('orden', 0) }}" min="0" />
                        <flux:description>Número menor aparece primero.</flux:description>
                        <flux:error name="orden" />
                    </flux:field>
                </div>

                {{-- Imagen --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Imagen principal</flux:heading>

                    <div id="preview-container" class="hidden">
                        <img id="preview-img" src="" alt="Preview" class="w-full rounded-lg object-cover aspect-[4/3]" />
                        <p id="preview-label" class="text-xs text-center text-primary mt-2"></p>
                    </div>

                    <input
                        type="file"
                        name="imagen"
                        accept="image/*"
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
                                    document.getElementById('preview-container').classList.remove('hidden');
                                };
                                reader.readAsDataURL(file);
                            } else {
                                document.getElementById('preview-container').classList.add('hidden');
                            }
                        "
                    />

                    <flux:description>Máx. 2 MB · PNG, JPG, WEBP.</flux:description>
                    <flux:error name="imagen" />
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
            document.querySelector('form').addEventListener('submit', () => {
                document.getElementById('editor-contenido').value = editor.getData();
            });
        }).catch(err => console.error(err));
    };
    document.head.appendChild(script);
});
</script>
@endpush

</x-layouts::app>