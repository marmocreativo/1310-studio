<x-layouts::public title="Mi carrito">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
            <span class="mx-2">·</span>
            Carrito
        </p>

        <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight mb-12">
            Tu carrito
        </h1>

        @if ($carrito->esta_vacio)
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 border border-outline-variant flex items-center justify-center mb-6">
                    <flux:icon name="shopping-bag" class="w-8 h-8 text-outline-variant" />
                </div>
                <p class="font-serif text-2xl text-on-surface mb-3">Tu carrito está vacío</p>
                <p class="text-on-surface-variant font-light mb-8 max-w-sm">
                    Explora nuestra colección y encuentra el arreglo perfecto.
                </p>
                <a href="{{ route('categorias.index') }}"
                   class="inline-block bg-primary text-on-primary px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300">
                    Ver colección
                </a>
            </div>

        @else

            <div
                class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start"
                x-data="carritoManager({
                    urlActualizar: '{{ url('carrito/actualizar') }}',
                    urlEliminar:   '{{ url('carrito/eliminar') }}',
                    urlVaciar:     '{{ route('carrito.vaciar') }}',
                    csrfToken:     '{{ csrf_token() }}',
                })"
            >

                {{-- ─── ITEMS ────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-px">

                    {{-- Header --}}
                    <div class="hidden sm:grid grid-cols-12 gap-4 pb-4 border-b border-outline-variant">
                        <div class="col-span-6">
                            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Producto</p>
                        </div>
                        <div class="col-span-2 text-center">
                            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Precio</p>
                        </div>
                        <div class="col-span-2 text-center">
                            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Cantidad</p>
                        </div>
                        <div class="col-span-2 text-right">
                            <p class="text-[10px] tracking-[0.2em] uppercase text-on-surface-variant">Total</p>
                        </div>
                    </div>

                    @foreach ($carrito->items as $item)
                        <div
                            class="grid grid-cols-12 gap-4 py-6 border-b border-outline-variant/50 transition-opacity duration-300"
                            x-bind:class="eliminando === {{ $item->id }} ? 'opacity-30 pointer-events-none' : ''"
                            id="item-{{ $item->id }}"
                        >
                            {{-- Imagen + Info --}}
                            <div class="col-span-12 sm:col-span-6 flex gap-4">
                                <div class="w-20 h-20 shrink-0 overflow-hidden bg-surface-container-low">
                                    @if ($item->imagen_url)
                                        <img src="{{ $item->imagen_url }}"
                                             alt="{{ $item->nombre_snapshot }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <flux:icon name="photo" class="w-6 h-6 text-outline-variant" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col justify-center gap-1">
                                    <p class="font-serif text-base text-on-surface leading-tight">
                                        {{ $item->nombre_snapshot }}
                                    </p>
                                    @if ($item->opciones_snapshot)
                                        <div class="space-y-0.5">
                                            @foreach ($item->opciones_snapshot as $opcion)
                                                <p class="text-[11px] tracking-[0.05em] text-on-surface-variant">
                                                    {{ $opcion['tipo'] }}: {{ $opcion['opcion'] }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                    {{-- Eliminar (mobile) --}}
                                    <button
                                        type="button"
                                        @click="eliminarItem({{ $item->id }})"
                                        class="sm:hidden mt-1 text-[10px] tracking-[0.1em] uppercase text-outline hover:text-red-500 transition-colors text-left underline">
                                        Eliminar
                                    </button>
                                </div>
                            </div>

                            {{-- Precio unitario --}}
                            <div class="hidden sm:flex col-span-2 items-center justify-center">
                                <p class="text-sm text-on-surface-variant">
                                    ${{ number_format($item->precio_snapshot, 2) }}
                                </p>
                            </div>

                            {{-- Cantidad --}}
                            <div class="col-span-7 sm:col-span-2 flex items-center justify-start sm:justify-center">
                                <div class="flex items-center border border-outline-variant">
                                    <button
                                        type="button"
                                        @click="cambiarCantidad({{ $item->id }}, {{ $item->cantidad - 1 }})"
                                        :disabled="actualizando === {{ $item->id }}"
                                        class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors disabled:opacity-40">
                                        <flux:icon name="minus" class="w-3 h-3" />
                                    </button>
                                    <span
                                        class="w-8 h-8 flex items-center justify-center text-sm text-on-surface border-x border-outline-variant"
                                        id="cantidad-{{ $item->id }}">
                                        {{ $item->cantidad }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="cambiarCantidad({{ $item->id }}, {{ $item->cantidad + 1 }})"
                                        :disabled="actualizando === {{ $item->id }}"
                                        class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors disabled:opacity-40">
                                        <flux:icon name="plus" class="w-3 h-3" />
                                    </button>
                                </div>
                            </div>

                            {{-- Subtotal + eliminar desktop --}}
                            <div class="col-span-5 sm:col-span-2 flex items-center justify-end gap-3">
                                <p class="text-sm font-medium text-on-surface"
                                   id="subtotal-{{ $item->id }}">
                                    ${{ number_format($item->subtotal, 2) }}
                                </p>
                                <button
                                    type="button"
                                    @click="eliminarItem({{ $item->id }})"
                                    class="hidden sm:flex w-6 h-6 items-center justify-center text-outline hover:text-red-500 transition-colors">
                                    <flux:icon name="x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- Acciones del carrito --}}
                    <div class="flex items-center justify-between pt-6">
                        <button
                            type="button"
                            @click="vaciarCarrito()"
                            :disabled="vaciando"
                            class="text-[10px] tracking-[0.1em] uppercase text-outline hover:text-red-500 transition-colors underline disabled:opacity-40">
                            Vaciar carrito
                        </button>
                        <a href="{{ route('categorias.index') }}"
                           class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant hover:text-on-surface transition-colors">
                            ← Seguir comprando
                        </a>
                    </div>

                    {{-- Error --}}
                    <p x-show="error" x-text="error" class="text-xs text-red-600 pt-2"></p>
                </div>

                {{-- ─── RESUMEN ──────────────────────────── --}}
                <div class="lg:col-span-1">
                    <div class="border border-outline-variant p-8 space-y-6 sticky top-8">

                        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">
                            Resumen del pedido
                        </p>

                        <div class="space-y-3">
                            <div class="flex justify-between items-baseline">
                                <span class="text-sm text-on-surface-variant font-light">
                                    Subtotal
                                    <span class="text-[11px]">({{ $carrito->total_items }} {{ $carrito->total_items === 1 ? 'artículo' : 'artículos' }})</span>
                                </span>
                                <span class="text-sm text-on-surface font-medium"
                                      id="total-carrito">
                                    ${{ number_format($carrito->subtotal, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-baseline">
                                <span class="text-sm text-on-surface-variant font-light">Envío</span>
                                <span class="text-[11px] tracking-[0.05em] text-on-surface-variant italic">
                                    Se calcula en el checkout
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-outline-variant pt-4 flex justify-between items-baseline">
                            <span class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Total</span>
                            <span class="font-serif text-2xl text-on-surface"
                                  id="total-final">
                                ${{ number_format($carrito->subtotal, 2) }}
                            </span>
                        </div>

                        <a href="{{ route('checkout.index') }}"
                           class="block w-full bg-primary text-on-primary px-6 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-90 transition-all duration-300 text-center">
                            Proceder al pago
                        </a>

                        <p class="text-[10px] tracking-[0.05em] text-on-surface-variant text-center leading-relaxed">
                            Pago seguro · Tarjeta, efectivo o contra entrega
                        </p>
                    </div>
                </div>

            </div>
        @endif

    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('carritoManager', (config) => ({

            actualizando: null,
            eliminando:   null,
            vaciando:     false,
            error:        '',

            async cambiarCantidad(itemId, nuevaCantidad) {
                if (nuevaCantidad < 1) {
                    return this.eliminarItem(itemId);
                }
                if (nuevaCantidad > 99) return;

                this.actualizando = itemId;
                this.error        = '';

                try {
                    const res  = await fetch(`${config.urlActualizar}/${itemId}`, {
                        method:  'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                        body: JSON.stringify({ cantidad: nuevaCantidad }),
                    });

                    const data = await res.json();
                    if (!res.ok) { this.error = data.message ?? 'Error al actualizar.'; return; }

                    // Actualizar DOM
                    const elCantidad  = document.getElementById(`cantidad-${itemId}`);
                    const elSubtotal  = document.getElementById(`subtotal-${itemId}`);
                    const elTotal     = document.getElementById('total-carrito');
                    const elFinal     = document.getElementById('total-final');

                    if (elCantidad) elCantidad.textContent = nuevaCantidad;
                    if (elSubtotal) elSubtotal.textContent = `$${data.subtotal}`;
                    if (elTotal)    elTotal.textContent    = `$${data.total}`;
                    if (elFinal)    elFinal.textContent    = `$${data.total}`;

                } catch (e) {
                    this.error = 'Error de conexión.';
                } finally {
                    this.actualizando = null;
                }
            },

            async eliminarItem(itemId) {
                this.eliminando = itemId;
                this.error      = '';

                try {
                    const res  = await fetch(`${config.urlEliminar}/${itemId}`, {
                        method:  'DELETE',
                        headers: {
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                    });

                    const data = await res.json();
                    if (!res.ok) { this.error = data.message ?? 'Error al eliminar.'; return; }

                    // Eliminar fila del DOM
                    const el = document.getElementById(`item-${itemId}`);
                    if (el) el.remove();

                    // Actualizar totales
                    const elTotal = document.getElementById('total-carrito');
                    const elFinal = document.getElementById('total-final');
                    if (elTotal) elTotal.textContent = `$${data.total}`;
                    if (elFinal) elFinal.textContent = `$${data.total}`;

                    // Actualizar contador nav
                    window.dispatchEvent(new CustomEvent('carrito-actualizado', {
                        detail: { total_items: data.total_items }
                    }));

                    // Si quedó vacío recargar para mostrar empty state
                    if (data.vacio) {
                        window.location.reload();
                    }

                } catch (e) {
                    this.error = 'Error de conexión.';
                } finally {
                    this.eliminando = null;
                }
            },

            async vaciarCarrito() {
                if (!confirm('¿Vaciar todo el carrito?')) return;

                this.vaciando = true;
                this.error    = '';

                try {
                    const res = await fetch(config.urlVaciar, {
                        method:  'DELETE',
                        headers: {
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                    });

                    if (res.ok) {
                        window.location.reload();
                    }
                } catch (e) {
                    this.error   = 'Error de conexión.';
                    this.vaciando = false;
                }
            },
        }));
    });
    </script>

</x-layouts::public>