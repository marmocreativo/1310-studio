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
        x-data="slideForm({
            tipo:       @js(old('tipo', $slide->tipo->value)),
            titulo:     @js(old('titulo', $slide->titulo)),
            caption:    @js(old('caption', $slide->caption)),
            textoBoton: @js(old('texto_boton', $slide->texto_boton)),
        })"
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
                    <flux:input name="titulo" x-model="campos.titulo" placeholder="Texto principal del slide" />
                    <flux:error name="titulo" />
                </flux:field>

                <flux:field>
                    <flux:label>Caption <flux:badge size="sm" color="zinc">Opcional</flux:badge></flux:label>
                    <flux:input name="caption" x-model="campos.caption" placeholder="Subtítulo o etiqueta sobre el título" />
                    <flux:error name="caption" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Texto del botón</flux:label>
                        <flux:input name="texto_boton" x-model="campos.texto_boton" placeholder="Explorar colección" />
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
            <div class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-3">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Imagen de fondo</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Se recortará y escalará a <strong>1920 × 1080 px</strong>. Deja vacío para mantener la actual.</flux:text>
                </div>

                <input
                    type="file"
                    name="imagen_fondo"
                    accept="image/*"
                    x-on:change="previewFile($event, 'fondo')"
                    class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 transition-colors cursor-pointer"
                >
                @error('imagen_fondo') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Logo --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-3">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Logo</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Opcional. Deja vacío para mantener el actual.</flux:text>
                </div>

                <input
                    type="file"
                    name="logo"
                    accept="image/*"
                    x-on:change="previewFile($event, 'logo')"
                    class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 transition-colors cursor-pointer"
                >
                @error('logo') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Video (solo tipo video) --}}
            <div x-show="tipo === 'video'" x-cloak class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-5">
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
                                x-on:change="previewFile($event, 'video')"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 transition-colors cursor-pointer">
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
            <div x-show="tipo === 'capas'" x-cloak class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-3">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Imagen overlay</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Se recortará a <strong>1080 × 1080 px</strong>. Deja vacío para mantener la actual.</flux:text>
                </div>

                <input
                    type="file"
                    name="imagen_overlay"
                    accept="image/*"
                    x-on:change="previewFile($event, 'overlay')"
                    class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 transition-colors cursor-pointer"
                >
                @error('imagen_overlay') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- ── Vista previa del hero ── --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 flex flex-col gap-4">
                <div>
                    <flux:heading size="sm" class="text-zinc-700">Vista previa</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">Así se verá el slide en el sitio. Usa los archivos nuevos si seleccionaste alguno.</flux:text>
                </div>

                <div class="relative w-full aspect-video rounded-lg overflow-hidden bg-zinc-900">

                    {{-- Fondo --}}
                    <template x-if="tipo !== 'video'">
                        <template x-if="previews.fondo || {{ $slide->imagen_fondo_url ? 'true' : 'false' }}">
                            <img :src="previews.fondo || '{{ $slide->imagen_fondo_url }}'" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                    </template>

                    <template x-if="tipo === 'video'">
                        <template x-if="previews.video">
                            <video :src="previews.video" class="absolute inset-0 w-full h-full object-cover" muted autoplay loop playsinline></video>
                        </template>
                    </template>
                    <template x-if="tipo === 'video' && !previews.video">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <flux:icon name="film" class="size-8 text-zinc-600" />
                        </div>
                    </template>

                    {{-- Overlay oscuro --}}
                    <div class="absolute inset-0 bg-black/25 z-10"></div>

                    {{-- Copy centrado --}}
                    <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center gap-2 px-4">
                        <template x-if="previews.logo || {{ $slide->logo_url ? 'true' : 'false' }}">
                            <img :src="previews.logo || '{{ $slide->logo_url }}'" class="w-[15%] max-w-[60px] opacity-90 select-none">
                        </template>

                        <p x-show="campos.caption" x-text="campos.caption"
                           class="font-body text-white/90 text-[7px] tracking-widest uppercase font-light"
                           style="text-shadow: 0 1px 2px rgba(0,0,0,0.3)"></p>

                        <p x-show="campos.titulo" x-text="campos.titulo"
                           class="font-serif font-light italic text-white leading-tight text-lg"
                           style="text-shadow: 0 1px 2px rgba(0,0,0,0.3)"></p>

                        <span x-show="campos.texto_boton"
                              x-text="campos.texto_boton"
                              class="inline-block bg-primary text-on-primary px-3 py-1.5 text-[6px] tracking-[0.2em] uppercase mt-1"></span>
                    </div>

                    {{-- Overlay de imagen (tipo capas) --}}
                    <template x-if="tipo === 'capas'">
                        <template x-if="previews.overlay || {{ $slide->imagen_overlay_url ? 'true' : 'false' }}">
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-30 h-3/4 aspect-square pointer-events-none">
                                <img :src="previews.overlay || '{{ $slide->imagen_overlay_url }}'" class="w-full h-full object-cover object-top">
                            </div>
                        </template>
                    </template>

                </div>

                <p class="text-[11px] text-zinc-400">
                    <span x-show="previews.fondo || previews.logo || previews.overlay || previews.video" class="inline-flex items-center gap-1 text-[#927F64] font-medium">
                        <flux:icon name="check-circle" class="size-3.5" /> Mostrando archivo(s) nuevo(s)
                    </span>
                    <span x-show="!(previews.fondo || previews.logo || previews.overlay || previews.video)">
                        Mostrando archivos actuales guardados.
                    </span>
                </p>
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

<style>
[x-cloak] { display: none !important; }
</style>

</x-layouts::app>