<x-layouts::public title="Checkout — Paso 2">
<div class="py-16 px-8 max-w-[800px] mx-auto">

    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
        <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
        <span class="mx-2">·</span>
        <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
        <span class="mx-2">·</span>
        Checkout
    </p>

    @include('pages.public.checkout.partials.pasos', ['pasoActual' => 2])

    <h1 class="font-serif text-3xl text-on-surface mb-8">Entrega</h1>

    @if($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-700">Revisa los campos marcados en rojo.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.paso2.store') }}"
          x-data="paso2({
              estados: {{ $estados->map(fn($e) => [
                  'id'         => $e->id,
                  'nombre'     => $e->nombre,
                  'municipios' => $e->municipios->map(fn($m) => [
                      'id'     => $m->id,
                      'nombre' => $m->nombre,
                  ])->values(),
              ])->values() }},
              mapaZonas: {{ $mapaZonas }},
              direcciones: {{ $direcciones->map(fn($d) => [
                  'id'             => $d->id,
                  'alias'          => $d->alias,
                  'resumen'        => $d->direccion_completa,
                  'calle'          => $d->calle,
                  'numero_ext'     => $d->numero_ext,
                  'numero_int'     => $d->numero_int,
                  'colonia'        => $d->colonia,
                  'cp'             => $d->cp,
                  'id_estado'      => $d->id_estado,
                  'id_municipio'   => $d->id_municipio,
                  'referencias'    => $d->referencias,
                  'predeterminada' => $d->predeterminada,
              ])->values() }},
              hayDirecciones: {{ $direcciones->isNotEmpty() ? 'true' : 'false' }},
              hora: {{ $hora }},
              fechaMinima: '{{ $fechaMinima }}',
              old: {
                  tipo_entrega:   '{{ old('tipo_entrega',  $datos['tipo_entrega']  ?? 'envio') }}',
                  id_estado:      {{ old('id_estado',      $datos['id_estado']      ?? 'null') }},
                  id_municipio:   {{ old('id_municipio',   $datos['id_municipio']   ?? 'null') }},
                  fecha_entrega:  '{{ old('fecha_entrega', $datos['fecha_entrega']  ?? '') }}',
                  bloque_entrega: '{{ old('bloque_entrega',$datos['bloque_entrega'] ?? '') }}',
                  calle:          '{{ old('calle',         $datos['calle']          ?? '') }}',
                  numero_ext:     '{{ old('numero_ext',    $datos['numero_ext']     ?? '') }}',
                  numero_int:     '{{ old('numero_int',    $datos['numero_int']     ?? '') }}',
                  colonia:        '{{ old('colonia',       $datos['colonia']        ?? '') }}',
                  cp:             '{{ old('cp',            $datos['cp']             ?? '') }}',
                  referencias:    '{{ old('referencias',   $datos['referencias']    ?? '') }}',
              },
          })"
          x-on:submit="prepararSubmit()"
          class="space-y-8">
        @csrf

        {{-- Tipo de entrega --}}
        <div class="space-y-4">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                Tipo de entrega
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label x-on:click="tipoEntrega = 'envio'"
                    x-bind:class="tipoEntrega === 'envio' ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                    class="flex items-start gap-4 border p-5 cursor-pointer transition-colors">
                    <input type="radio" name="tipo_entrega" value="envio" x-model="tipoEntrega" class="sr-only">
                    <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                        <div x-show="tipoEntrega === 'envio'" class="w-2 h-2 bg-on-surface"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-on-surface">Envío a domicilio</p>
                        <p class="text-xs text-on-surface-variant mt-1 font-light">CDMX y zona metropolitana</p>
                    </div>
                </label>
                <label x-on:click="tipoEntrega = 'tienda'"
                    x-bind:class="tipoEntrega === 'tienda' ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                    class="flex items-start gap-4 border p-5 cursor-pointer transition-colors">
                    <input type="radio" name="tipo_entrega" value="tienda" x-model="tipoEntrega" class="sr-only">
                    <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                        <div x-show="tipoEntrega === 'tienda'" class="w-2 h-2 bg-on-surface"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-on-surface">Recoger en tienda</p>
                        <p class="text-xs text-on-surface-variant mt-1 font-light">Sin costo de envío</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Dirección (solo envío) --}}
        <div x-show="tipoEntrega === 'envio'" class="space-y-6">

            {{-- Direcciones guardadas --}}
            @auth
            @if($direcciones->isNotEmpty())
            <div class="space-y-3">
                <p class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Dirección de entrega</p>
                <div class="space-y-3">
                    @foreach($direcciones as $dir)
                        @php $zona = $mapaZonas->get($dir->id_municipio); @endphp
                        <label x-on:click="seleccionarDireccion({{ $dir->id }})"
                            x-bind:class="direccionId === {{ $dir->id }} ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                            class="flex items-start gap-4 border p-4 cursor-pointer transition-colors">
                            <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                <div x-show="direccionId === {{ $dir->id }}" class="w-2 h-2 bg-on-surface"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-medium text-on-surface">{{ $dir->alias }}</p>
                                    @if($dir->predeterminada)
                                        <span class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant border border-outline-variant px-2 py-0.5">
                                            Predeterminada
                                        </span>
                                    @endif
                                    @if($zona)
                                        <span class="text-[10px] tracking-[0.1em] uppercase px-2 py-0.5 bg-surface-container-low border border-outline-variant text-on-surface-variant">
                                            {{ $zona['nombre'] }} — ${{ number_format($zona['precio'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-red-500">Sin cobertura</span>
                                    @endif
                                </div>
                                <p class="text-xs text-on-surface-variant mt-1 font-light">{{ $dir->direccion_completa }}</p>
                            </div>
                        </label>
                    @endforeach

                    <label x-on:click="seleccionarDireccion(null)"
                        x-bind:class="direccionId === null ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                        class="flex items-start gap-4 border border-dashed p-4 cursor-pointer transition-colors">
                        <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                            <div x-show="direccionId === null" class="w-2 h-2 bg-on-surface"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:icon name="plus" class="w-4 h-4 text-on-surface-variant" />
                            <p class="text-sm text-on-surface-variant">Usar una dirección diferente</p>
                        </div>
                    </label>
                </div>
            </div>
            @endif
            @endauth

            {{-- Formulario de dirección — visible cuando se elige nueva --}}
            <div x-show="mostrarFormulario" class="space-y-5">
                <p class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                    Datos de la dirección
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    @auth
                    <div class="space-y-2 sm:col-span-2" x-show="guardarDireccion">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Alias</label>
                        <input type="text" name="alias" value="{{ old('alias', 'Casa') }}"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="Casa, Trabajo, etc." />
                    </div>
                    @endauth

                    <div class="space-y-2 sm:col-span-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Calle *</label>
                        <input type="text" x-model="form.calle"
                            class="w-full border {{ $errors->has('calle') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="Nombre de la calle" />
                        @error('calle') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Número exterior *</label>
                        <input type="text" x-model="form.numero_ext"
                            class="w-full border {{ $errors->has('numero_ext') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="123" />
                        @error('numero_ext') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                            Número interior <span class="normal-case font-light">(opcional)</span>
                        </label>
                        <input type="text" x-model="form.numero_int"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="Depto, Int., Piso" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Colonia *</label>
                        <input type="text" x-model="form.colonia"
                            class="w-full border {{ $errors->has('colonia') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="Colonia" />
                        @error('colonia') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Código postal *</label>
                        <input type="text" x-model="form.cp" maxlength="5"
                            class="w-full border {{ $errors->has('cp') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                            placeholder="00000" />
                        @error('cp') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Estado *</label>
                        <select x-model.number="form.id_estado"
                            x-on:change="form.id_municipio = null; idZona = null"
                            class="w-full border {{ $errors->has('id_estado') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors appearance-none">
                            <option value="">Selecciona un estado</option>
                            <template x-for="estado in estados" :key="estado.id">
                                <option :value="estado.id" x-text="estado.nombre"
                                    :selected="form.id_estado === estado.id"></option>
                            </template>
                        </select>
                        @error('id_estado') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Alcaldía / Municipio *</label>
                        <select x-model.number="form.id_municipio"
                            x-on:change="actualizarZona()"
                            :disabled="!form.id_estado"
                            class="w-full border {{ $errors->has('id_municipio') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors appearance-none disabled:opacity-40">
                            <option value="" x-text="form.id_estado ? 'Selecciona una alcaldía' : 'Primero elige un estado'"></option>
                            <template x-for="mun in municipiosFiltrados" :key="mun.id">
                                <option :value="mun.id" x-text="mun.nombre"
                                    :selected="form.id_municipio === mun.id"></option>
                            </template>
                        </select>
                        @error('id_municipio') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Zona automática --}}
                    <div class="sm:col-span-2" x-show="form.id_municipio">
                        <template x-if="zonaActual">
                            <div class="flex items-center gap-3 border border-outline-variant bg-surface-container-low px-4 py-3">
                                <flux:icon name="map-pin" class="w-4 h-4 text-on-surface-variant shrink-0" />
                                <div>
                                    <p class="text-xs text-on-surface-variant">Zona de entrega asignada</p>
                                    <p class="text-sm font-medium text-on-surface"
                                        x-text="zonaActual.nombre + ' — $' + zonaActual.precio.toLocaleString('es-MX', {minimumFractionDigits:2})">
                                    </p>
                                </div>
                            </div>
                        </template>
                        <template x-if="form.id_municipio && !zonaActual">
                            <div class="flex items-center gap-3 border border-red-200 bg-red-50 px-4 py-3">
                                <flux:icon name="exclamation-triangle" class="w-4 h-4 text-red-500 shrink-0" />
                                <p class="text-sm text-red-600">Esta zona no tiene cobertura de envío.</p>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-2 sm:col-span-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                            Referencias <span class="normal-case font-light">(opcional)</span>
                        </label>
                        <textarea x-model="form.referencias" rows="2"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                            placeholder="Entre qué calles, color de la fachada, etc."></textarea>
                    </div>
                </div>

                @auth
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="guardar_direccion" value="1"
                        x-model="guardarDireccion" class="w-4 h-4 accent-[#927F64]">
                    <span class="text-sm text-on-surface-variant font-light">Guardar esta dirección en mi cuenta</span>
                </label>
                @endauth
            </div>

            {{-- Inputs hidden — siempre presentes, llenados por prepararSubmit() --}}
            <input type="hidden" name="calle"        id="hidden_calle">
            <input type="hidden" name="numero_ext"   id="hidden_numero_ext">
            <input type="hidden" name="numero_int"   id="hidden_numero_int">
            <input type="hidden" name="colonia"      id="hidden_colonia">
            <input type="hidden" name="cp"           id="hidden_cp">
            <input type="hidden" name="id_estado"    id="hidden_id_estado">
            <input type="hidden" name="id_municipio" id="hidden_id_municipio">
            <input type="hidden" name="referencias"  id="hidden_referencias">
            <input type="hidden" name="id_zona"      id="hidden_id_zona">

        </div>

        {{-- Fecha --}}
        <div class="space-y-5">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                Fecha y horario de entrega
            </p>

            <div class="space-y-2">
                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Fecha de entrega *</label>
                <input type="date" name="fecha_entrega"
                    x-model="fechaEntrega"
                    x-bind:min="fechaMinima"
                    x-on:change="validarBloque()"
                    class="w-full border {{ $errors->has('fecha_entrega') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
                <p class="text-[11px] text-on-surface-variant" x-text="notaFecha"></p>
                @error('fecha_entrega') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3">
                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Horario de entrega *</label>
                <div class="grid grid-cols-3 gap-4">
                    <template x-for="bloque in bloques" :key="bloque.valor">
                        <label
                            x-bind:class="{
                                'border-on-surface bg-surface-container-low cursor-pointer': bloqueEntrega === bloque.valor && !bloque.desactivado,
                                'border-outline-variant hover:border-outline cursor-pointer': bloqueEntrega !== bloque.valor && !bloque.desactivado,
                                'opacity-35 cursor-not-allowed': bloque.desactivado
                            }"
                            class="flex flex-col items-center gap-2 border p-5 transition-colors"
                            x-on:click="!bloque.desactivado && (bloqueEntrega = bloque.valor)">
                            <input type="radio" name="bloque_entrega" x-bind:value="bloque.valor"
                                x-model="bloqueEntrega" x-bind:disabled="bloque.desactivado" class="sr-only">
                            <span class="text-xl" x-text="bloque.emoji"></span>
                            <span class="text-sm font-medium text-on-surface" x-text="bloque.label"></span>
                            <span class="text-[11px] text-on-surface-variant" x-text="bloque.hora"></span>
                            <span x-show="bloque.desactivado" class="text-[10px] text-on-surface-variant italic">No disponible</span>
                        </label>
                    </template>
                </div>
                @error('bloque_entrega') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-between pt-4">
            <a href="{{ route('checkout.paso1') }}"
                class="border border-outline-variant text-on-surface-variant px-8 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface hover:text-on-surface transition-colors">
                Anterior
            </a>
            <button type="submit"
                class="bg-on-surface text-surface px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
                Siguiente
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('paso2', (config) => ({

        tipoEntrega:      config.old.tipo_entrega,
        direccionId:      null,
        idZona:           null,
        fechaEntrega:     config.old.fecha_entrega || config.fechaMinima,
        bloqueEntrega:    config.old.bloque_entrega,
        guardarDireccion: false,

        // Exponer como propiedades del componente para evitar problemas con x-for
        estados:   config.estados,
        mapaZonas: config.mapaZonas,

        form: {
            calle:        config.old.calle,
            numero_ext:   config.old.numero_ext,
            numero_int:   config.old.numero_int,
            colonia:      config.old.colonia,
            cp:           config.old.cp,
            id_estado:    config.old.id_estado,
            id_municipio: config.old.id_municipio,
            referencias:  config.old.referencias,
        },

        get mostrarFormulario() {
            return this.direccionId === null;
        },

        get municipiosFiltrados() {
            if (!this.form.id_estado) return [];
            return this.estados.find(e => e.id === this.form.id_estado)?.municipios ?? [];
        },

        get zonaActual() {
            if (!this.form.id_municipio) return null;
            return this.mapaZonas[this.form.id_municipio] ?? null;
        },

        get notaFecha() {
            return config.hora >= 12
                ? 'Es después de las 12:00 — el primer día disponible es mañana.'
                : 'Pedidos con mínimo 6 horas de anticipación.';
        },

        get fechaMinima() {
            return config.fechaMinima;
        },

        get bloques() {
            const esPasadasLas12 = config.hora >= 12;
            const esHoy = this.fechaEntrega === config.fechaMinima;
            return [
                { valor: 'manana', label: 'Mañana', hora: '9:00 - 13:00',  emoji: '🌤',  desactivado: esPasadasLas12 && esHoy },
                { valor: 'tarde',  label: 'Tarde',  hora: '13:00 - 18:00', emoji: '☁️',  desactivado: false },
                { valor: 'noche',  label: 'Noche',  hora: '18:00 - 21:00', emoji: '🌙',  desactivado: false },
            ];
        },

        actualizarZona() {
            this.idZona = this.zonaActual?.id ?? null;
        },

        seleccionarDireccion(id) {
            if (id === null) {
                this.direccionId       = null;
                this.form.calle        = '';
                this.form.numero_ext   = '';
                this.form.numero_int   = '';
                this.form.colonia      = '';
                this.form.cp           = '';
                this.form.id_estado    = null;
                this.form.id_municipio = null;
                this.form.referencias  = '';
                this.idZona            = null;
            } else {
                this.direccionId = id;
                const dir = config.direcciones.find(d => d.id === id);
                if (dir) {
                    this.form.calle        = dir.calle        ?? '';
                    this.form.numero_ext   = dir.numero_ext   ?? '';
                    this.form.numero_int   = dir.numero_int   ?? '';
                    this.form.colonia      = dir.colonia      ?? '';
                    this.form.cp           = dir.cp           ?? '';
                    this.form.id_estado    = dir.id_estado    ?? null;
                    this.form.id_municipio = dir.id_municipio ?? null;
                    this.form.referencias  = dir.referencias  ?? '';
                    this.idZona            = this.mapaZonas[dir.id_municipio]?.id ?? null;
                }
            }
        },

        prepararSubmit() {
            // Llenar inputs hidden via JS directo antes del submit
            const map = {
                'hidden_calle':        this.form.calle        ?? '',
                'hidden_numero_ext':   this.form.numero_ext   ?? '',
                'hidden_numero_int':   this.form.numero_int   ?? '',
                'hidden_colonia':      this.form.colonia      ?? '',
                'hidden_cp':           this.form.cp           ?? '',
                'hidden_id_estado':    this.form.id_estado    ?? '',
                'hidden_id_municipio': this.form.id_municipio ?? '',
                'hidden_referencias':  this.form.referencias  ?? '',
                'hidden_id_zona':      this.idZona            ?? '',
            };

            Object.entries(map).forEach(([id, value]) => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            });
        },

        validarBloque() {
            const bloqueActivo = this.bloques.find(b => b.valor === this.bloqueEntrega);
            if (bloqueActivo?.desactivado) this.bloqueEntrega = '';
        },

        init() {
            // Precargar dirección predeterminada si hay guardadas
            if (config.hayDirecciones && config.direcciones.length > 0) {
                const pred = config.direcciones.find(d => d.predeterminada) ?? config.direcciones[0];
                this.seleccionarDireccion(pred.id);
            }

            // Inicializar zona si ya hay municipio (caso old values)
            if (this.form.id_municipio) {
                this.actualizarZona();
            }
        },
    }));
});
</script>
</x-layouts::public>