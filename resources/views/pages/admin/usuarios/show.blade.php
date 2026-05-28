<x-layouts::app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <x-admin.page-header
            titulo="{{ $usuario->name }} {{ $usuario->lastname }}"
            :breadcrumbs="[
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Usuarios', 'route' => 'admin.usuarios.index'],
                ['label' => $usuario->name],
            ]"
        >
            <x-slot:action>
                <flux:button href="{{ route('admin.usuarios.edit', $usuario) }}" variant="primary" size="sm">
                    Editar datos
                </flux:button>
            </x-slot:action>
        </x-admin.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Datos personales --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-4">
                    <flux:heading size="sm" class="text-zinc-700 border-b border-zinc-100 pb-2">
                        Datos personales
                    </flux:heading>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase tracking-wide">Nombre</dt>
                            <dd class="text-zinc-800 font-medium">{{ $usuario->name }} {{ $usuario->lastname }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase tracking-wide">Email</dt>
                            <dd class="text-zinc-800">{{ $usuario->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase tracking-wide">Rol</dt>
                            <dd>
                                <flux:badge color="{{ $usuario->role === 'admin' ? 'amber' : 'zinc' }}" size="sm">
                                    {{ $usuario->role === 'admin' ? 'Administrador' : 'Usuario' }}
                                </flux:badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase tracking-wide">Registro</dt>
                            <dd class="text-zinc-600">{{ $usuario->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase tracking-wide">Email verificado</dt>
                            <dd>
                                @if($usuario->email_verified_at)
                                    <span class="text-green-600 text-xs">✓ Verificado {{ $usuario->email_verified_at->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-red-500 text-xs">✗ No verificado</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Direcciones --}}
                @if($usuario->direcciones->count())
                    <div class="bg-white border border-zinc-200 rounded-sm p-5 space-y-3">
                        <flux:heading size="sm" class="text-zinc-700 border-b border-zinc-100 pb-2">
                            Direcciones ({{ $usuario->direcciones->count() }})
                        </flux:heading>
                        @foreach($usuario->direcciones as $dir)
                            <div class="text-sm text-zinc-600 space-y-0.5">
                                <p class="font-medium text-zinc-800">
                                    {{ $dir->nombre }}
                                    @if($dir->predeterminada)
                                        <flux:badge color="blue" size="sm" class="ml-1">Principal</flux:badge>
                                    @endif
                                </p>
                                <p>{{ $dir->calle }} {{ $dir->numero_ext }}{{ $dir->numero_int ? ' Int. '.$dir->numero_int : '' }}</p>
                                <p>{{ $dir->colonia }}, CP {{ $dir->cp }}</p>
                                <p>{{ $dir->telefono }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Acciones destructivas --}}
                @if($usuario->id !== auth()->id())
                    <div class="bg-white border border-red-100 rounded-sm p-5">
                        <flux:heading size="sm" class="text-red-600 border-b border-red-100 pb-2 mb-3">
                            Zona de peligro
                        </flux:heading>
                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                            onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full text-sm text-red-600 border border-red-200 rounded-sm px-3 py-2 hover:bg-red-50 transition-colors">
                                Eliminar usuario
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Pedidos --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-zinc-200 rounded-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-zinc-100 flex items-center justify-between">
                        <flux:heading size="sm" class="text-zinc-700">
                            Pedidos ({{ $pedidos->count() }})
                        </flux:heading>
                    </div>
                    @if($pedidos->count())
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 text-zinc-500 uppercase text-xs tracking-wide">
                                <tr>
                                    <th class="px-4 py-3 text-left">Número</th>
                                    <th class="px-4 py-3 text-left">Fecha entrega</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($pedidos as $pedido)
                                    <tr class="hover:bg-zinc-50 transition-colors">
                                        <td class="px-4 py-3 font-medium text-zinc-800">{{ $pedido->numero }}</td>
                                        <td class="px-4 py-3 text-zinc-500">
                                            {{ $pedido->fecha_entrega?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <flux:badge color="{{ $pedido->estado_color }}" size="sm">
                                                {{ $pedido->estado_label }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-3 text-right text-zinc-800">
                                            ${{ number_format($pedido->total, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <flux:button href="{{ route('admin.pedidos.show', $pedido) }}" size="sm" variant="ghost">
                                                Ver
                                            </flux:button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="px-5 py-10 text-center text-zinc-400 text-sm">Este usuario no tiene pedidos aún.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-layouts::app>