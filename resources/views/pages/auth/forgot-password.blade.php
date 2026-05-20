<x-layouts::auth :title="__('Olvidaste tu contraseña')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Olvidaste tu contraseña')" :description="__('Ingresa tu correo para obtener un link de reinicio para tu contraseña')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email')"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                {{ __('Enviar link de reinicio') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('Volver a intentarlo') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Inicio de sesión') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
