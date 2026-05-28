<x-layouts::cuenta title="Contraseña">
<div class="space-y-8 max-w-lg">

    <h1 class="font-serif text-3xl text-on-surface">Cambiar contraseña</h1>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('cuenta.password.update') }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Contraseña actual *</label>
            <input type="password" name="actual" required
                class="w-full border {{ $errors->has('actual') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
            @error('actual') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nueva contraseña *</label>
            <input type="password" name="nueva" required minlength="8"
                class="w-full border {{ $errors->has('nueva') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
            @error('nueva') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Confirmar nueva contraseña *</label>
            <input type="password" name="nueva_confirmation" required minlength="8"
                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="bg-on-surface text-surface px-10 py-3 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
                Actualizar contraseña
            </button>
        </div>
    </form>
</div>
</x-layouts::cuenta>