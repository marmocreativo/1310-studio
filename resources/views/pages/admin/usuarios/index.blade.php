<x-layouts::app>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <x-admin.page-header
            titulo="Usuarios"
            :breadcrumbs="[['label' => 'Dashboard', 'route' => 'admin.dashboard'], ['label' => 'Usuarios']]"
        />

        {{-- Filtros --}}
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <flux:input
                    name="busqueda"
                    placeholder="Buscar por nombre o email…"
                    value="{{ request('busqueda') }}"
                />
            </div>
            <div>
                <flux:select name="rol" placeholder="Todos los roles">
                    <flux:select.option value="">Todos los roles</flux:select.option>
                    <flux:select.option value="user" :selected="request('rol') === 'user'">Usuario</flux:select.option>
                    <flux:select.option value="admin" :selected="request('rol') === 'admin'">Administrador</flux:select.option>
                </flux:select>
            </div>
            <flux:button type="submit" variant="primary">Filtrar</flux:button>
            @if(request()->hasAny(['busqueda', 'rol']))
                <flux:button href="{{ route('admin.usuarios.index') }}" variant="ghost">Limpiar</flux:button>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white border border-zinc-200 rounded-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-center">Pedidos</th>
                        <th class="px-4 py-3 text-center">Registro</th>
                        <th class="px-4 py-3 text-center">Rol</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-zinc-50 transition-colors" wire:key="u-{{ $usuario->id }}">
                            <td class="px-4 py-3 font-medium text-zinc-800">
                                {{ $usuario->name }} {{ $usuario->lastname }}
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ $usuario->email }}</td>
                            <td class="px-4 py-3 text-center text-zinc-600">{{ $usuario->pedidos_count }}</td>
                            <td class="px-4 py-3 text-center text-zinc-400 text-xs">
                                {{ $usuario->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($usuario->id !== auth()->id())
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border transition-colors cursor-pointer
                                            {{ $usuario->role === 'admin'
                                                ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'
                                                : 'bg-zinc-100 text-zinc-600 border-zinc-200 hover:bg-zinc-200' }}"
                                        x-data
                                        @click="
                                            $el.disabled = true;
                                            fetch('{{ route('admin.usuarios.toggle-rol', $usuario) }}', {
                                                method: 'PATCH',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json'
                                                }
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                if (data.ok) window.location.reload();
                                            })
                                            .finally(() => $el.disabled = false);
                                        "
                                    >
                                        @if($usuario->role === 'admin')
                                            <flux:icon.shield-check class="size-3" /> Admin
                                        @else
                                            <flux:icon.user class="size-3" /> Usuario
                                        @endif
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <flux:icon.shield-check class="size-3" /> Admin (tú)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('admin.usuarios.show', $usuario) }}" size="sm" variant="ghost">
                                        Ver
                                    </flux:button>
                                    <flux:button href="{{ route('admin.usuarios.edit', $usuario) }}" size="sm" variant="ghost">
                                        Editar
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-zinc-400">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
            <div class="mt-2">
                {{ $usuarios->links() }}
            </div>
        @endif

    </div>
</x-layouts::app>