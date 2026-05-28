<x-layouts::cuenta title="Datos personales">
<div class="space-y-8 max-w-lg">

    <h1 class="font-serif text-3xl text-on-surface">Datos personales</h1>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('cuenta.perfil.update') }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                class="w-full border {{ $errors->has('name') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
            @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Apellido</label>
            <input type="text" name="lastname" value="{{ old('lastname', $usuario->lastname) }}"
                class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
        </div>

        <div class="space-y-2">
            <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Correo electrónico *</label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                class="w-full border {{ $errors->has('email') ? 'border-red-400' : 'border-outline-variant' }} bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface transition-colors" />
            @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                class="bg-on-surface text-surface px-10 py-3 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
</x-layouts::cuenta>