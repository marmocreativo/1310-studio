<x-layouts::app :title="__('Nuevo Taller')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 max-w-2xl">

        <div class="flex items-center gap-4">
            <flux:button href="{{ route('admin.talleres.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">Nuevo Taller</flux:heading>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li><flux:text class="text-red-700 dark:text-red-400 text-sm">{{ $error }}</flux:text></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.talleres.store') }}"
              enctype="multipart/form-data"
              class="flex flex-col gap-6 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @csrf

            <flux:field>
                <flux:label>Nombre *</flux:label>
                <flux:input name="nombre" value="{{ old('nombre') }}" required />
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label>Detalles</flux:label>
                <flux:textarea name="detalles" rows="6" placeholder="Descripción, temario, requisitos...">{{ old('detalles') }}</flux:textarea>
                <flux:error name="detalles" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Fecha y hora</flux:label>
                    <flux:input type="datetime-local" name="fecha" value="{{ old('fecha') }}" />
                    <flux:error name="fecha" />
                </flux:field>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select name="estado">
                        <flux:select.option value="1" :selected="old('estado', '1') === '1'">Activo</flux:select.option>
                        <flux:select.option value="0" :selected="old('estado') === '0'">Inactivo</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Imagen</flux:label>
                <input type="file" name="imagen" accept="image/*"
                       class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-300">
                <flux:error name="imagen" />
            </flux:field>

            <div class="flex gap-3 pt-2">
                <flux:button type="submit" variant="primary">Crear taller</flux:button>
                <flux:button href="{{ route('admin.talleres.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>