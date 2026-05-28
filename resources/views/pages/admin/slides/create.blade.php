<x-layouts::app :title="__('Nuevo Slide')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Nuevo Slide"
        :breadcrumbs="[
            ['label' => 'Dashboard',    'route' => 'admin.dashboard'],
            ['label' => 'Slides Hero',  'route' => 'admin.slides.index'],
            ['label' => 'Nuevo'],
        ]"
    />

    <form
        id="form-slide"
        method="POST"
        action="{{ route('admin.slides.store') }}"
        enctype="multipart/form-data"
        class="flex gap-6 items-start"
        x-data="slideForm()"
    >
        @csrf

        {{-- ── Columna principal ── --}}
        <div class="flex-1 flex flex-col gap-6">

            {{-- Contenido del slide --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 flex flex-col gap-5">
                <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Contenido</flux:heading>

                <flux:field>
                    <flux:label>Título <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                    <flux:input name="titulo" value="{{ old('titulo') }}" placeholder="Texto principal del slide" />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Caption <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                    <flux:input name="caption" value="{{ old('caption') }}" placeholder="Subtítulo o etiqueta sobre el título" />
                    <flux:error name="caption" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Texto del botón <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                        <flux:input name="texto_boton" value="{{ old('texto_boton') }}" placeholder="Explorar colección" />
                        <flux:error name="texto_boton" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Enlace del botón <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                        <flux:input name="enlace_boton" value="{{ old('enlace_boton') }}" placeholder="/categorias/primavera" />
                        <flux:error name="enlace_boton" />
                    </flux:field>
                </div>
            </div>

            {{-- Imagen de fondo (todos los tipos) --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Imagen de fondo</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Requerida. Se recortará y escalará a <strong>1920 × 1080 px</strong> (16:9). Máx. 10 MB.</flux:text>
                </div>

                <div
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave="dragover = false"
                    x-on:drop.prevent="handleDrop($event, 'fondo')"
                    :class="dragover ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-300 dark:border-zinc-600'"
                    class="relative rounded-lg border-2 border-dashed transition-colors"
                >
                    <input
                        type="file"
                        name="imagen_fondo"
                        accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full"
                        x-on:change="previewFile($event, 'fondo')"
                    >
                    <div class="p-8 text-center" x-show="!previews.fondo">
                        <flux:icon name="photo" class="size-10 mx-auto mb-2 text-zinc-300" />
                        <flux:text size="sm" class="text-zinc-500">Arrastra una imagen o haz clic para seleccionar</flux:text>
                    </div>
                    <div x-show="previews.fondo" class="p-2">
                        <img :src="previews.fondo" class="w-full h-48 object-cover rounded-md" />
                    </div>
                </div>
                @error('imagen_fondo')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Logo --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Logo</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Opcional. Se escalará a máx. 600 px de ancho. Usa PNG con fondo transparente.</flux:text>
                </div>

                <div
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave="dragover = false"
                    x-on:drop.prevent="handleDrop($event, 'logo')"
                    :class="dragover ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-300 dark:border-zinc-600'"
                    class="relative rounded-lg border-2 border-dashed transition-colors"
                >
                    <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full"
                        x-on:change="previewFile($event, 'logo')"
                    >
                    <div class="p-6 text-center" x-show="!previews.logo">
                        <flux:icon name="photo" class="size-8 mx-auto mb-2 text-zinc-300" />
                        <flux:text size="sm" class="text-zinc-500">Seleccionar logo</flux:text>
                    </div>
                    <div x-show="previews.logo" class="p-2 flex justify-center">
                        <img :src="previews.logo" class="h-24 object-contain rounded-md" />
                    </div>
                </div>
                @error('logo')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Video (solo tipo video) --}}
            <div x-show="tipo === 'video'" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Video</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Sube un archivo MP4 <strong>o</strong> ingresa un enlace de YouTube. Solo uno de los dos.</flux:text>
                </div>

                {{-- Tabs mp4 / youtube --}}
                <div x-data="{ videoTab: 'mp4' }" class="flex flex-col gap-4">
                    <div class="flex gap-2">
                        <button
                            type="button"
                            x-on:click="videoTab = 'mp4'"
                            :class="videoTab === 'mp4' ? 'bg-[#927F64] text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors"
                        >Archivo MP4</button>
                        <button
                            type="button"
                            x-on:click="videoTab = 'youtube'"
                            :class="videoTab === 'youtube' ? 'bg-[#927F64] text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors"
                        >YouTube</button>
                    </div>

                    <div x-show="videoTab === 'mp4'">
                        <flux:field>
                            <flux:label>Archivo de video <flux:badge size="sm" color="zinc">MP4, máx. 100 MB</flux:badge></flux:label>
                            <input
                                type="file"
                                name="video"
                                accept="video/mp4"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 dark:file:bg-zinc-800 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 transition-colors"
                            >
                            <flux:error name="video" />
                        </flux:field>
                    </div>

                    <div x-show="videoTab === 'youtube'">
                        <flux:field>
                            <flux:label>URL de YouTube</flux:label>
                            <flux:input name="video_youtube" value="{{ old('video_youtube') }}" placeholder="https://www.youtube.com/watch?v=..." />
                            <flux:error name="video_youtube" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- Overlay (solo tipo capas) --}}
            <div x-show="tipo === 'capas'" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Imagen overlay</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Se recortará a <strong>1080 × 1080 px</strong> (1:1). Usa PNG con transparencia. Máx. 10 MB.</flux:text>
                </div>

                <div
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave="dragover = false"
                    x-on:drop.prevent="handleDrop($event, 'overlay')"
                    :class="dragover ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-300 dark:border-zinc-600'"
                    class="relative rounded-lg border-2 border-dashed transition-colors"
                >
                    <input
                        type="file"
                        name="imagen_overlay"
                        accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full"
                        x-on:change="previewFile($event, 'overlay')"
                    >
                    <div class="p-8 text-center" x-show="!previews.overlay">
                        <flux:icon name="square-2-stack" class="size-10 mx-auto mb-2 text-zinc-300" />
                        <flux:text size="sm" class="text-zinc-500">Arrastra la imagen overlay o haz clic para seleccionar</flux:text>
                    </div>
                    <div x-show="previews.overlay" class="p-2 flex justify-center">
                        <img :src="previews.overlay" class="h-48 object-contain rounded-md" />
                    </div>
                </div>
                @error('imagen_overlay')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- ── Sidebar ── --}}
        <div class="w-72 flex flex-col gap-4">

            {{-- Publicación --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 flex flex-col gap-4">
                <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Publicación</flux:heading>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado" form="form-slide">
                        <flux:select.option value="1" :selected="old('estado', '1') === '1'">Activo</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado') === '0'">Inactivo</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Orden</flux:label>
                    <flux:input type="number" name="orden" value="{{ old('orden', 0) }}" min="0" form="form-slide" />
                    <flux:description>Número de posición en el slider (menor = primero).</flux:description>
                </flux:field>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#927F64] hover:bg-[#7d6b52] text-white text-sm font-medium px-4 py-2.5 transition-colors"
                >
                    Crear slide
                </button>

                <flux:button href="{{ route('admin.slides.index') }}" variant="ghost" size="sm" wire:navigate class="w-full">
                    Cancelar
                </flux:button>
            </div>

            {{-- Tipo --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 flex flex-col gap-4">
                <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Tipo de slide</flux:heading>

                <div class="flex flex-col gap-2">
                    @foreach($tipos as $t)
                        <label
                            :class="tipo === '{{ $t->value }}' ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-200 dark:border-zinc-700'"
                            class="flex items-start gap-3 rounded-lg border p-3 cursor-pointer transition-colors"
                        >
                            <input
                                type="radio"
                                name="tipo"
                                value="{{ $t->value }}"
                                x-model="tipo"
                                form="form-slide"
                                class="mt-0.5 accent-[#927F64]"
                            >
                            <div>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ ucfirst($t->value) }}</p>
                                <p class="text-xs text-zinc-400 mt-0.5">
                                    @if($t->value === 'imagen') Fondo estático con texto
                                    @elseif($t->value === 'video') Fondo en video o YouTube
                                    @else Fondo + imagen en primer plano
                                    @endif
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

    </form>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('slideForm', () => ({
        tipo: '{{ old('tipo', 'imagen') }}',
        dragover: false,
        previews: { fondo: null, logo: null, overlay: null },

        previewFile(event, key) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => { this.previews[key] = e.target.result; };
            reader.readAsDataURL(file);
        },

        handleDrop(event, key) {
            this.dragover = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            // Asignar al input correcto
            const inputMap = { fondo: 'imagen_fondo', logo: 'logo', overlay: 'imagen_overlay' };
            const input = document.querySelector(`input[name="${inputMap[key]}"]`);
            if (input) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }
            const reader = new FileReader();
            reader.onload = (e) => { this.previews[key] = e.target.result; };
            reader.readAsDataURL(file);
        },
    }));
});
</script>
</x-layouts::app>