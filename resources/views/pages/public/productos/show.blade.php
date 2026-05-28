<x-layouts::public :title="$producto->nombre">

@php
    // ── Control de modo de compra ──────────────────────────────────────────
    $modoCarrito = $conf['activar_tienda'] ?? false;
@endphp

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
            <span class="mx-2">·</span>
            @if ($producto->categorias->first())
                <a href="{{ route('categorias.show', $producto->categorias->first()->slug) }}"
                   class="hover:text-on-surface transition-colors">
                    {{ $producto->categorias->first()->titulo }}
                </a>
                <span class="mx-2">·</span>
            @endif
            {{ $producto->nombre }}
        </p>

        {{-- Layout principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-24"
            x-data="{
                activa: 0,
                lightbox: false,
                zoom: false,
                total: {{ $producto->galeria->count() }},
                imagenSku: null,
                next() { if(!this.imagenSku){ this.activa = (this.activa + 1) % this.total; this.zoom = false; } },
                prev() { if(!this.imagenSku){ this.activa = (this.activa - 1 + this.total) % this.total; this.zoom = false; } },
            }"
            @sku-imagen.window="imagenSku = $event.detail.url"
            @sku-imagen-clear.window="imagenSku = null"
            @keydown.escape.window="lightbox = false; zoom = false"
            @keydown.arrow-right.window="if(lightbox) next()"
            @keydown.arrow-left.window="if(lightbox) prev()">

            {{-- ─── GALERÍA ─────────────────────────────── --}}
            <div>
                <div class="aspect-square overflow-hidden bg-surface-container-low mb-4 relative group cursor-zoom-in"
                     @click="lightbox = true">

                    <template x-if="imagenSku">
                        <img :src="imagenSku"
                             alt="{{ $producto->nombre }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </template>

                    <template x-if="!imagenSku">
                        <div class="w-full h-full">
                            @forelse ($producto->galeria as $i => $img)
                                <img src="{{ Storage::disk('public')->url($img->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     x-show="activa === {{ $i }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @empty
                                <div class="w-full h-full flex items-center justify-center">
                                    <flux:icon name="photo" class="w-16 h-16 text-outline-variant" />
                                </div>
                            @endforelse
                        </div>
                    </template>

                    @if ($producto->galeria->count() > 1)
                        <div x-show="!imagenSku"
                             class="absolute bottom-4 right-4 bg-black/50 text-white text-[10px] tracking-[0.1em] px-3 py-1">
                            <span x-text="activa + 1"></span> / {{ $producto->galeria->count() }}
                        </div>
                    @endif
                </div>

                <template x-if="imagenSku">
                    <div class="flex items-end gap-3 mb-3">
                        <div class="flex-shrink-0 w-20 h-20 overflow-hidden border-2 border-on-surface">
                            <img :src="imagenSku" alt="Variación seleccionada" class="w-full h-full object-cover">
                        </div>
                        @if ($producto->galeria->count() > 0)
                            <button type="button"
                                @click="imagenSku = null; activa = 0"
                                class="text-[10px] tracking-[0.1em] uppercase text-outline hover:text-on-surface transition-colors underline pb-1">
                                Ver galería
                            </button>
                        @endif
                    </div>
                </template>

                @if ($producto->galeria->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-1" x-show="!imagenSku">
                        @foreach ($producto->galeria as $i => $img)
                            <button type="button"
                                @click="activa = {{ $i }}"
                                class="flex-shrink-0 w-20 h-20 overflow-hidden border-2 transition-colors duration-200"
                                :class="activa === {{ $i }} ? 'border-primary' : 'border-transparent hover:border-outline-variant'">
                                <img src="{{ Storage::disk('public')->url($img->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ─── INFO ────────────────────────────────── --}}
            <div class="flex flex-col gap-8">

                @if ($producto->categorias->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($producto->categorias as $cat)
                            <a href="{{ route('categorias.show', $cat->slug) }}"
                               class="text-[10px] tracking-[0.15em] uppercase text-primary border-b border-primary/40 hover:border-primary transition-colors duration-300">
                                {{ $cat->titulo }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight">
                    {{ $producto->nombre }}
                </h1>

                {{-- Bloque reactivo --}}
                @php
                    $tieneVariaciones = $producto->variacionTipos->count() > 0
                                     && $producto->variacionSkus->count() > 0;

                    $skusData = $producto->variacionSkus->map(fn($sku) => [
                        'id'           => $sku->id,
                        'precio_venta' => (float) $sku->precio_venta,
                        'precio_lista' => $sku->precio_lista ? (float) $sku->precio_lista : null,
                        'estado'       => $sku->estado,
                        'imagen'       => $sku->imagen ? Storage::disk('public')->url($sku->imagen) : null,
                        'opciones'     => $sku->opciones->pluck('id')->values()->toArray(),
                    ])->values()->toJson();

                    $tiposData = $producto->variacionTipos->map(fn($tipo) => [
                        'id'      => $tipo->id,
                        'nombre'  => $tipo->nombre,
                        'opciones'=> $tipo->opciones->map(fn($o) => [
                            'id'     => $o->id,
                            'nombre' => $o->nombre,
                        ])->values()->toArray(),
                    ])->values()->toJson();
                @endphp

                <div
                    x-data="variacionesSelector({
                        skus:             {{ $skusData }},
                        tipos:            {{ $tiposData }},
                        precioBase:       {{ (float) $producto->precio_venta }},
                        precioListaBase:  {{ $producto->precio_lista ? (float) $producto->precio_lista : 'null' }},
                        tieneVariaciones: {{ $tieneVariaciones ? 'true' : 'false' }},
                        nombreProducto:   '{{ addslashes($producto->nombre) }}',
                        modoCarrito:      {{ $modoCarrito ? 'true' : 'false' }},
                        urlCarrito:       '{{ route('carrito.agregar') }}',
                        csrfToken:        '{{ csrf_token() }}',
                        idProducto:       {{ $producto->id }},
                        whatsapp:         '{{ $conf['whatsapp_contacto'] ?? '5215500000000' }}',
                    })"
                    class="space-y-6"
                >
                    {{-- Precio reactivo --}}
                    @if($conf['activar_tienda'] ?? false)
                        <div class="flex items-baseline gap-4">
                            <span class="font-serif text-3xl text-on-surface"
                                x-text="formatPrecio(precioActual)"></span>
                            <template x-if="precioListaActual && precioListaActual > precioActual">
                                <span class="text-lg text-outline line-through"
                                    x-text="formatPrecio(precioListaActual)"></span>
                            </template>
                            <template x-if="precioListaActual && precioListaActual > precioActual">
                                <span class="text-xs tracking-[0.1em] uppercase text-tertiary"
                                    x-text="'-' + Math.round((1 - precioActual / precioListaActual) * 100) + '% descuento'">
                                </span>
                            </template>
                        </div>
                    @endif

                    {{-- Descripción --}}
                    @if ($producto->descripcion)
                        <p class="text-on-surface-variant font-light leading-relaxed">
                            {{ $producto->descripcion }}
                        </p>
                    @endif

                    {{-- Selectores de variación --}}
                    <template x-if="tieneVariaciones">
                        <div class="space-y-5">
                            <template x-for="tipo in tipos" :key="tipo.id">
                                <div class="space-y-2">
                                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                                        <span x-text="tipo.nombre"></span>
                                        <template x-if="seleccion[tipo.id]">
                                            <span class="text-on-surface font-medium normal-case tracking-normal ml-2"
                                                  x-text="tipo.opciones.find(o => o.id === seleccion[tipo.id])?.nombre">
                                            </span>
                                        </template>
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="opcion in tipo.opciones" :key="opcion.id">
                                            <button
                                                type="button"
                                                @click="seleccionar(tipo.id, opcion.id)"
                                                :disabled="!opcionDisponible(tipo.id, opcion.id)"
                                                :class="{
                                                    'border-on-surface text-on-surface':
                                                        seleccion[tipo.id] === opcion.id,
                                                    'border-outline-variant text-on-surface-variant hover:border-outline':
                                                        seleccion[tipo.id] !== opcion.id && opcionDisponible(tipo.id, opcion.id),
                                                    'border-outline-variant/30 text-on-surface-variant/30 cursor-not-allowed line-through':
                                                        !opcionDisponible(tipo.id, opcion.id),
                                                }"
                                                class="border px-4 py-2 text-xs tracking-[0.1em] uppercase transition-all duration-200"
                                                x-text="opcion.nombre"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="seleccionCompleta && !skuActivo">
                                <p class="text-xs text-outline tracking-[0.1em] uppercase">
                                    Esta combinación no está disponible.
                                </p>
                            </template>
                        </div>
                    </template>

                    {{-- Cantidad (solo modo carrito) --}}
                    <template x-if="modoCarrito">
                        <div class="flex items-center gap-4">
                            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Cantidad</p>
                            <div class="flex items-center border border-outline-variant">
                                <button type="button"
                                    @click="cantidad = Math.max(1, cantidad - 1)"
                                    class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors">
                                    <flux:icon name="minus" class="w-3 h-3" />
                                </button>
                                <span x-text="cantidad"
                                      class="w-9 h-9 flex items-center justify-center text-sm text-on-surface border-x border-outline-variant">
                                </span>
                                <button type="button"
                                    @click="cantidad = Math.min(99, cantidad + 1)"
                                    class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors">
                                    <flux:icon name="plus" class="w-3 h-3" />
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- CTAs --}}
                    <div class="flex flex-col sm:flex-row gap-4 pt-2">

                        {{-- ── MODO CARRITO ── --}}
                        <template x-if="modoCarrito">
                            <div class="flex flex-col sm:flex-row gap-4 w-full">
                                <button
                                    type="button"
                                    @click="agregarAlCarrito()"
                                    :disabled="ctaDisabled || agregando || agregado"
                                    :class="{
                                        'opacity-40 cursor-not-allowed': ctaDisabled,
                                        'opacity-70 cursor-wait': agregando,
                                        'bg-green-700': agregado,
                                        'bg-primary hover:opacity-90': !ctaDisabled && !agregando && !agregado,
                                    }"
                                    class="flex-1 inline-flex items-center justify-center gap-2 text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase transition-all duration-300"
                                >
                                    {{-- Spinner --}}
                                    <template x-if="agregando">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                    </template>
                                    {{-- Check --}}
                                    <template x-if="agregado && !agregando">
                                        <flux:icon name="check" class="w-4 h-4" />
                                    </template>
                                    <span x-text="labelBotonCarrito"></span>
                                </button>

                                <a href="{{ route('carrito.index') }}"
                                   class="inline-flex items-center justify-center border border-outline-variant text-on-surface px-6 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface transition-all duration-300">
                                    Ver carrito
                                </a>
                            </div>
                        </template>

                        {{-- ── MODO WHATSAPP ── --}}
                        <template x-if="!modoCarrito">
                            <div class="flex flex-col sm:flex-row gap-4 w-full">
                                <a
                                    :href="ctaHref"
                                    :class="ctaDisabled ? 'opacity-40 pointer-events-none' : 'hover:opacity-90'"
                                    target="_blank"
                                    class="flex-1 inline-flex items-center justify-center gap-2 bg-primary text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase transition-all duration-300"
                                    x-text="ctaLabel"
                                ></a>
                                <a href="{{ route('visitanos') }}"
                                class="inline-flex items-center justify-center border border-outline-variant text-on-surface px-6 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface transition-all duration-300">
                                    Visítanos
                                </a>
                            </div>
                        </template>

                    </div>

                    {{-- Error carrito --}}
                    <p x-show="errorCarrito"
                       x-text="errorCarrito"
                       class="text-xs text-red-600 tracking-[0.05em]"></p>

                </div>

                {{-- Flores incluidas --}}
                @if ($producto->flores->count())
                    <div class="pt-4 border-t border-outline-variant">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">
                            Flores incluidas
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($producto->flores as $flor)
                                <a href="{{ route('directorio-floral.show', $flor->slug) }}"
                                   class="text-xs tracking-[0.1em] px-3 py-1 border border-outline-variant text-on-surface-variant hover:border-primary hover:text-primary transition-colors duration-300">
                                    {{ $flor->nombre }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Detalles --}}
                @if ($producto->detalles)
                    <div class="pt-4 border-t border-outline-variant">
                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-4">Detalles</p>
                        <div class="text-on-surface-variant font-light leading-relaxed text-sm">
                            {!! $producto->detalles !!}
                        </div>
                    </div>
                @endif

            </div>

            {{-- ─── LIGHTBOX ─────────────────────────────── --}}
            <div x-show="lightbox"
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[300] bg-black/95 flex items-center justify-center"
                 @click.self="lightbox = false; zoom = false"
                 style="display: none;">

                <button type="button"
                        @click="lightbox = false; zoom = false"
                        class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors z-10">
                    <flux:icon name="x-mark" class="w-8 h-8" />
                </button>

                <div class="relative w-full h-full flex items-center justify-center overflow-hidden"
                     :class="zoom ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                     @click="zoom = !zoom">

                    <template x-if="imagenSku">
                        <img :src="imagenSku"
                             alt="{{ $producto->nombre }}"
                             :class="zoom ? 'scale-[2]' : 'scale-100'"
                             class="max-h-screen max-w-full object-contain transition-transform duration-500 select-none">
                    </template>

                    <template x-if="!imagenSku">
                        <div class="w-full h-full flex items-center justify-center">
                            @foreach ($producto->galeria as $i => $img)
                                <img src="{{ Storage::disk('public')->url($img->imagen) }}"
                                     alt="{{ $producto->nombre }}"
                                     x-show="activa === {{ $i }}"
                                     :class="zoom ? 'scale-[2]' : 'scale-100'"
                                     class="max-h-screen max-w-full object-contain transition-transform duration-500 select-none">
                            @endforeach
                        </div>
                    </template>
                </div>

                @if($producto->galeria->count() > 1)
                    <div x-show="!imagenSku"
                         class="absolute top-6 left-1/2 -translate-x-1/2 text-white/50 text-[11px] tracking-[0.2em] uppercase">
                        <span x-text="activa + 1"></span> / {{ $producto->galeria->count() }}
                    </div>
                @endif

                @if($producto->galeria->count() > 1)
                    <button type="button" x-show="!imagenSku" @click.stop="prev()"
                            class="absolute left-6 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                        <flux:icon name="chevron-left" class="w-10 h-10" />
                    </button>
                    <button type="button" x-show="!imagenSku" @click.stop="next()"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                        <flux:icon name="chevron-right" class="w-10 h-10" />
                    </button>
                @endif

            </div>

        </div>

        {{-- Productos relacionados --}}
        @if ($relacionados->count())
            <div class="border-t border-outline-variant pt-16">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
                    También te puede interesar
                </p>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relacionados as $rel)
                        @php $portada = $rel->galeria->first(); @endphp
                        <a href="{{ route('productos.show', $rel->slug) }}" class="group block">
                            <div class="aspect-square overflow-hidden bg-surface-container-low mb-3">
                                @if ($portada)
                                    <img src="{{ Storage::disk('public')->url($portada->imagen) }}"
                                         alt="{{ $rel->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                @endif
                            </div>
                            <div class="text-center space-y-1">
                                <h3 class="font-serif text-sm text-on-surface group-hover:text-primary transition-colors duration-300">
                                    {{ $rel->nombre }}
                                </h3>
                                <p class="text-sm text-on-surface-variant">
                                    @if($rel->precio_venta > 0)
                                        ${{ number_format($rel->precio_venta, 2) }}
                                    @else
                                        <span class="italic text-on-surface-variant/60">Próximamente</span>
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('variacionesSelector', (config) => ({

            skus:             config.skus,
            tipos:            config.tipos,
            tieneVariaciones: config.tieneVariaciones,
            modoCarrito:      config.modoCarrito,
            seleccion:        {},
            cantidad:         1,
            agregando:        false,
            agregado:         false,
            errorCarrito:     '',

            get seleccionCompleta() {
                if (!this.tieneVariaciones) return true;
                return this.tipos.every(t => this.seleccion[t.id] != null);
            },

            get skuActivo() {
                if (!this.seleccionCompleta || !this.tieneVariaciones) return null;
                const opcionesSeleccionadas = Object.values(this.seleccion)
                    .map(Number).sort((a, b) => a - b);

                return this.skus.find(sku => {
                    if (!sku.estado) return false;
                    const opcionesSku = [...sku.opciones].map(Number).sort((a, b) => a - b);
                    return JSON.stringify(opcionesSku) === JSON.stringify(opcionesSeleccionadas);
                }) ?? null;
            },

            get precioActual() {
                return this.skuActivo ? this.skuActivo.precio_venta : config.precioBase;
            },

            get precioListaActual() {
                return this.skuActivo ? this.skuActivo.precio_lista : config.precioListaBase;
            },

            get ctaDisabled() {
                if (!this.tieneVariaciones) return false;
                return !this.seleccionCompleta || !this.skuActivo;
            },

            get labelBotonCarrito() {
                if (this.agregado)   return 'Agregado';
                if (this.agregando)  return 'Agregando…';
                if (this.tieneVariaciones && !this.seleccionCompleta) return 'Selecciona tus opciones';
                if (this.tieneVariaciones && this.seleccionCompleta && !this.skuActivo) return 'No disponible';
                return 'Agregar al carrito';
            },

            // Para modo WhatsApp
            get ctaLabel() {
                if (this.tieneVariaciones && !this.seleccionCompleta) return 'Selecciona tus opciones';
                if (this.tieneVariaciones && this.seleccionCompleta && !this.skuActivo) return 'No disponible';
                return 'Pre-ordena por WhatsApp';
            },

            get ctaHref() {
                if (this.ctaDisabled) return '#';
                const nombre   = config.nombreProducto;
                const precio   = this.formatPrecio(this.precioActual);
                const opciones = this.tipos.map(t => {
                    const op = t.opciones.find(o => o.id === this.seleccion[t.id]);
                    return op ? `${t.nombre}: ${op.nombre}` : '';
                }).filter(Boolean).join(', ');

                const msg = opciones
                    ? `Hola, me interesa el producto: ${nombre} (${opciones}) — ${precio}`
                    : `Hola, me interesa el producto: ${nombre} — ${precio}`;

                return `https://wa.me/${config.whatsapp}?text=${encodeURIComponent(msg)}`;
            },

            async agregarAlCarrito() {
                if (this.ctaDisabled || this.agregando || this.agregado) return;

                this.agregando    = true;
                this.errorCarrito = '';

                try {
                    const body = {
                        id_producto: config.idProducto,
                        cantidad:    this.cantidad,
                    };

                    if (this.skuActivo) {
                        body.id_sku = this.skuActivo.id;
                    }

                    const res  = await fetch(config.urlCarrito, {
                        method:  'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                        body: JSON.stringify(body),
                    });

                    const data = await res.json();

                    if (!res.ok || !data.ok) {
                        this.errorCarrito = data.message ?? 'No se pudo agregar al carrito.';
                        return;
                    }

                    this.agregado = true;

                    // Actualizar contador del nav
                    window.dispatchEvent(new CustomEvent('carrito-actualizado', {
                        detail: { total_items: data.total_items }
                    }));

                    // Resetear después de 3 segundos
                    setTimeout(() => { this.agregado = false; }, 3000);

                } catch (e) {
                    this.errorCarrito = 'Error de conexión. Intenta de nuevo.';
                } finally {
                    this.agregando = false;
                }
            },

            seleccionar(idTipo, idOpcion) {
                if (!this.opcionDisponible(idTipo, idOpcion)) return;

                if (this.seleccion[idTipo] === idOpcion) {
                    const nueva = { ...this.seleccion };
                    delete nueva[idTipo];
                    this.seleccion = nueva;
                } else {
                    this.seleccion = { ...this.seleccion, [idTipo]: idOpcion };
                }

                this.$nextTick(() => this.actualizarImagenGaleria());
            },

            actualizarImagenGaleria() {
                const sku = this.skuActivo;
                if (sku && sku.imagen) {
                    window.dispatchEvent(new CustomEvent('sku-imagen', { detail: { url: sku.imagen } }));
                } else {
                    window.dispatchEvent(new CustomEvent('sku-imagen-clear'));
                }
            },

            opcionDisponible(idTipo, idOpcion) {
                const hipotetica = { ...this.seleccion, [idTipo]: idOpcion };
                return this.skus.some(sku => {
                    if (!sku.estado) return false;
                    return Object.entries(hipotetica).every(([tipo, opcion]) =>
                        sku.opciones.map(Number).includes(Number(opcion))
                    );
                });
            },

            formatPrecio(precio) {
                if (!precio) return '';
                return '$' + Number(precio).toLocaleString('es-MX', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            },
        }));
    });
    </script>

</x-layouts::public>