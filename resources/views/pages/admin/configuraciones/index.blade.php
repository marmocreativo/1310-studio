<x-layouts::app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <x-admin.page-header
            titulo="Configuraciones"
            :breadcrumbs="[['label' => 'Dashboard', 'route' => 'admin.dashboard'], ['label' => 'Configuraciones']]"
        />

        {{-- Tabs de grupos --}}
        <div class="flex gap-1 border-b border-zinc-200">
            @foreach($grupos as $grupo)
                <a href="{{ route('admin.configuraciones.index', ['grupo' => $grupo]) }}"
                    class="px-4 py-2 text-sm font-medium capitalize transition-colors border-b-2 -mb-px
                        {{ $grupoActivo === $grupo
                            ? 'border-[#927F64] text-[#927F64]'
                            : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ $grupo }}
                </a>
            @endforeach
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Listado de configuraciones --}}
        <div class="space-y-3">
            @forelse($configuraciones as $config)
                <form method="POST" action="{{ route('admin.configuraciones.update', $config) }}"
                    class="bg-white border border-zinc-200 rounded-sm p-4 flex flex-col sm:flex-row gap-4 items-start">
                    @csrf
                    @method('PATCH')

                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-mono text-zinc-400 mb-1">{{ $config->nombre_conf }}</p>

                        {{-- Booleanos --}}
                        @if($config->contenido_conf === 'true' || $config->contenido_conf === 'false')
                            <div class="flex items-center gap-3 mt-2">
                                <input type="hidden" name="contenido_conf" value="false">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox"
                                        name="contenido_conf"
                                        value="true"
                                        {{ $config->contenido_conf === 'true' ? 'checked' : '' }}
                                        class="w-4 h-4 accent-[#927F64]"
                                        onchange="
                                            var hidden = this.previousElementSibling;
                                            hidden.disabled = this.checked;
                                            this.closest('form').submit();
                                        ">
                                    <span class="text-sm text-zinc-700">
                                        {{ $config->contenido_conf === 'true' ? 'Activado' : 'Desactivado' }}
                                    </span>
                                </label>
                            </div>

                        {{-- JSON --}}
                        @elseif(str_starts_with(trim($config->contenido_conf ?? ''), '[') || str_starts_with(trim($config->contenido_conf ?? ''), '{'))
                            <textarea
                                name="contenido_conf"
                                rows="6"
                                class="w-full text-sm font-mono border border-zinc-200 rounded-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#927F64]"
                            >{{ $config->contenido_conf }}</textarea>

                        {{-- Texto largo --}}
                        @elseif(strlen($config->contenido_conf ?? '') > 80)
                            <textarea
                                name="contenido_conf"
                                rows="3"
                                class="w-full text-sm border border-zinc-200 rounded-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#927F64]"
                            >{{ $config->contenido_conf }}</textarea>

                        {{-- String / número --}}
                        @else
                            <input
                                type="text"
                                name="contenido_conf"
                                value="{{ $config->contenido_conf }}"
                                class="w-full text-sm border border-zinc-200 rounded-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#927F64]"
                            >
                        @endif
                    </div>

                    <div class="flex gap-2 shrink-0 pt-5">
                        @if($config->contenido_conf !== 'true' && $config->contenido_conf !== 'false')
                            <button type="submit"
                                class="text-sm bg-[#927F64] hover:bg-[#7a6a53] text-white px-3 py-1.5 rounded-sm transition-colors">
                                Guardar
                            </button>
                        @endif
                        <form method="POST" action="{{ route('admin.configuraciones.destroy', $config) }}"
                            onsubmit="return confirm('¿Eliminar la configuración {{ $config->nombre_conf }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-sm text-red-500 hover:text-red-700 px-3 py-1.5 rounded-sm transition-colors border border-red-100 hover:border-red-300">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </form>
            @empty
                <p class="text-sm text-zinc-400 text-center py-10">No hay configuraciones en este grupo.</p>
            @endforelse
        </div>

        {{-- Agregar nueva --}}
        <div class="bg-white border border-zinc-200 rounded-sm p-5">
            <flux:heading size="sm" class="text-zinc-700 border-b border-zinc-100 pb-2 mb-4">
                Nueva configuración
            </flux:heading>
            <form method="POST" action="{{ route('admin.configuraciones.store') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <flux:field class="flex-1 min-w-40">
                    <flux:label>Nombre (snake_case)</flux:label>
                    <flux:input name="nombre_conf" placeholder="ej: mi_configuracion" value="{{ old('nombre_conf') }}" />
                    <flux:error name="nombre_conf" />
                </flux:field>
                <flux:field class="flex-1 min-w-40">
                    <flux:label>Valor</flux:label>
                    <flux:input name="contenido_conf" placeholder="ej: true, un texto, o JSON" value="{{ old('contenido_conf') }}" />
                </flux:field>
                <flux:field>
                    <flux:label>Grupo</flux:label>
                    <flux:select name="grupo">
                        @foreach($grupos as $g)
                            <flux:select.option value="{{ $g }}" :selected="old('grupo', $grupoActivo) === $g">{{ $g }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <button type="submit"
                    class="text-sm bg-[#927F64] hover:bg-[#7a6a53] text-white px-4 py-2 rounded-sm transition-colors">
                    Agregar
                </button>
            </form>
        </div>

    </div>
</x-layouts::app>