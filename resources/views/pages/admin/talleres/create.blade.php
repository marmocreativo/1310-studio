<x-layouts::app :title="__('Nuevo taller')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Nuevo taller"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Talleres',  'route' => 'admin.talleres.index'],
            ['label' => 'Nuevo taller'],
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

    <form method="POST" action="{{ route('admin.talleres.store') }}" enctype="multipart/form-data">
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
                    <flux:label>Detalles</flux:label>
                    <textarea name="detalles" id="detalles" rows="8"
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-sm p-3"
                        placeholder="Descripción, temario, requisitos…">{{ old('detalles') }}</textarea>
                    <flux:error name="detalles" />
                </flux:field>

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary">Crear taller</flux:button>
                    <flux:button href="{{ route('admin.talleres.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
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
                        <flux:label>Fecha y hora</flux:label>
                        <flux:input type="datetime-local" name="fecha" value="{{ old('fecha') }}" />
                        <flux:description>Deja vacío si aún no está definida.</flux:description>
                        <flux:error name="fecha" />
                    </flux:field>
                </div>

                {{-- Imagen --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Imagen</flux:heading>

                    <div x-data="{
                        preview: null,
                        handleFile(f) {
                            if (!f || !f.type.startsWith('image/')) return;
                            const r = new FileReader();
                            r.onload = e => { this.preview = e.target.result; };
                            r.readAsDataURL(f);
                        }
                    }">
                        <input
                            type="file"
                            name="imagen"
                            accept="image/*"
                            class="w-full text-sm text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-600 dark:file:text-zinc-300 hover:file:bg-zinc-200 cursor-pointer"
                            @change="handleFile($event.target.files[0])"
                        />
                        <template x-if="preview">
                            <img :src="preview" class="mt-3 w-full rounded-lg object-cover aspect-video" />
                        </template>
                    </div>

                    <flux:error name="imagen" />
                </div>

            </div>
        </div>
    </form>

</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#detalles'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|',
                      'bulletedList', 'numberedList', '|', 'link', 'blockQuote', '|',
                      'undo', 'redo'],
        })
        .catch(error => console.error(error));
</script>
@endpush

</x-layouts::app>