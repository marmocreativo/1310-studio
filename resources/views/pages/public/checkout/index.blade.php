<x-layouts::public title="Checkout">

<div class="py-16 px-8 max-w-[900px] mx-auto">

    {{-- Breadcrumb --}}
    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
        <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
        <span class="mx-2">·</span>
        <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
        <span class="mx-2">·</span>
        Checkout
    </p>

    @if(session('error'))
        <div class="mb-8 border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <div x-data="checkoutWizard({
            estados:   {{ $estados->map(fn($e) => [
                'id'         => $e->id,
                'nombre'     => $e->nombre,
                'municipios' => $e->municipios->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])->values(),
            ])->values()->toJson() }},
            mapaZonas: {{ $mapaZonas->toJson() }},
            direcciones: {{ $direcciones->map(fn($d) => [
                'id'           => $d->id,
                'alias'        => $d->alias,
                'resumen'      => $d->direccion_completa,
                'calle'        => $d->calle,
                'numero_ext'   => $d->numero_ext,
                'numero_int'   => $d->numero_int,
                'colonia'      => $d->colonia,
                'cp'           => $d->cp,
                'id_estado'    => $d->id_estado,
                'id_municipio' => $d->id_municipio,
                'referencias'  => $d->referencias,
                'predeterminada' => $d->predeterminada,
            ])->values()->toJson() }},
            subtotal: {{ $carrito->subtotal }},
            hayDirecciones: {{ $direcciones->isNotEmpty() ? 'true' : 'false' }},
            horaActual: {{ now()->setTimezone('America/Mexico_City')->hour }},
        })">

        {{-- ── Indicador de pasos ── --}}
        <div class="flex items-center gap-0 mb-12">
            <template x-for="(label, i) in ['Contacto', 'Entrega', 'Pago']" :key="i">
                <div class="flex items-center">
                    <div class="flex items-center gap-3 cursor-pointer" @click="irA(i + 1)">
                        <div class="w-7 h-7 flex items-center justify-center text-xs font-medium transition-colors"
                            :class="{
                                'bg-on-surface text-surface': paso === i + 1,
                                'bg-green-600 text-white': paso > i + 1,
                                'border border-outline-variant text-on-surface-variant': paso < i + 1
                            }">
                            <template x-if="paso > i + 1">
                                <flux:icon name="check" class="w-3.5 h-3.5" />
                            </template>
                            <template x-if="paso <= i + 1">
                                <span x-text="i + 1"></span>
                            </template>
                        </div>
                        <span class="text-[11px] tracking-[0.15em] uppercase hidden sm:block"
                            :class="paso === i + 1 ? 'text-on-surface' : 'text-on-surface-variant'">
                            <span x-text="label"></span>
                        </span>
                    </div>
                    <template x-if="i < 2">
                        <div class="w-12 md:w-20 h-px bg-outline-variant mx-3"></div>
                    </template>
                </div>
            </template>
        </div>

        <form method="POST" action="{{ route('checkout.procesar') }}" id="form-checkout">
            @csrf

            {{-- ════════════════════════════════════════
                PASO 1 — Contacto y Destinatario
            ════════════════════════════════════════ --}}
            <div x-show="paso === 1" class="space-y-8">

                <h2 class="font-serif text-3xl text-on-surface">Datos de contacto</h2>

                <div class="space-y-6">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Quien realiza el pedido
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre completo *</label>
                            <input type="text" name="nombre" x-model="f.nombre" required
                                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                placeholder="Tu nombre completo" />
                            <p x-show="errores.nombre" x-text="errores.nombre" class="text-xs text-red-600"></p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Correo electrónico *</label>
                            <input type="email" name="email" x-model="f.email" required
                                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                placeholder="correo@ejemplo.com" />
                            <p x-show="errores.email" x-text="errores.email" class="text-xs text-red-600"></p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Teléfono *</label>
                            <input type="tel" name="telefono" x-model="f.telefono" required
                                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                placeholder="10 dígitos" />
                            <p x-show="errores.telefono" x-text="errores.telefono" class="text-xs text-red-600"></p>
                        </div>
                    </div>
                </div>

                {{-- Destinatario --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-outline-variant pb-3">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">¿Es un regalo?</p>
                        <button type="button" @click="f.esRegalo = !f.esRegalo"
                            class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant hover:text-on-surface transition-colors underline">
                            <span x-text="f.esRegalo ? 'Cancelar' : 'Agregar destinatario'"></span>
                        </button>
                    </div>

                    <div x-show="f.esRegalo" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <p class="sm:col-span-2 text-xs text-on-surface-variant font-light">
                            Ingresa los datos de quien recibirá el pedido.
                        </p>
                        <div class="space-y-2">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre del destinatario</label>
                            <input type="text" name="destinatario_nombre" x-model="f.destinatario_nombre"
                                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                placeholder="Nombre de quien recibe" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Teléfono del destinatario</label>
                            <input type="tel" name="destinatario_telefono" x-model="f.destinatario_telefono"
                                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                placeholder="10 dígitos" />
                        </div>
                        <div class="space-y-2 sm:col-span-2">
                            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Mensaje para la tarjeta</label>
                            <textarea name="mensaje_tarjeta" x-model="f.mensaje_tarjeta" rows="3" maxlength="500"
                                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                                placeholder="Tu mensaje para acompañar el arreglo…"></textarea>
                            <p class="text-[10px] text-on-surface-variant text-right">Máximo 500 caracteres</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="siguientePaso()"
                        class="bg-on-surface text-surface px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
                        Siguiente
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════════════════
                PASO 2 — Entrega
            ════════════════════════════════════════ --}}
            <div x-show="paso === 2" class="space-y-8">

                <h2 class="font-serif text-3xl text-on-surface">Entrega</h2>

                {{-- Tipo --}}
                <div class="space-y-4">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Tipo de entrega
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label @click="f.tipo_entrega = 'envio'"
                            :class="f.tipo_entrega === 'envio' ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                            class="flex items-start gap-4 border p-5 cursor-pointer transition-colors">
                            <input type="radio" name="tipo_entrega" value="envio" x-model="f.tipo_entrega" class="sr-only" />
                            <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                <div x-show="f.tipo_entrega === 'envio'" class="w-2 h-2 bg-on-surface"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-on-surface">Envío a domicilio</p>
                                <p class="text-xs text-on-surface-variant mt-1 font-light">CDMX y zona metropolitana</p>
                            </div>
                        </label>
                        <label @click="f.tipo_entrega = 'tienda'"
                            :class="f.tipo_entrega === 'tienda' ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                            class="flex items-start gap-4 border p-5 cursor-pointer transition-colors">
                            <input type="radio" name="tipo_entrega" value="tienda" x-model="f.tipo_entrega" class="sr-only" />
                            <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                <div x-show="f.tipo_entrega === 'tienda'" class="w-2 h-2 bg-on-surface"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-on-surface">Recoger en tienda</p>
                                <p class="text-xs text-on-surface-variant mt-1 font-light">Sin costo de envío</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Dirección (solo envío) --}}
                <div x-show="f.tipo_entrega === 'envio'" class="space-y-6">

                    {{-- Direcciones guardadas --}}
                    @auth
                    <template x-if="hayDirecciones">
                        <div class="space-y-3">
                            <p class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Dirección de entrega</p>
                            <div class="space-y-3">
                                <template x-for="dir in config.direcciones" :key="dir.id">
                                    <label @click="seleccionarDireccion(dir)"
                                        :class="f.direccion_id === dir.id ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                                        class="flex items-start gap-4 border p-4 cursor-pointer transition-colors">
                                        <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                            <div x-show="f.direccion_id === dir.id" class="w-2 h-2 bg-on-surface"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-sm font-medium text-on-surface" x-text="dir.alias"></p>
                                                <template x-if="dir.predeterminada">
                                                    <span class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant border border-outline-variant px-2 py-0.5">
                                                        Predeterminada
                                                    </span>
                                                </template>
                                                {{-- Badge zona --}}
                                                <template x-if="zonaDeDir(dir)">
                                                    <span class="text-[10px] tracking-[0.1em] uppercase px-2 py-0.5 bg-surface-container-low border border-outline-variant text-on-surface-variant"
                                                        x-text="zonaDeDir(dir)?.nombre + ' — $' + zonaDeDir(dir)?.precio.toLocaleString('es-MX')">
                                                    </span>
                                                </template>
                                                <template x-if="!zonaDeDir(dir)">
                                                    <span class="text-[10px] text-red-500">Zona sin cobertura</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-on-surface-variant mt-1 font-light leading-relaxed" x-text="dir.resumen"></p>
                                        </div>
                                    </label>
                                </template>

                                <label @click="seleccionarDireccion(null)"
                                    :class="f.direccion_id === null ? 'border-on-surface bg-surface-container-low' : 'border-outline-variant hover:border-outline'"
                                    class="flex items-start gap-4 border border-dashed p-4 cursor-pointer transition-colors">
                                    <div class="mt-0.5 w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                        <div x-show="f.direccion_id === null" class="w-2 h-2 bg-on-surface"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="plus" class="w-4 h-4 text-on-surface-variant" />
                                        <p class="text-sm text-on-surface-variant">Usar una dirección diferente</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </template>
                    @endauth

                    {{-- Formulario de dirección --}}
                    <div x-show="f.direccion_id === null" class="space-y-4">
                        <p class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                            Datos de la dirección
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div class="space-y-2 sm:col-span-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Calle *</label>
                                <input type="text" name="calle" x-model="f.calle"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="Nombre de la calle" />
                                <p x-show="errores.calle" x-text="errores.calle" class="text-xs text-red-600"></p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Número exterior *</label>
                                <input type="text" name="numero_ext" x-model="f.numero_ext"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="123" />
                                <p x-show="errores.numero_ext" x-text="errores.numero_ext" class="text-xs text-red-600"></p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Número interior <span class="normal-case font-light">(opcional)</span>
                                </label>
                                <input type="text" name="numero_int" x-model="f.numero_int"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="Depto, Int., Piso" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Colonia *</label>
                                <input type="text" name="colonia" x-model="f.colonia"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="Colonia" />
                                <p x-show="errores.colonia" x-text="errores.colonia" class="text-xs text-red-600"></p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Código postal *</label>
                                <input type="text" name="cp" x-model="f.cp" maxlength="5"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                                    placeholder="00000" />
                                <p x-show="errores.cp" x-text="errores.cp" class="text-xs text-red-600"></p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Estado *</label>
                                <select name="id_estado" x-model.number="f.id_estado"
                                    @change="f.id_municipio = null; f.id_zona = null"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors appearance-none">
                                    <option value="">Selecciona un estado</option>
                                    <template x-for="estado in config.estados" :key="estado.id">
                                        <option :value="estado.id" x-text="estado.nombre"
                                            :selected="f.id_estado === estado.id"></option>
                                    </template>
                                </select>
                                <p x-show="errores.id_estado" x-text="errores.id_estado" class="text-xs text-red-600"></p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Alcaldía / Municipio *</label>
                                <select name="id_municipio" x-model.number="f.id_municipio"
                                    @change="actualizarZona()"
                                    :disabled="!f.id_estado"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors appearance-none disabled:opacity-40">
                                    <option value="">
                                        <template x-if="!f.id_estado">Primero elige un estado</template>
                                        <template x-if="f.id_estado">Selecciona una alcaldía</template>
                                    </option>
                                    <template x-for="mun in municipiosFiltrados" :key="mun.id">
                                        <option :value="mun.id" x-text="mun.nombre"
                                            :selected="f.id_municipio === mun.id"></option>
                                    </template>
                                </select>
                                <p x-show="errores.id_municipio" x-text="errores.id_municipio" class="text-xs text-red-600"></p>
                            </div>

                            {{-- Zona automática --}}
                            <div class="sm:col-span-2" x-show="f.id_municipio">
                                <template x-if="zonaActual">
                                    <div class="flex items-center gap-3 border border-outline-variant bg-surface-container-low px-4 py-3">
                                        <flux:icon name="map-pin" class="w-4 h-4 text-on-surface-variant shrink-0" />
                                        <div>
                                            <p class="text-xs text-on-surface-variant">Zona de entrega asignada</p>
                                            <p class="text-sm font-medium text-on-surface" x-text="zonaActual.nombre + ' — $' + zonaActual.precio.toLocaleString('es-MX', {minimumFractionDigits:2})"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="f.id_municipio && !zonaActual">
                                    <div class="flex items-center gap-3 border border-red-200 bg-red-50 px-4 py-3">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4 text-red-500 shrink-0" />
                                        <p class="text-sm text-red-600">Esta zona no tiene cobertura de envío por el momento.</p>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" name="id_zona" :value="f.id_zona">

                            <div class="space-y-2 sm:col-span-2">
                                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">
                                    Referencias <span class="normal-case font-light">(opcional)</span>
                                </label>
                                <textarea name="referencias" x-model="f.referencias" rows="2"
                                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                                    placeholder="Entre qué calles, color de la fachada, etc."></textarea>
                            </div>
                        </div>

                        {{-- Guardar dirección --}}
                        @auth
                        <label class="flex items-center gap-3 cursor-pointer pt-1">
                            <input type="checkbox" name="guardar_direccion" value="1"
                                x-model="f.guardar_direccion" class="w-4 h-4 accent-[#927F64]">
                            <span class="text-sm text-on-surface-variant font-light">
                                Guardar esta dirección en mi cuenta
                            </span>
                        </label>
                        @endauth
                    </div>

                    {{-- Inputs ocultos cuando se usa dirección guardada --}}
                    <template x-if="f.direccion_id !== null">
                        <div>
                            <input type="hidden" name="calle"        :value="f.calle">
                            <input type="hidden" name="numero_ext"   :value="f.numero_ext">
                            <input type="hidden" name="numero_int"   :value="f.numero_int">
                            <input type="hidden" name="colonia"      :value="f.colonia">
                            <input type="hidden" name="cp"           :value="f.cp">
                            <input type="hidden" name="id_estado"    :value="f.id_estado">
                            <input type="hidden" name="id_municipio" :value="f.id_municipio">
                            <input type="hidden" name="referencias"  :value="f.referencias">
                            <input type="hidden" name="id_zona"      :value="f.id_zona">
                        </div>
                    </template>
                </div>

                {{-- Fecha y Horario --}}
                <div class="space-y-6">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Fecha y horario de entrega
                    </p>

                    <div class="space-y-2">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Fecha de entrega *</label>
                        <input type="date" name="fecha_entrega" x-model="f.fecha_entrega"
                            :min="fechaMinima"
                            @change="validarBloque()"
                            class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
                        <p class="text-[11px] text-on-surface-variant" x-text="notaFecha"></p>
                        <p x-show="errores.fecha_entrega" x-text="errores.fecha_entrega" class="text-xs text-red-600"></p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Horario de entrega *</label>
                        <div class="grid grid-cols-3 gap-4">
                            <template x-for="bloque in bloques" :key="bloque.valor">
                                <label
                                    :class="{
                                        'border-on-surface bg-surface-container-low': f.bloque_entrega === bloque.valor && !bloque.desactivado,
                                        'border-outline-variant hover:border-outline cursor-pointer': !bloque.desactivado && f.bloque_entrega !== bloque.valor,
                                        'opacity-35 cursor-not-allowed': bloque.desactivado
                                    }"
                                    class="flex flex-col items-center gap-2 border p-5 transition-colors"
                                    @click="!bloque.desactivado && (f.bloque_entrega = bloque.valor)">
                                    <input type="radio" name="bloque_entrega" :value="bloque.valor"
                                        x-model="f.bloque_entrega" :disabled="bloque.desactivado" class="sr-only" />
                                    <span class="text-lg" x-text="bloque.emoji"></span>
                                    <span class="text-sm font-medium text-on-surface" x-text="bloque.label"></span>
                                    <span class="text-[11px] text-on-surface-variant" x-text="bloque.hora"></span>
                                    <template x-if="bloque.desactivado">
                                        <span class="text-[10px] text-on-surface-variant italic">No disponible</span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        <p x-show="errores.bloque_entrega" x-text="errores.bloque_entrega" class="text-xs text-red-600"></p>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="paso = 1"
                        class="border border-outline-variant text-on-surface-variant px-8 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface hover:text-on-surface transition-colors">
                        Anterior
                    </button>
                    <button type="button" @click="siguientePaso()"
                        class="bg-on-surface text-surface px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
                        Siguiente
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════════════════
                PASO 3 — Pago y Notas
            ════════════════════════════════════════ --}}
            <div x-show="paso === 3" class="space-y-8">

                <h2 class="font-serif text-3xl text-on-surface">Pago</h2>

                {{-- Resumen --}}
                <div class="border border-outline-variant p-6 space-y-4 bg-surface-container-low">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Resumen del pedido</p>
                    <div class="space-y-3">
                        @foreach($carrito->items as $item)
                            <div class="flex justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm text-on-surface truncate">{{ $item->nombre_snapshot }}</p>
                                    <p class="text-[11px] text-on-surface-variant">{{ $item->cantidad }} × ${{ number_format($item->precio_snapshot, 2) }}</p>
                                </div>
                                <p class="text-sm text-on-surface shrink-0">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-outline-variant pt-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-on-surface-variant font-light">Subtotal</span>
                            <span>${{ number_format($carrito->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-on-surface-variant font-light">Envío</span>
                            <span x-text="costoEnvioFormateado"></span>
                        </div>
                        <div class="flex justify-between items-baseline pt-2 border-t border-outline-variant">
                            <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                            <span class="font-serif text-2xl text-on-surface" x-text="totalFormateado"></span>
                        </div>
                    </div>
                </div>

                {{-- Método de pago --}}
                <div class="space-y-4">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Método de pago
                    </p>
                    <div class="space-y-3">
                        @foreach([
                            'tarjeta'        => ['label' => 'Tarjeta de crédito o débito',    'desc' => 'Visa, Mastercard, American Express'],
                            'efectivo'       => ['label' => 'Pago en tienda de conveniencia', 'desc' => 'OXXO, 7-Eleven, Farmacias del Ahorro'],
                            'transferencia'  => ['label' => 'Transferencia bancaria',          'desc' => 'SPEI — recibirás los datos por correo'],
                            'contra_entrega' => ['label' => 'Pago contra entrega',            'desc' => 'Solo disponible para recoger en tienda'],
                        ] as $valor => $metodo)
                            <label
                                @click="!('{{ $valor }}' === 'contra_entrega' && f.tipo_entrega !== 'tienda') && (f.metodo_pago = '{{ $valor }}')"
                                :class="{
                                    'border-on-surface bg-surface-container-low': f.metodo_pago === '{{ $valor }}',
                                    'border-outline-variant hover:border-outline cursor-pointer': f.metodo_pago !== '{{ $valor }}' && !('{{ $valor }}' === 'contra_entrega' && f.tipo_entrega !== 'tienda'),
                                    'opacity-40 cursor-not-allowed': '{{ $valor }}' === 'contra_entrega' && f.tipo_entrega !== 'tienda'
                                }"
                                class="flex items-center gap-4 border p-5 transition-colors">
                                <input type="radio" name="metodo_pago" value="{{ $valor }}"
                                    x-model="f.metodo_pago"
                                    :disabled="'{{ $valor }}' === 'contra_entrega' && f.tipo_entrega !== 'tienda'"
                                    class="sr-only" />
                                <div class="w-4 h-4 shrink-0 border border-current flex items-center justify-center">
                                    <div x-show="f.metodo_pago === '{{ $valor }}'" class="w-2 h-2 bg-on-surface"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">{{ $metodo['label'] }}</p>
                                    <p class="text-xs text-on-surface-variant font-light mt-0.5">{{ $metodo['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p x-show="errores.metodo_pago" x-text="errores.metodo_pago" class="text-xs text-red-600"></p>
                </div>

                {{-- Notas --}}
                <div class="space-y-3">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                        Notas adicionales <span class="normal-case font-light">(opcional)</span>
                    </p>
                    <textarea name="notas" x-model="f.notas" rows="3"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                        placeholder="Instrucciones especiales, alergias, preferencias…"></textarea>
                </div>

                {{-- Inputs ocultos del formulario completo --}}
                <input type="hidden" name="nombre"                :value="f.nombre">
                <input type="hidden" name="email"                 :value="f.email">
                <input type="hidden" name="telefono"              :value="f.telefono">
                <input type="hidden" name="destinatario_nombre"   :value="f.destinatario_nombre">
                <input type="hidden" name="destinatario_telefono" :value="f.destinatario_telefono">
                <input type="hidden" name="mensaje_tarjeta"       :value="f.mensaje_tarjeta">
                <input type="hidden" name="tipo_entrega"          :value="f.tipo_entrega">
                <input type="hidden" name="fecha_entrega"         :value="f.fecha_entrega">
                <input type="hidden" name="bloque_entrega"        :value="f.bloque_entrega">
                <input type="hidden" name="metodo_pago"           :value="f.metodo_pago">
                <input type="hidden" name="notas"                 :value="f.notas">

                <div class="flex justify-between pt-4">
                    <button type="button" @click="paso = 2"
                        class="border border-outline-variant text-on-surface-variant px-8 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface hover:text-on-surface transition-colors">
                        Anterior
                    </button>
                    <button type="submit" :disabled="enviando"
                        :class="enviando ? 'opacity-60 cursor-wait' : 'hover:opacity-80'"
                        class="bg-on-surface text-surface px-10 py-4 text-xs tracking-[0.3em] uppercase transition-colors flex items-center gap-2">
                        <template x-if="enviando">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </template>
                        <span x-text="enviando ? 'Procesando…' : 'Confirmar pedido'"></span>
                    </button>
                </div>

                <p class="text-[10px] tracking-[0.05em] text-on-surface-variant text-center leading-relaxed">
                    Al confirmar aceptas nuestros
                    <a href="{{ route('paginas.show', 'terminos-y-condiciones') }}"
                       class="underline hover:text-on-surface transition-colors" target="_blank">términos y condiciones</a>
                </p>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutWizard', (config) => ({

        paso: 1,
        enviando: false,
        hayDirecciones: config.hayDirecciones,
        errores: {},

        // ── Hora actual CDMX ──────────────────────
        horaActual: config.horaActual,

        // ── Bloques con lógica de disponibilidad ──
        get bloques() {
            const esPasadasLas12 = this.horaActual >= 12;
            const esHoy = this.f.fecha_entrega === this.fechaMinima;

            return [
                {
                    valor: 'manana',
                    label: 'Mañana',
                    hora: '9:00 – 13:00',
                    emoji: '🌤',
                    desactivado: esPasadasLas12 && esHoy,
                },
                {
                    valor: 'tarde',
                    label: 'Tarde',
                    hora: '13:00 – 18:00',
                    emoji: '☁️',
                    desactivado: false,
                },
                {
                    valor: 'noche',
                    label: 'Noche',
                    hora: '18:00 – 21:00',
                    emoji: '🌙',
                    desactivado: false,
                },
            ];
        },

        // ── Fecha mínima ──────────────────────────
        get fechaMinima() {
            const ahora = new Date();
            const cdmx  = new Date(ahora.toLocaleString('en-US', { timeZone: 'America/Mexico_City' }));
            const hora  = cdmx.getHours();

            // Si ya pasó del mediodía, mínimo es mañana; si no, es hoy + 6h (mismo día)
            if (hora >= 12) {
                cdmx.setDate(cdmx.getDate() + 1);
            }
            return cdmx.toISOString().split('T')[0];
        },

        get notaFecha() {
            if (this.horaActual >= 12) {
                return 'Es después de las 12:00 — el primer día disponible es mañana.';
            }
            return 'Pedidos con mínimo 6 horas de anticipación.';
        },

        // ── Computed zona ─────────────────────────
        get zonaActual() {
            if (!this.f.id_municipio) return null;
            return config.mapaZonas[this.f.id_municipio] ?? null;
        },

        get municipiosFiltrados() {
            if (!this.f.id_estado) return [];
            return config.estados.find(e => e.id === this.f.id_estado)?.municipios ?? [];
        },

        get costoEnvio() {
            if (this.f.tipo_entrega === 'tienda') return 0;
            return this.zonaActual?.precio ?? 0;
        },

        get costoEnvioFormateado() {
            if (this.f.tipo_entrega === 'tienda') return 'Gratis';
            if (!this.zonaActual) return 'Por definir';
            return '$' + this.costoEnvio.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        get totalFormateado() {
            const total = config.subtotal + this.costoEnvio;
            return '$' + total.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // ── Helper zona por dirección guardada ────
        zonaDeDir(dir) {
            return config.mapaZonas[dir.id_municipio] ?? null;
        },

        // ── Estado del formulario ─────────────────
        f: {
            // Paso 1
            nombre:                '{{ old('nombre', auth()->user()?->name ?? '') }}',
            email:                 '{{ old('email', auth()->user()?->email ?? '') }}',
            telefono:              '{{ old('telefono') }}',
            esRegalo:              false,
            destinatario_nombre:   '{{ old('destinatario_nombre') }}',
            destinatario_telefono: '{{ old('destinatario_telefono') }}',
            mensaje_tarjeta:       '{{ old('mensaje_tarjeta') }}',

            // Paso 2
            tipo_entrega:    '{{ old('tipo_entrega', 'envio') }}',
            direccion_id:    null,
            calle:           '{{ old('calle') }}',
            numero_ext:      '{{ old('numero_ext') }}',
            numero_int:      '{{ old('numero_int') }}',
            colonia:         '{{ old('colonia') }}',
            cp:              '{{ old('cp') }}',
            id_estado:       {{ old('id_estado', 'null') }},
            id_municipio:    {{ old('id_municipio', 'null') }},
            id_zona:         {{ old('id_zona', 'null') }},
            referencias:     '{{ old('referencias') }}',
            guardar_direccion: false,
            fecha_entrega:   '{{ old('fecha_entrega') }}',
            bloque_entrega:  '{{ old('bloque_entrega') }}',

            // Paso 3
            metodo_pago: '{{ old('metodo_pago', 'tarjeta') }}',
            notas:       '{{ old('notas') }}',
        },

        // ── Métodos ───────────────────────────────

        actualizarZona() {
            this.f.id_zona = this.zonaActual?.id ?? null;
        },

        seleccionarDireccion(dir) {
            if (dir === null) {
                this.f.direccion_id  = null;
                this.f.calle         = '';
                this.f.numero_ext    = '';
                this.f.numero_int    = '';
                this.f.colonia       = '';
                this.f.cp            = '';
                this.f.id_estado     = null;
                this.f.id_municipio  = null;
                this.f.id_zona       = null;
                this.f.referencias   = '';
            } else {
                this.f.direccion_id  = dir.id;
                this.f.calle         = dir.calle;
                this.f.numero_ext    = dir.numero_ext;
                this.f.numero_int    = dir.numero_int ?? '';
                this.f.colonia       = dir.colonia;
                this.f.cp            = dir.cp;
                this.f.id_estado     = dir.id_estado;
                this.f.id_municipio  = dir.id_municipio;
                this.f.id_zona       = this.zonaDeDir(dir)?.id ?? null;
                this.f.referencias   = dir.referencias ?? '';
            }
        },

        validarBloque() {
            // Si el bloque seleccionado quedó desactivado, limpiarlo
            const bloqueActivo = this.bloques.find(b => b.valor === this.f.bloque_entrega);
            if (bloqueActivo?.desactivado) {
                this.f.bloque_entrega = '';
            }
        },

        irA(numeroPaso) {
            // Solo permite ir a pasos ya completados
            if (numeroPaso < this.paso) this.paso = numeroPaso;
        },

        siguientePaso() {
            this.errores = {};

            if (this.paso === 1) {
                if (!this.f.nombre.trim())  this.errores.nombre   = 'El nombre es requerido.';
                if (!this.f.email.trim())   this.errores.email    = 'El correo es requerido.';
                if (!this.f.telefono.trim()) this.errores.telefono = 'El teléfono es requerido.';
                if (Object.keys(this.errores).length === 0) this.paso = 2;
            }

            else if (this.paso === 2) {
                if (this.f.tipo_entrega === 'envio') {
                    if (!this.f.calle.trim())      this.errores.calle        = 'La calle es requerida.';
                    if (!this.f.numero_ext.trim())  this.errores.numero_ext   = 'El número exterior es requerido.';
                    if (!this.f.colonia.trim())     this.errores.colonia      = 'La colonia es requerida.';
                    if (!this.f.cp.trim())          this.errores.cp           = 'El código postal es requerido.';
                    if (!this.f.id_estado)          this.errores.id_estado    = 'Selecciona un estado.';
                    if (!this.f.id_municipio)       this.errores.id_municipio = 'Selecciona una alcaldía.';
                    if (this.f.id_municipio && !this.zonaActual)
                                                    this.errores.id_municipio = 'Esta zona no tiene cobertura.';
                }
                if (!this.f.fecha_entrega)    this.errores.fecha_entrega   = 'Selecciona una fecha.';
                if (!this.f.bloque_entrega)   this.errores.bloque_entrega  = 'Selecciona un horario.';
                if (Object.keys(this.errores).length === 0) this.paso = 3;
            }
        },

        init() {
            // Precargar dirección predeterminada
            if (this.hayDirecciones && config.direcciones.length > 0) {
                const pred = config.direcciones.find(d => d.predeterminada) ?? config.direcciones[0];
                this.seleccionarDireccion(pred);
            }

            // Preseleccionar fecha mínima
            if (!this.f.fecha_entrega) {
                this.f.fecha_entrega = this.fechaMinima;
            }

            // Spinner al enviar
            document.getElementById('form-checkout').addEventListener('submit', () => {
                this.enviando = true;
            });
        },
    }));
});
</script>

</x-layouts::public>