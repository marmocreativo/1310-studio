<x-layouts::app :title="__('Editar Slide')">
<div class="flex h-full w-full flex-1 flex-col gap-6">

    <x-admin.page-header
        titulo="Editar Slide"
        :breadcrumbs="[
            ['label' => 'Dashboard',   'route' => 'admin.dashboard'],
            ['label' => 'Slides Hero', 'route' => 'admin.slides.index'],
            ['label' => $slide->titulo ?: 'Sin título'],
        ]"
    />

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Form de eliminación (fuera del form principal) --}}
    <form
        id="form-destroy"
        method="POST"
        action="{{ route('admin.slides.destroy', $slide) }}"
        onsubmit="return confirm('¿Eliminar este slide permanentemente?')"
        style="display:none"
    >
        @csrf
        @method('DELETE')
    </form>

    <form
        id="form-slide"
        method="POST"
        action="{{ route('admin.slides.update', $slide) }}"
        enctype="multipart/form-data"
        class="flex gap-6 items-start"
        x-data="slideForm()"
    >
        @csrf
        @method('PUT')

        {{-- ── Columna principal ── --}}
        <div class="flex-1 flex flex-col gap-6">

            {{-- Contenido --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-5">
                <flux:heading size="sm" class="text-zinc-700">Contenido</flux:heading>

                <flux:field>
                    <flux:label>Título <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                    <flux:input name="titulo" value="{{ old('titulo', $slide->titulo) }}" placeholder="Texto principal del slide" />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Caption <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                    <flux:input name="caption" value="{{ old('caption', $slide->caption) }}" placeholder="Subtítulo o etiqueta sobre el título" />
                    <flux:error name="caption" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Texto del botón</flux:label>
                        <flux:input name="texto_boton" value="{{ old('texto_boton', $slide->texto_boton) }}" placeholder="Explorar colección" />
                        <flux:error name="texto_boton" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Enlace del botón</flux:label>
                        <flux:input name="enlace_boton" value="{{ old('enlace_boton', $slide->enlace_boton) }}" placeholder="/categorias/primavera" />
                        <flux:error name="enlace_boton" />
                    </flux:field>
                </div>
            </div>

            {{-- Imagen de fondo --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Imagen de fondo</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Se recortará y escalará a <strong>1920 × 1080 px</strong>. Deja vacío para mantener la actual.</flux:text>
                </div>

                @if($slide->imagen_fondo_url)
                    <div class="relative rounded-lg overflow-hidden" x-show="!previews.fondo">
                        <img src="{{ $slide->imagen_fondo_url }}" class="w-full h-40 object-cover" alt="Fondo actual">
                        <div class="absolute inset-0 bg-black/30 flex items-end p-3">
                            <flux:badge color="zinc" size="sm">Imagen actual</flux:badge>
                        </div>
                    </div>
                @endif

                <div
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave="dragover = false"
                    x-on:drop.prevent="handleDrop($event, 'fondo')"
                    :class="dragover ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-300'"
                    class="relative rounded-lg border-2 border-dashed transition-colors"
                >
                    <input
                        type="file"
                        name="imagen_fondo"
                        accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full"
                        x-on:change="previewFile($event, 'fondo')"
                    >
                    <div class="p-6 text-center" x-show="!previews.fondo">
                        <flux:icon name="arrow-up-tray" class="size-8 mx-auto mb-2 text-zinc-300" />
                        <flux:text size="sm" class="text-zinc-500">Subir nueva imagen de fondo</flux:text>
                    </div>
                    <div x-show="previews.fondo" class="p-2">
                        <img :src="previews.fondo" class="w-full h-40 object-cover rounded-md" />
                    </div>
                </div>
                @error('imagen_fondo') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Logo --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Logo</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Opcional. Deja vacío para mantener el actual.</flux:text>
                </div>

                @if($slide->logo_url)
                    <div class="rounded-lg bg-zinc-100 p-3 flex items-center justify-center" x-show="!previews.logo">
                        <img src="{{ $slide->logo_url }}" class="h-16 object-contain" alt="Logo actual">
                    </div>
                @endif

                <div
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave="dragover = false"
                    x-on:drop.prevent="handleDrop($event, 'logo')"
                    :class="dragover ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-300'"
                    class="relative rounded-lg border-2 border-dashed transition-colors"
                >
                    <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full"
                        x-on:change="previewFile($event, 'logo')"
                    >
                    <div class="p-5 text-center" x-show="!previews.logo">
                        <flux:icon name="arrow-up-tray" class="size-7 mx-auto mb-2 text-zinc-300" />
                        <flux:text size="sm" class="text-zinc-500">Subir nuevo logo</flux:text>
                    </div>
                    <div x-show="previews.logo" class="p-2 flex justify-center">
                        <img :src="previews.logo" class="h-20 object-contain rounded-md" />
                    </div>
                </div>
                @error('logo') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Video (solo tipo video) --}}
            <div x-show="tipo === 'video'" class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Video</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Sube un archivo MP4 <strong>o</strong> ingresa un enlace de YouTube.</flux:text>
                </div>

                @if($slide->video_url)
                    <div class="rounded-lg bg-zinc-100 p-3 flex items-center gap-3">
                        <flux:icon name="film" class="size-5 text-zinc-400 shrink-0" />
                        <flux:text size="sm" class="text-zinc-500 truncate">Video MP4 actual</flux:text>
                    </div>
                @elseif($slide->video_youtube)
                    <div class="rounded-lg bg-zinc-100 p-3 flex items-center gap-3">
                        <flux:icon name="play-circle" class="size-5 text-zinc-400 shrink-0" />
                        <flux:text size="sm" class="text-zinc-500 truncate">{{ $slide->video_youtube }}</flux:text>
                    </div>
                @endif

                <div x-data="{ videoTab: '{{ $slide->video_youtube ? 'youtube' : 'mp4' }}' }" class="flex flex-col gap-4">
                    <div class="flex gap-2">
                        <button type="button" x-on:click="videoTab = 'mp4'"
                            :class="videoTab === 'mp4' ? 'bg-[#927F64] text-white' : 'bg-zinc-100 text-zinc-600'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">Archivo MP4</button>
                        <button type="button" x-on:click="videoTab = 'youtube'"
                            :class="videoTab === 'youtube' ? 'bg-[#927F64] text-white' : 'bg-zinc-100 text-zinc-600'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">YouTube</button>
                    </div>

                    <div x-show="videoTab === 'mp4'">
                        <flux:field>
                            <flux:label>Nuevo archivo MP4 <flux:badge size="sm" color="zinc">Máx. 100 MB</flux:badge></flux:label>
                            <input type="file" name="video" accept="video/mp4"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 transition-colors">
                            <flux:error name="video" />
                        </flux:field>
                    </div>

                    <div x-show="videoTab === 'youtube'">
                        <flux:field>
                            <flux:label>URL de YouTube</flux:label>
                            <flux:input name="video_youtube" value="{{ old('video_youtube', $slide->video_youtube) }}" placeholder="https://www.youtube.com/watch?v=..." />
                            <flux:error name="video_youtube" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- Overlay (solo tipo capas) --}}
            <div x-show="tipo === 'capas'" class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-5">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Imagen overlay</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Se recortará a <strong>1080 × 1080 px</strong>. Deja vacío para mantener la actual.</flux:text>
                </div>

                @if($slide->imagen_overlay_url)
                    <div class="rounded-lg bg-zinc-50 p-2 flex justify-center" x-show="!previews.overlay">
                        <img src="{{ $slide->imagen_overlay_url }}" class="h-40 object-contain rounded-md" alt="Overlay actual">
                    </div>
                @endif

                <div
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave="dragover = false"
                    x-on:drop.prevent="handleDrop($event, 'overlay')"
                    :class="dragover ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-300'"
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
                        <flux:text size="sm" class="text-zinc-500">Subir nueva imagen overlay</flux:text>
                    </div>
                    <div x-show="previews.overlay" class="p-2 flex justify-center">
                        <img :src="previews.overlay" class="h-48 object-contain rounded-md" />
                    </div>
                </div>
                @error('imagen_overlay') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- ── Sidebar ── --}}
        <div class="w-72 flex flex-col gap-4">

            {{-- Publicación --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 flex flex-col gap-4">
                <flux:heading size="sm" class="text-zinc-700">Publicación</flux:heading>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado" form="form-slide">
                        <flux:select.option value="1" :selected="old('estado', $slide->estado ? '1' : '0') === '1'">Activo</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado', $slide->estado ? '1' : '0') === '0'">Inactivo</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Orden</flux:label>
                    <flux:input type="number" name="orden" value="{{ old('orden', $slide->orden) }}" min="0" form="form-slide" />
                    <flux:description>Número de posición (menor = primero).</flux:description>
                </flux:field>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#927F64] hover:bg-[#7d6b52] text-white text-sm font-medium px-4 py-2.5 transition-colors"
                >
                    Guardar cambios
                </button>

                <flux:button href="{{ route('admin.slides.index') }}" variant="ghost" size="sm" wire:navigate class="w-full">
                    Cancelar
                </flux:button>
            </div>

            {{-- Tipo --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 flex flex-col gap-4">
                <flux:heading size="sm" class="text-zinc-700">Tipo de slide</flux:heading>

                <div class="flex flex-col gap-2">
                    @foreach($tipos as $t)
                        <label
                            :class="tipo === '{{ $t->value }}' ? 'border-[#927F64] bg-[#927F64]/5' : 'border-zinc-200'"
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
                                <p class="text-sm font-medium text-zinc-800">{{ ucfirst($t->value) }}</p>
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

            {{-- Zona de peligro --}}
            <div class="rounded-xl border border-red-100 bg-white p-5 flex flex-col gap-3">
                <flux:heading size="sm" class="text-red-600">Zona de peligro</flux:heading>
                <flux:text size="sm" class="text-zinc-500">Esta acción eliminará el slide y todos sus archivos de forma permanente.</flux:text>
                <button
                    type="button"
                    onclick="document.getElementById('form-destroy').submit()"
                    class="w-full rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium px-4 py-2 transition-colors"
                >
                    Eliminar slide
                </button>
            </div>

        </div>
    </form>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('slideForm', () => ({
        tipo: '{{ old('tipo', $slide->tipo->value) }}',
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