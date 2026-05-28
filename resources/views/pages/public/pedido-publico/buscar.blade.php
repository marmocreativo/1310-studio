<x-layouts::public title="Consultar pedido">
<div class="py-24 px-8 max-w-lg mx-auto">

    <div class="space-y-2 mb-12">
        <h1 class="font-serif text-4xl text-on-surface">Consultar pedido</h1>
        <p class="text-on-surface-variant font-light">Ingresa tu número de pedido y correo electrónico para ver el estado de tu compra.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 px-5 py-3">
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('pedido-publico.show') }}" class="space-y-5">
        @csrf

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Número de pedido *</label>
            <input type="text" name="numero" value="{{ old('numero') }}" required
                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                placeholder="1310-XXXXXX" />
        </div>

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Correo electrónico *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                placeholder="correo@ejemplo.com" />
        </div>

        <button type="submit"
            class="w-full bg-on-surface text-surface px-8 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
            Consultar pedido
        </button>

        <p class="text-center text-xs text-on-surface-variant">
            ¿Tienes una cuenta?
            <a href="{{ route('login') }}" class="underline hover:text-on-surface transition-colors">Inicia sesión</a>
            para ver todos tus pedidos.
        </p>
    </form>
</div>
</x-layouts::public>