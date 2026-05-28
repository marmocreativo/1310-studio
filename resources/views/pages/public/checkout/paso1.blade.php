<x-layouts::public title="Checkout — Paso 1">
<div class="py-16 px-8 max-w-[800px] mx-auto">

    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
        <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
        <span class="mx-2">·</span>
        <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
        <span class="mx-2">·</span>
        Checkout
    </p>

    @include('pages.public.checkout.partials.pasos', ['pasoActual' => 1])

    <h1 class="font-serif text-3xl text-on-surface mb-8">Datos de contacto</h1>

    @if($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-700">Revisa los campos marcados en rojo.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.paso1.store') }}"
          x-data="{ esRegalo: {{ old('es_regalo', $datos['es_regalo'] ?? false) ? 'true' : 'false' }} }"
          class="space-y-8">
        @csrf

        {{-- Datos del comprador --}}
        <div class="space-y-5">
            <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
                Quien realiza el pedido
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre completo *</label>
                    <input type="text" name="nombre"
                        value="{{ old('nombre', $datos['nombre'] ?? $usuario?->name) }}"
                        required autofocus
                        class="w-full border {{ $errors->has('nombre') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                        placeholder="Tu nombre completo" />
                    @error('nombre') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Correo electrónico *</label>
                    <input type="email" name="email"
                        value="{{ old('email', $datos['email'] ?? $usuario?->email) }}"
                        required
                        class="w-full border {{ $errors->has('email') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                        placeholder="correo@ejemplo.com" />
                    @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Teléfono *</label>
                    <input type="tel" name="telefono"
                        value="{{ old('telefono', $datos['telefono'] ?? '') }}"
                        required
                        class="w-full border {{ $errors->has('telefono') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                        placeholder="10 dígitos" />
                    @error('telefono') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Destinatario --}}
        <div class="space-y-5">
            <div class="flex items-center justify-between border-b border-outline-variant pb-3">
                <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">¿Es un regalo?</p>
                <button type="button" @click="esRegalo = !esRegalo"
                    class="text-[10px] tracking-[0.1em] uppercase text-on-surface-variant hover:text-on-surface transition-colors underline">
                    <span x-text="esRegalo ? 'Cancelar' : 'Agregar destinatario'"></span>
                </button>
            </div>

            <input type="hidden" name="es_regalo" :value="esRegalo ? 1 : 0">

            <div x-show="esRegalo" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <p class="sm:col-span-2 text-xs text-on-surface-variant font-light">
                    Ingresa los datos de quien recibirá el pedido.
                </p>
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre del destinatario</label>
                    <input type="text" name="destinatario_nombre"
                        value="{{ old('destinatario_nombre', $datos['destinatario_nombre'] ?? '') }}"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                        placeholder="Nombre de quien recibe" />
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Teléfono del destinatario</label>
                    <input type="tel" name="destinatario_telefono"
                        value="{{ old('destinatario_telefono', $datos['destinatario_telefono'] ?? '') }}"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                        placeholder="10 dígitos" />
                </div>
                <div class="space-y-2 sm:col-span-2">
                    <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Mensaje para la tarjeta</label>
                    <textarea name="mensaje_tarjeta" rows="3" maxlength="500"
                        class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors resize-none"
                        placeholder="Tu mensaje para acompañar el arreglo…">{{ old('mensaje_tarjeta', $datos['mensaje_tarjeta'] ?? '') }}</textarea>
                    <p class="text-[10px] text-on-surface-variant text-right">Máximo 500 caracteres</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit"
                class="bg-on-surface text-surface px-10 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
                Siguiente
            </button>
        </div>
    </form>
</div>
</x-layouts::public>