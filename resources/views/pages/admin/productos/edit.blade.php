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

    {{-- Contenedor principal con tabs --}}
    <div x-data="{ tab: 'datos' }">

        {{-- Tab nav --}}
        <div class="border-b border-zinc-200 dark:border-zinc-700 mb-6">
            <nav class="flex gap-1">
                <button type="button"
                    @click="tab = 'datos'"
                    :class="tab === 'datos'
                        ? 'border-b-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                        : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors">
                    Datos
                </button>
                <button type="button"
                    @click="tab = 'galeria'"
                    :class="tab === 'galeria'
                        ? 'border-b-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                        : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors">
                    Galería
                </button>
                <button type="button"
                    @click="tab = 'tipos'"
                    :class="tab === 'tipos'
                        ? 'border-b-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                        : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors">
                    Tipos y opciones
                </button>
                <button type="button"
                    @click="tab = 'combinaciones'"
                    :class="tab === 'combinaciones'
                        ? 'border-b-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                        : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors">
                    Combinaciones
                </button>
            </nav>
        </div>

        {{-- ══════════════════════════════════════
             TAB: DATOS
             ══════════════════════════════════════ --}}
        <div x-show="tab === 'datos'" x-cloak>
            <div class="flex gap-6 items-start">

                {{-- Columna principal --}}
                <div class="flex-1">
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
                                <textarea id="editor-detalles" name="detalles" class="sr-only">{{ old('detalles', $producto->detalles) }}</textarea>
                                <div id="editor-detalles-container" class="rounded-lg border border-zinc-200 dark:border-zinc-700 min-h-80"></div>
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
                                <button type="submit" form="form-datos" class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                                    Guardar cambios
                                </button>
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

                    {{-- Categorías --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                        <flux:heading size="sm">Categorías</flux:heading>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($categorias as $categoria)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="categorias[]"
                                        value="{{ $categoria->id }}"
                                        {{ in_array($categoria->id, old('categorias', $categoriasActivas)) ? 'checked' : '' }}
                                        form="form-datos"
                                        class="rounded border-zinc-300 text-primary focus:ring-primary"
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
                                    <input
                                        type="checkbox"
                                        name="flores[]"
                                        value="{{ $flor->id }}"
                                        {{ in_array($flor->id, old('flores', $floresActivas)) ? 'checked' : '' }}
                                        form="form-datos"
                                        class="rounded border-zinc-300 text-primary focus:ring-primary"
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

        {{-- ══════════════════════════════════════
             TAB: GALERÍA
             ══════════════════════════════════════ --}}
        <div x-show="tab === 'galeria'" x-cloak>
            <div class="max-w-2xl">
                <div
                    class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-5"
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
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3" x-show="imagenes.length > 0">
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
                        class="flex flex-col items-center justify-center py-12 rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center"
                    >
                        <flux:icon name="photo" class="size-10 text-zinc-300 mb-2" />
                        <flux:text class="text-sm text-zinc-400">Sin imágenes aún.</flux:text>
                    </div>

                    {{-- Dropzone --}}
                    <div
                        @dragover.prevent="drag = true"
                        @dragleave.prevent="drag = false"
                        @drop.prevent="drop($event)"
                        :class="drag ? 'border-zinc-500 bg-zinc-50 dark:bg-zinc-800' : 'border-zinc-300 dark:border-zinc-600 hover:border-zinc-400'"
                        class="rounded-lg border-2 border-dashed p-6 transition-colors cursor-pointer text-center"
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
                        <flux:icon name="arrow-up-tray" class="size-7 text-zinc-400 mx-auto mb-2" />
                        <p class="text-sm text-zinc-500">Arrastra imágenes aquí o haz clic para seleccionar</p>
                        <p class="text-xs text-zinc-400 mt-1">Las imágenes se suben automáticamente</p>
                    </div>

                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             TAB: TIPOS Y OPCIONES  +  COMBINACIONES
             (comparten el mismo Alpine component)
             ══════════════════════════════════════ --}}
        <div
            x-show="tab === 'tipos' || tab === 'combinaciones'"
            x-cloak
            x-data="variacionesManager({
                urlIndex:      '{{ route('admin.productos.variaciones.index',   $producto) }}',
                urlTipoStore:  '{{ route('admin.productos.variaciones.tipos.store',   $producto) }}',
                urlSkuStore:   '{{ route('admin.productos.variaciones.skus.store',    $producto) }}',
                urlSkuGenerar: '{{ route('admin.productos.variaciones.skus.generar',  $producto) }}',
                urlDefaults:   '{{ route('admin.variaciones-defaults.index') }}',
                csrfToken:     '{{ csrf_token() }}',
            })"
            x-init="init()"
            class="space-y-6"
        >
            {{-- Loading --}}
            <div x-show="cargando" class="flex items-center gap-2 py-12 justify-center text-zinc-400">
                <svg class="animate-spin size-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span class="text-sm">Cargando variaciones…</span>
            </div>

            {{-- Error global --}}
            <div
                x-show="errorGlobal"
                x-text="errorGlobal"
                class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400"
            ></div>

            {{-- ── TIPOS Y OPCIONES ── --}}
            <div x-show="!cargando && $root.closest('[x-data]')" style="display:none"
                x-effect="$el.style.display = (!cargando && tab === 'tipos') ? 'block' : 'none'">
                <div class="space-y-4">

                    {{-- Importar desde defaults --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <flux:heading size="sm">Importar del catálogo</flux:heading>
                            <button
                                type="button"
                                @click="mostrarDefaults = !mostrarDefaults"
                                class="text-xs text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 underline"
                            >
                                <span x-text="mostrarDefaults ? 'Ocultar' : 'Ver catálogo'"></span>
                            </button>
                        </div>
                        <div x-show="mostrarDefaults" class="space-y-2">
                            <template x-if="defaults.length === 0">
                                <flux:text class="text-sm text-zinc-400">El catálogo está vacío.</flux:text>
                            </template>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template x-for="def in defaults" :key="def.id">
                                    <button
                                        type="button"
                                        @click="importarDefault(def)"
                                        class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm text-left hover:border-zinc-400 transition-colors"
                                    >
                                        <flux:icon name="plus" class="size-3.5 text-zinc-400 shrink-0" />
                                        <span x-text="def.nombre" class="truncate"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Agregar tipo manual --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-3">
                        <flux:heading size="sm">Agregar tipo personalizado</flux:heading>
                        <div class="flex gap-2">
                            <flux:input
                                x-model="nuevoTipo"
                                placeholder="Ej. Tamaño del arreglo"
                                class="flex-1"
                                @keydown.enter.prevent="agregarTipo()"
                            />
                            <button
                                type="button"
                                @click="agregarTipo()"
                                :disabled="!nuevoTipo.trim() || guardando"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 text-sm font-medium disabled:opacity-40 hover:opacity-90 transition-opacity"
                            >
                                <flux:icon name="plus" class="size-4" />
                                Agregar
                            </button>
                        </div>
                        <p x-show="errorTipo" x-text="errorTipo" class="text-xs text-red-500"></p>
                    </div>

                    {{-- Empty state tipos --}}
                    <template x-if="tipos.length === 0">
                        <div class="flex flex-col items-center justify-center py-12 rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                            <flux:icon name="tag" class="size-10 text-zinc-300 mb-2" />
                            <flux:text class="text-zinc-400 text-sm">Sin tipos de variación. Agrega uno arriba.</flux:text>
                        </div>
                    </template>

                    {{-- Lista de tipos --}}
                    <template x-for="tipo in tipos" :key="tipo.id">
                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">

                            {{-- Header tipo --}}
                            <div class="flex items-center gap-3 px-5 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                                <flux:icon name="bars-3" class="size-4 text-zinc-400 shrink-0" />

                                <div class="flex-1 flex items-center gap-2">
                                    <template x-if="!tipo._editando">
                                        <span class="font-medium text-sm text-zinc-800 dark:text-zinc-200" x-text="tipo.nombre"></span>
                                    </template>
                                    <template x-if="tipo._editando">
                                        <input
                                            type="text"
                                            x-model="tipo._nombreTemp"
                                            @keydown.enter="guardarNombreTipo(tipo)"
                                            @keydown.escape="tipo._editando = false"
                                            class="flex-1 rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-400"
                                        />
                                    </template>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <template x-if="!tipo._editando">
                                        <button type="button"
                                            @click="tipo._editando = true; tipo._nombreTemp = tipo.nombre"
                                            class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 transition-colors">
                                            <flux:icon name="pencil" class="size-3.5" />
                                        </button>
                                    </template>
                                    <template x-if="tipo._editando">
                                        <button type="button"
                                            @click="guardarNombreTipo(tipo)"
                                            class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-green-500 transition-colors">
                                            <flux:icon name="check" class="size-3.5" />
                                        </button>
                                    </template>
                                    <button type="button"
                                        @click="eliminarTipo(tipo)"
                                        class="p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-zinc-400 hover:text-red-500 transition-colors">
                                        <flux:icon name="trash" class="size-3.5" />
                                    </button>
                                </div>
                            </div>

                            {{-- Opciones --}}
                            <div class="p-5 space-y-3">
                                <div class="space-y-2">
                                    <template x-if="tipo.opciones.length === 0">
                                        <flux:text class="text-xs text-zinc-400">Sin opciones aún.</flux:text>
                                    </template>
                                    <template x-for="opcion in tipo.opciones" :key="opcion.id">
                                        <div class="flex items-center gap-2 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 px-3 py-2">

                                            <div class="shrink-0">
                                                <template x-if="opcion.imagen">
                                                    <img :src="opcion.imagen" class="size-7 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-700" />
                                                </template>
                                                <template x-if="!opcion.imagen">
                                                    <div class="size-7 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                                        <flux:icon name="photo" class="size-3.5 text-zinc-400" />
                                                    </div>
                                                </template>
                                            </div>

                                            <template x-if="!opcion._editando">
                                                <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300" x-text="opcion.nombre"></span>
                                            </template>
                                            <template x-if="opcion._editando">
                                                <input
                                                    type="text"
                                                    x-model="opcion._nombreTemp"
                                                    @keydown.enter="guardarNombreOpcion(tipo, opcion)"
                                                    @keydown.escape="opcion._editando = false"
                                                    class="flex-1 rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-2 py-0.5 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-400"
                                                />
                                            </template>

                                            <div class="flex items-center gap-1 shrink-0">
                                                <label class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 transition-colors cursor-pointer" title="Subir imagen">
                                                    <flux:icon name="photo" class="size-3.5" />
                                                    <input type="file" accept="image/*" class="sr-only" @change="subirImagenOpcion(tipo, opcion, $event)" />
                                                </label>
                                                <template x-if="!opcion._editando">
                                                    <button type="button"
                                                        @click="opcion._editando = true; opcion._nombreTemp = opcion.nombre"
                                                        class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 transition-colors">
                                                        <flux:icon name="pencil" class="size-3.5" />
                                                    </button>
                                                </template>
                                                <template x-if="opcion._editando">
                                                    <button type="button"
                                                        @click="guardarNombreOpcion(tipo, opcion)"
                                                        class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-green-500 transition-colors">
                                                        <flux:icon name="check" class="size-3.5" />
                                                    </button>
                                                </template>
                                                <button type="button"
                                                    @click="eliminarOpcion(tipo, opcion)"
                                                    class="p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-zinc-400 hover:text-red-500 transition-colors">
                                                    <flux:icon name="trash" class="size-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Agregar opción --}}
                                <div class="flex gap-2 pt-1">
                                    <flux:input
                                        x-model="tipo._nuevaOpcion"
                                        ::placeholder="'Nueva opción de ' + tipo.nombre.toLowerCase()"
                                        class="flex-1 text-sm"
                                        @keydown.enter.prevent="agregarOpcion(tipo)"
                                    />
                                    <button
                                        type="button"
                                        @click="agregarOpcion(tipo)"
                                        :disabled="!tipo._nuevaOpcion || !tipo._nuevaOpcion.trim() || guardando"
                                        class="inline-flex items-center gap-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 disabled:opacity-40 hover:bg-zinc-50 transition-colors"
                                    >
                                        <flux:icon name="plus" class="size-3.5" />
                                        Opción
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── COMBINACIONES ── --}}
            <div style="display:none"
                x-effect="$el.style.display = (!cargando && tab === 'combinaciones') ? 'block' : 'none'">
                <div class="space-y-4">

                    {{-- Acciones --}}
                    <div class="flex items-center justify-between">
                        <flux:text class="text-sm text-zinc-500">
                            <span x-text="skus.length"></span> combinaciones ·
                            <span x-text="skus.filter(s => s.estado).length"></span> activas
                        </flux:text>
                        <button
                            type="button"
                            @click="generarSkus()"
                            :disabled="tipos.length === 0 || guardando"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 text-sm font-medium disabled:opacity-40 hover:opacity-90 transition-opacity"
                        >
                            <flux:icon name="sparkles" class="size-4" />
                            <span x-text="skus.length > 0 ? 'Generar faltantes' : 'Generar todas'"></span>
                        </button>
                    </div>

                    {{-- Mensaje generar --}}
                    <div
                        x-show="mensajeGenerar"
                        x-text="mensajeGenerar"
                        class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400"
                    ></div>

                    {{-- Empty state --}}
                    <template x-if="skus.length === 0">
                        <div class="flex flex-col items-center justify-center py-16 rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                            <flux:icon name="squares-2x2" class="size-10 text-zinc-300 mb-2" />
                            <flux:text class="text-zinc-500 text-sm font-medium">Sin combinaciones aún</flux:text>
                            <flux:text class="text-zinc-400 text-xs mt-1">Define los tipos y opciones, luego genera las combinaciones.</flux:text>
                        </div>
                    </template>

                    {{-- Tabla SKUs --}}
                    <template x-if="skus.length > 0">
                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Combinación</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide w-32">Precio lista</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide w-32">Precio venta</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wide w-20">Imagen</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wide w-20">Activa</th>
                                        <th class="px-4 py-3 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                    <template x-for="sku in skus" :key="sku.id">
                                        <tr :class="!sku.estado ? 'opacity-50' : ''" class="transition-opacity">

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-zinc-800 dark:text-zinc-200" x-text="sku.label"></div>
                                                <div x-show="sku.notas" x-text="sku.notas" class="text-xs text-zinc-400 mt-0.5"></div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <input
                                                    type="number"
                                                    x-model="sku._precioLista"
                                                    min="0" step="0.01"
                                                    placeholder="—"
                                                    @change="actualizarSku(sku)"
                                                    class="w-28 rounded-md border border-zinc-200 dark:border-zinc-700 bg-transparent px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-400"
                                                />
                                            </td>

                                            <td class="px-4 py-3">
                                                <input
                                                    type="number"
                                                    x-model="sku._precioVenta"
                                                    min="0" step="0.01"
                                                    @change="actualizarSku(sku)"
                                                    class="w-28 rounded-md border border-zinc-200 dark:border-zinc-700 bg-transparent px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-400"
                                                />
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <label ::for="'img-sku-' + sku.id" class="cursor-pointer inline-block">
                                                    <template x-if="sku.imagen">
                                                        <img :src="sku.imagen" class="size-9 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700 mx-auto hover:opacity-80 transition-opacity" />
                                                    </template>
                                                    <template x-if="!sku.imagen">
                                                        <div class="size-9 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mx-auto ring-1 ring-zinc-200 dark:ring-zinc-700 hover:bg-zinc-200 transition-colors">
                                                            <flux:icon name="photo" class="size-4 text-zinc-400" />
                                                        </div>
                                                    </template>
                                                    <input ::id="'img-sku-' + sku.id" type="file" accept="image/*" class="sr-only" @change="subirImagenSku(sku, $event)" />
                                                </label>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    type="button"
                                                    @click="toggleEstadoSku(sku)"
                                                    :class="sku.estado ? 'bg-green-500 hover:bg-green-600' : 'bg-zinc-300 dark:bg-zinc-600 hover:bg-zinc-400'"
                                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200"
                                                >
                                                    <span
                                                        :class="sku.estado ? 'translate-x-4' : 'translate-x-0.5'"
                                                        class="inline-block h-4 w-4 mt-0.5 rounded-full bg-white shadow transform transition-transform duration-200"
                                                    ></span>
                                                </button>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <button type="button"
                                                    @click="eliminarSku(sku)"
                                                    class="p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-zinc-400 hover:text-red-500 transition-colors">
                                                    <flux:icon name="trash" class="size-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>

        </div>{{-- fin variacionesManager --}}

    </div>{{-- fin tab container --}}

</div>

<style>
[x-cloak] { display: none !important; }

.ck-editor__editable {
    min-height: 320px;
}
</style>

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

    const { ClassicEditor, Essentials, Bold, Italic, Underline, Link, Paragraph, List } = CKEDITOR;

    ClassicEditor
        .create(container, {
            plugins: [Essentials, Bold, Italic, Underline, Link, Paragraph, List],
            toolbar: ['bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList'],
            initialData: textarea.value,
        })
        .then((editor) => {
            window.editorDetalles = editor;
            const form = document.getElementById('form-datos');
            if (form) {
                form.addEventListener('submit', () => {
                    textarea.value = editor.getData();
                });
            }
        })
        .catch((error) => {
            console.error('Error al inicializar CKEditor:', error);
        });
}

(function cargarCKEditor() {
    // Caso 1: CKEDITOR ya está disponible globalmente (otra vista ya lo cargó)
    if (typeof CKEDITOR !== 'undefined') {
        inicializarEditorDetalles();
        return;
    }

    // Caso 2: ya hay un <script> de CKEditor en el DOM
    const scriptExistente = document.querySelector('script[data-ckeditor-loader]');
    if (scriptExistente) {
        // Si el navegador ya completó la carga de ese script previamente (cache),
        // el evento "load" no volverá a disparar. Por eso usamos polling como respaldo.
        let intentos = 0;
        const esperar = setInterval(() => {
            intentos++;
            if (typeof CKEDITOR !== 'undefined') {
                clearInterval(esperar);
                inicializarEditorDetalles();
            } else if (intentos > 50) { // ~5 segundos máximo
                clearInterval(esperar);
                console.error('Tiempo de espera agotado para CKEDITOR.');
            }
        }, 100);
        return;
    }

    // Caso 3: primera carga real, inyectar el script
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



</x-layouts::app>