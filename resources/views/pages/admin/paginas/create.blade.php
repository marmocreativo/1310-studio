<x-layouts::app :title="__('Nueva página')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Nueva página"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Páginas',   'route' => 'admin.paginas.index'],
            ['label' => 'Nueva página'],
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

    <form method="POST" action="{{ route('admin.paginas.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="flex gap-6 items-start">

            {{-- Columna principal --}}
            <div class="flex-1 space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

                <flux:field>
                    <flux:label>Título</flux:label>
                    <flux:input name="titulo" value="{{ old('titulo') }}" required />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Resumen</flux:label>
                    <flux:textarea name="resumen" rows="3">{{ old('resumen') }}</flux:textarea>
                    <flux:error name="resumen" />
                </flux:field>

                <flux:field>
                    <flux:label>Contenido</flux:label>
                    <flux:textarea name="contenido" rows="12">{{ old('contenido') }}</flux:textarea>
                    <flux:error name="contenido" />
                </flux:field>

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary">Crear página</flux:button>
                    <flux:button href="{{ route('admin.paginas.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
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
                            <flux:select.option value="borrador"  :selected="old('estado', 'borrador') === 'borrador'">Borrador</flux:select.option>
                            <flux:select.option value="publicado" :selected="old('estado') === 'publicado'">Publicado</flux:select.option>
                        </flux:select>
                        <flux:error name="estado" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Categoría</flux:label>
                        <flux:select name="categoria">
                            <flux:select.option value="general" :selected="old('categoria', 'general') === 'general'">General</flux:select.option>
                            <flux:select.option value="legal"   :selected="old('categoria') === 'legal'">Legal</flux:select.option>
                        </flux:select>
                        <flux:error name="categoria" />
                    </flux:field>
                </div>

                {{-- Imagen --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Imagen principal</flux:heading>

                    <div
                        x-data="dropzone()"
                        @dragover.prevent="active = true"
                        @dragleave.prevent="active = false"
                        @drop.prevent="handleDrop($event)"
                        class="relative"
                    >
                        <label
                            :class="active ? 'border-primary bg-primary/5' : 'border-zinc-300 dark:border-zinc-600 hover:border-zinc-400'"
                            class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed p-6 cursor-pointer transition-colors"
                        >
                            <input
                                type="file"
                                name="imagen"
                                accept="image/*"
                                class="sr-only"
                                @change="handleFile($event.target.files[0])"
                            />
                            <template x-if="!preview">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <flux:icon name="photo" class="size-8 text-zinc-400" />
                                    <div>
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Arrastra o haz clic</p>
                                        <p class="text-xs text-zinc-400 mt-0.5">PNG, JPG, WEBP · Máx. 5 MB</p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="preview">
                                <div class="relative w-full">
                                    <img :src="preview" class="w-full rounded-lg object-cover aspect-video" />
                                    <p class="text-xs text-center text-zinc-400 mt-2" x-text="filename"></p>
                                </div>
                            </template>
                        </label>
                        <template x-if="preview">
                            <button
                                type="button"
                                @click.prevent="clear()"
                                class="absolute top-2 right-2 rounded-full bg-white dark:bg-zinc-800 p-1 shadow ring-1 ring-zinc-200 dark:ring-zinc-700 hover:bg-zinc-100"
                            >
                                <flux:icon name="x-mark" class="size-4 text-zinc-500" />
                            </button>
                        </template>
                    </div>

                    <flux:description>Si no subes imagen se usará la imagen por defecto.</flux:description>
                    <flux:error name="imagen" />
                </div>

            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
function dropzone(initial = null) {
    return {
        active: false, preview: initial, filename: '',
        handleDrop(e) { this.active = false; const f = e.dataTransfer.files[0]; if (f) this.handleFile(f); },
        handleFile(f) {
            if (!f || !f.type.startsWith('image/')) return;
            this.filename = f.name;
            const r = new FileReader();
            r.onload = e => { this.preview = e.target.result; };
            r.readAsDataURL(f);
        },
        clear() { this.preview = null; this.filename = ''; this.$el.querySelector('input[type=file]').value = ''; },
    }
}
</script>
@endpush

</x-layouts::app>