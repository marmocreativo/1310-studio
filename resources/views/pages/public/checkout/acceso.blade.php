<x-layouts::public title="Checkout">

    <div class="py-16 px-8 max-w-[1440px] mx-auto">

        {{-- Breadcrumb --}}
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
            <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
            <span class="mx-2">·</span>
            <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
            <span class="mx-2">·</span>
            Checkout
        </p>

        <h1 class="font-serif text-4xl md:text-5xl text-on-surface leading-tight mb-4">
            ¿Cómo deseas continuar?
        </h1>
        <p class="text-on-surface-variant font-light mb-16 max-w-lg">
            Puedes completar tu compra sin crear una cuenta, o iniciar sesión para un proceso más rápido y hacer seguimiento de tus pedidos.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">

            {{-- Invitado --}}
            <div class="border border-outline-variant p-10 flex flex-col gap-6">
                <div class="space-y-2">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Sin cuenta</p>
                    <h2 class="font-serif text-2xl text-on-surface">Continuar como invitado</h2>
                    <p class="text-sm text-on-surface-variant font-light leading-relaxed">
                        Completa tu compra rápidamente sin registrarte. Solo necesitarás tu correo para recibir los detalles.
                    </p>
                </div>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                        <flux:icon name="check" class="w-4 h-4 text-on-surface shrink-0" />
                        Proceso rápido
                    </li>
                    <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                        <flux:icon name="check" class="w-4 h-4 text-on-surface shrink-0" />
                        Confirmación por correo
                    </li>
                    <li class="flex items-center gap-2 text-sm text-on-surface-variant opacity-40">
                        <flux:icon name="x-mark" class="w-4 h-4 shrink-0" />
                        Sin historial de pedidos
                    </li>
                </ul>
                <form method="POST" action="{{ route('checkout.invitado') }}" class="mt-auto">
                    @csrf
                    <button
                        type="submit"
                        class="w-full border border-on-surface text-on-surface px-8 py-4 text-xs tracking-[0.3em] uppercase hover:bg-on-surface hover:text-surface transition-all duration-300"
                    >
                        Continuar sin cuenta
                    </button>
                </form>
            </div>

            {{-- Cuenta --}}
            <div class="border border-on-surface bg-surface-container-low p-10 flex flex-col gap-6">
                <div class="space-y-2">
                    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant">Con cuenta</p>
                    <h2 class="font-serif text-2xl text-on-surface">Iniciar sesión</h2>
                    <p class="text-sm text-on-surface-variant font-light leading-relaxed">
                        Accede a tu cuenta para un checkout más rápido con tus datos guardados y consulta el historial de tus pedidos.
                    </p>
                </div>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                        <flux:icon name="check" class="w-4 h-4 text-on-surface shrink-0" />
                        Datos prellenados
                    </li>
                    <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                        <flux:icon name="check" class="w-4 h-4 text-on-surface shrink-0" />
                        Historial de pedidos
                    </li>
                    <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                        <flux:icon name="check" class="w-4 h-4 text-on-surface shrink-0" />
                        Proceso más rápido
                    </li>
                </ul>
                <div class="mt-auto flex flex-col gap-3">
                    <a
                        href="{{ route('login') }}"
                        class="block w-full bg-on-surface text-surface px-8 py-4 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-all duration-300 text-center"
                    >
                        Iniciar sesión
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="block w-full border border-outline-variant text-on-surface-variant px-8 py-4 text-xs tracking-[0.3em] uppercase hover:border-on-surface hover:text-on-surface transition-all duration-300 text-center"
                    >
                        Crear cuenta nueva
                    </a>
                </div>
            </div>

        </div>

    </div>

</x-layouts::public>