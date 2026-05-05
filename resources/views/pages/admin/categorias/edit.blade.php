<x-layouts::app :title="__('Editar categoría')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        :titulo="'Editar: ' . $categoria->titulo"
        :breadcrumbs="[
            ['label' => 'Dashboard',         'route' => 'admin.dashboard'],
            ['label' => 'Categorías',        'route' => 'admin.categorias.index'],
            ['label' => $categoria->titulo,  'route' => 'admin.categorias.show', 'param' => $categoria],
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

    <form method="POST" action="{{ route('admin.categorias.update', $categoria) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex gap-6 items-start">

            {{-- Columna principal --}}
            <div class="flex-1 space-y-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">

                <flux:field>
                    <flux:label>Título</flux:label>
                    <flux:input name="titulo" value="{{ old('titulo', $categoria->titulo) }}" required />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Categoría padre</flux:label>
                    <flux:select name="id_padre">
                        <flux:select.option value="">Sin categoría padre (raíz)</flux:select.option>
                        @foreach($padres as $padre)
                            <flux:select.option value="{{ $padre->id }}" :selected="old('id_padre', $categoria->id_padre) == $padre->id">
                                {{ $padre->titulo }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="id_padre" />
                </flux:field>

                <flux:field>
                    <flux:label>Resumen</flux:label>
                    <flux:textarea name="resumen" rows="4">{{ old('resumen', $categoria->resumen) }}</flux:textarea>
                    <flux:error name="resumen" />
                </flux:field>

                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition-opacity"
                    >
                        Guardar cambios
                    </button>
                    <flux:button href="{{ route('admin.categorias.index') }}" variant="ghost" wire:navigate>
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
                            <flux:select.option value="borrador"  :selected="old('estado', $categoria->estado) === 'borrador'">Borrador</flux:select.option>
                            <flux:select.option value="publicado" :selected="old('estado', $categoria->estado) === 'publicado'">Publicado</flux:select.option>
                        </flux:select>
                        <flux:error name="estado" />
                    </flux:field>
                </div>

                {{-- Imagen --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                    <flux:heading size="sm">Imagen</flux:heading>

                    <div id="preview-container">
                        <img
                            id="preview-img"
                            src="{{ $categoria->imagen_url }}"
                            alt="{{ $categoria->titulo }}"
                            class="w-full rounded-lg object-cover aspect-[4/3]"
                        />
                        <p id="preview-label" class="text-xs text-center text-zinc-400 mt-2">
                            Imagen actual
                        </p>
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
                                    document.getElementById('preview-label').className = 'text-xs text-center text-primary mt-2';
                                };
                                reader.readAsDataURL(file);
                            }
                        "
                    />

                    <flux:description>Deja vacío para mantener la imagen actual.</flux:description>
                    <flux:error name="imagen" />
                </div>

            </div>
        </div>
    </form>

</div>
</x-layouts::app>