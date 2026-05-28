<x-layouts::app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <x-admin.page-header
            titulo="Editar usuario"
            :breadcrumbs="[
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Usuarios', 'route' => 'admin.usuarios.index'],
                ['label' => $usuario->name, 'route' => 'admin.usuarios.show', 'param' => $usuario->id],
                ['label' => 'Editar'],
            ]"
        />

        <form
            id="form-usuario"
            method="POST"
            action="{{ route('admin.usuarios.update', $usuario) }}"
        >
            @csrf
            @method('PUT')

            <div class="flex gap-6 items-start">

                {{-- Contenido principal --}}
                <div class="flex-1 space-y-4">
                    <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-4">
                        <flux:heading size="sm" class="text-zinc-700 border-b border-zinc-100 pb-2">
                            Datos personales
                        </flux:heading>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Nombre <flux:error name="name" /></flux:label>
                                <flux:input
                                    name="name"
                                    value="{{ old('name', $usuario->name) }}"
                                    required
                                    autocomplete="off"
                                />
                            </flux:field>

                            <flux:field>
                                <flux:label>Apellido <flux:error name="lastname" /></flux:label>
                                <flux:input
                                    name="lastname"
                                    value="{{ old('lastname', $usuario->lastname) }}"
                                    autocomplete="off"
                                />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>Correo electrónico <flux:error name="email" /></flux:label>
                            <flux:input
                                name="email"
                                type="email"
                                value="{{ old('email', $usuario->email) }}"
                                required
                                autocomplete="off"
                            />
                        </flux:field>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="w-72 space-y-4">
                    <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-4">
                        <flux:heading size="sm" class="text-zinc-700 border-b border-zinc-100 pb-2">
                            Publicación
                        </flux:heading>

                        <div class="flex flex-col gap-2">
                            <button type="submit" form="form-usuario"
                                class="w-full bg-[#927F64] hover:bg-[#7a6a53] text-white text-sm font-medium py-2 px-4 rounded-sm transition-colors">
                                Guardar cambios
                            </button>
                            <flux:button href="{{ route('admin.usuarios.show', $usuario) }}" variant="ghost" class="w-full">
                                Cancelar
                            </flux:button>
                        </div>
                    </div>

                    <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-2 text-sm text-zinc-500">
                        <flux:heading size="sm" class="text-zinc-700 border-b border-zinc-100 pb-2">
                            Info
                        </flux:heading>
                        <p>Registrado: {{ $usuario->created_at->format('d/m/Y') }}</p>
                        <p>Rol actual:
                            <flux:badge color="{{ $usuario->role === 'admin' ? 'amber' : 'zinc' }}" size="sm">
                                {{ $usuario->role === 'admin' ? 'Administrador' : 'Usuario' }}
                            </flux:badge>
                        </p>
                        <p class="text-xs text-zinc-400">El rol se cambia desde el listado de usuarios.</p>
                    </div>
                </div>

            </div>
        </form>

    </div>
</x-layouts::app>