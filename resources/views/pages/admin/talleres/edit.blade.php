<x-layouts::app :title="__('Editar Taller')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 max-w-2xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.talleres.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Editar: {{ $taller->nombre }}</flux:heading>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 py-3">
                <flux:text class="text-green-700 dark:text-green-400">{{ session('success') }}</flux:text>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li><flux:text class="text-red-700 dark:text-red-400 text-sm">{{ $error }}</flux:text></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.talleres.update', $taller) }}"
              enctype="multipart/form-data"
              class="flex flex-col gap-6 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @csrf
            @method('PUT')

            <flux:field>
                <flux:label>Nombre *</flux:label>
                <flux:input name="nombre" value="{{ old('nombre', $taller->nombre) }}" required />
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label>Detalles</flux:label>
                <flux:textarea name="detalles" rows="6">{{ old('detalles', $taller->detalles) }}</flux:textarea>
                <flux:error name="detalles" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Fecha y hora</flux:label>
                    <flux:input type="datetime-local" name="fecha"
                                value="{{ old('fecha', $taller->fecha?->format('Y-m-d\TH:i')) }}" />
                    <flux:error name="fecha" />
                </flux:field>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="1" :selected="old('estado', $taller->estado) == 1">Activo</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado', $taller->estado) == 0">Inactivo</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Imagen</flux:label>
                @if ($taller->imagen)
                    <div class="mb-3">
                        <img src="{{ Storage::url($taller->imagen) }}"
                             alt="{{ $taller->nombre }}"
                             class="w-32 h-32 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <flux:text class="text-xs text-zinc-400 mt-1">Imagen actual — sube una nueva para reemplazarla</flux:text>
                    </div>
                @endif
                <input type="file" name="imagen" accept="image/*"
                       class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-300">
                <flux:error name="imagen" />
            </flux:field>

            <div class="flex gap-3 pt-2">
                <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
                <flux:button href="{{ route('admin.talleres.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>