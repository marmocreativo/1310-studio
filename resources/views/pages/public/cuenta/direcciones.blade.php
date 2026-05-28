<x-layouts::cuenta title="Mis direcciones">
<div class="space-y-6"
     x-data="direccionesManager({{ $estados->map(fn($e) => ['id' => $e->id, 'nombre' => $e->nombre, 'municipios' => $e->municipios->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])->values()])->values() }})">

    <div class="flex items-center justify-between">
        <h1 class="font-serif text-3xl text-on-surface">Mis direcciones</h1>
        <button @click="abrirNueva()"
            class="bg-on-surface text-surface px-6 py-2.5 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
            + Nueva
        </button>
    </div>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Lista de direcciones --}}
    @forelse($direcciones as $dir)
        <div class="border border-outline-variant p-5 space-y-3">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-medium text-sm text-on-surface">{{ $dir->alias }}</p>
                        @if($dir->predeterminada)
                            <span class="text-[10px] tracking-[0.1em] uppercase border border-outline-variant px-2 py-0.5 text-on-surface-variant">
                                Predeterminada
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-on-surface">{{ $dir->nombre_contacto }} · {{ $dir->telefono }}</p>
                    <p class="text-xs text-on-surface-variant font-light">{{ $dir->direccion_completa }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if(!$dir->predeterminada)
                        <form method="POST" action="{{ route('cuenta.direcciones.predeterminada', $dir) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant hover:text-on-surface transition-colors underline">
                                Predeterminar
                            </button>
                        </form>
                    @endif
                    <button @click="abrirEditar({{ $dir->id }}, {{ $dir->toJson() }})"
                        class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant hover:text-on-surface transition-colors underline">
                        Editar
                    </button>
                    <form method="POST" action="{{ route('cuenta.direcciones.destroy', $dir) }}"
                          onsubmit="return confirm('¿Eliminar esta dirección?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[10px] tracking-[0.1em] uppercase text-red-500 hover:text-red-700 transition-colors underline">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="border border-outline-variant p-16 text-center space-y-3">
            <flux:icon name="map-pin" class="w-10 h-10 mx-auto text-outline-variant" />
            <p class="text-on-surface-variant font-light">No tienes direcciones guardadas.</p>
        </div>
    @endforelse

    {{-- ── Modal Nueva / Editar ── --}}
    <div x-show="modalAbierto" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-zinc-900 border border-outline-variant p-8 max-w-lg w-full max-h-[90vh] overflow-y-auto space-y-6"
             @click.outside="modalAbierto = false">

            <h2 class="font-serif text-2xl text-on-surface" x-text="modoEditar ? 'Editar dirección' : 'Nueva dirección'"></h2>

            <form :method="'POST'"
                  :action="modoEditar ? `/cuenta/direcciones/${direccionId}` : '{{ route('cuenta.direcciones.store') }}'"
                  id="form-direccion"
                  class="space-y-4">
                @csrf
                <input x-show="modoEditar" type="hidden" name="_method" value="PUT">

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2 col-span-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Alias</label>
                        <input type="text" name="alias" x-model="form.alias"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="Casa, Trabajo, etc." />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre de contacto *</label>
                        <input type="text" name="nombre_contacto" x-model="form.nombre_contacto" required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Teléfono *</label>
                        <input type="tel" name="telefono" x-model="form.telefono" required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="10 dígitos" />
                    </div>

                    <div class="space-y-2 col-span-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Calle *</label>
                        <input type="text" name="calle" x-model="form.calle" required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Número ext. *</label>
                        <input type="text" name="numero_ext" x-model="form.numero_ext" required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Número int.</label>
                        <input type="text" name="numero_int" x-model="form.numero_int"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Colonia *</label>
                        <input type="text" name="colonia" x-model="form.colonia" required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Código postal *</label>
                        <input type="text" name="cp" x-model="form.cp" required maxlength="5"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface transition-colors" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Estado *</label>
                        <select name="id_estado" x-model.number="form.id_estado"
                            @change="form.id_municipio = null"
                            required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface appearance-none">
                            <option value="">Selecciona</option>
                            <template x-for="estado in estados" :key="estado.id">
                                <option :value="estado.id" x-text="estado.nombre" :selected="form.id_estado === estado.id"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Alcaldía / Municipio *</label>
                        <select name="id_municipio" x-model.number="form.id_municipio"
                            :disabled="!form.id_estado"
                            required
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface appearance-none disabled:opacity-40">
                            <option value="">Selecciona</option>
                            <template x-for="mun in municipiosFiltrados" :key="mun.id">
                                <option :value="mun.id" x-text="mun.nombre" :selected="form.id_municipio === mun.id"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2 col-span-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Referencias</label>
                        <textarea name="referencias" x-model="form.referencias" rows="2"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm focus:outline-none focus:border-on-surface resize-none"
                            placeholder="Entre qué calles, color de la fachada, etc."></textarea>
                    </div>

                    <div class="col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="predeterminada" value="1"
                                x-model="form.predeterminada" class="w-4 h-4 accent-[#927F64]">
                            <span class="text-sm text-on-surface-variant font-light">Marcar como predeterminada</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="modalAbierto = false"
                        class="flex-1 border border-outline-variant text-on-surface-variant py-3 text-xs tracking-[0.15em] uppercase hover:border-on-surface transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-on-surface text-surface py-3 text-xs tracking-[0.15em] uppercase hover:opacity-80 transition-colors">
                        <span x-text="modoEditar ? 'Guardar cambios' : 'Agregar dirección'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('direccionesManager', (estados) => ({
        estados,
        modalAbierto: false,
        modoEditar:   false,
        direccionId:  null,

        form: {
            alias: '', nombre_contacto: '', telefono: '',
            calle: '', numero_ext: '', numero_int: '',
            colonia: '', cp: '', id_estado: null, id_municipio: null,
            referencias: '', predeterminada: false,
        },

        get municipiosFiltrados() {
            if (!this.form.id_estado) return [];
            return this.estados.find(e => e.id === this.form.id_estado)?.municipios ?? [];
        },

        abrirNueva() {
            this.modoEditar  = false;
            this.direccionId = null;
            this.form = {
                alias: 'Casa', nombre_contacto: '', telefono: '',
                calle: '', numero_ext: '', numero_int: '',
                colonia: '', cp: '', id_estado: null, id_municipio: null,
                referencias: '', predeterminada: false,
            };
            this.modalAbierto = true;
        },

        abrirEditar(id, dir) {
            this.modoEditar  = true;
            this.direccionId = id;
            this.form = {
                alias:           dir.alias ?? 'Casa',
                nombre_contacto: dir.nombre_contacto ?? '',
                telefono:        dir.telefono ?? '',
                calle:           dir.calle ?? '',
                numero_ext:      dir.numero_ext ?? '',
                numero_int:      dir.numero_int ?? '',
                colonia:         dir.colonia ?? '',
                cp:              dir.cp ?? '',
                id_estado:       dir.id_estado ?? null,
                id_municipio:    dir.id_municipio ?? null,
                referencias:     dir.referencias ?? '',
                predeterminada:  dir.predeterminada ?? false,
            };
            this.modalAbierto = true;
        },
    }));
});
</script>
</x-layouts::cuenta>