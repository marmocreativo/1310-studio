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

        {{-- Listado de configuraciones — auto-guardado --}}
        <div class="space-y-3">
            @forelse($configuraciones as $config)
                <div class="bg-white border border-zinc-200 rounded-sm p-4 flex flex-col sm:flex-row gap-4 items-start"
                    data-config-row
                    data-update-url="{{ route('admin.configuraciones.update', $config) }}">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-xs font-mono text-zinc-400">{{ $config->nombre_conf }}</p>
                            <span data-save-indicator class="text-xs text-zinc-400 opacity-0 transition-opacity duration-200">
                                Guardado
                            </span>
                        </div>

                        {{-- Booleanos --}}
                        @if($config->contenido_conf === 'true' || $config->contenido_conf === 'false')
                            <label class="flex items-center gap-2 cursor-pointer select-none mt-2">
                                <input type="checkbox"
                                    data-config-input
                                    data-type="boolean"
                                    {{ $config->contenido_conf === 'true' ? 'checked' : '' }}
                                    class="w-4 h-4 accent-[#927F64]">
                                <span data-bool-label class="text-sm text-zinc-700">
                                    {{ $config->contenido_conf === 'true' ? 'Activado' : 'Desactivado' }}
                                </span>
                            </label>

                        {{-- JSON --}}
                        @elseif(str_starts_with(trim($config->contenido_conf ?? ''), '[') || str_starts_with(trim($config->contenido_conf ?? ''), '{'))
                            <textarea
                                data-config-input
                                data-type="text"
                                rows="6"
                                class="w-full text-sm font-mono border border-zinc-200 rounded-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#927F64]"
                            >{{ $config->contenido_conf }}</textarea>

                        {{-- Texto largo --}}
                        @elseif(strlen($config->contenido_conf ?? '') > 80)
                            <textarea
                                data-config-input
                                data-type="text"
                                rows="3"
                                class="w-full text-sm border border-zinc-200 rounded-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#927F64]"
                            >{{ $config->contenido_conf }}</textarea>

                        {{-- String / número --}}
                        @else
                            <input
                                type="text"
                                data-config-input
                                data-type="text"
                                value="{{ $config->contenido_conf }}"
                                class="w-full text-sm border border-zinc-200 rounded-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#927F64]"
                            >
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-zinc-400 text-center py-10">No hay configuraciones en este grupo.</p>
            @endforelse
        </div>

        {{-- Crear / Eliminar — colapsable --}}
        <details class="bg-white border border-zinc-200 rounded-sm group">
            <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between select-none">
                <flux:heading size="sm" class="text-zinc-700">
                    Crear o eliminar configuraciones
                </flux:heading>
                <flux:icon.chevron-down class="size-4 text-zinc-400 transition-transform group-open:rotate-180" />
            </summary>

            <div class="px-5 pb-5 pt-1 border-t border-zinc-100 space-y-5">

                {{-- Form crear --}}
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

                {{-- Listado para eliminar --}}
                <div class="space-y-2 pt-3 border-t border-zinc-100">
                    @forelse($configuraciones as $config)
                        <div class="flex items-center justify-between text-sm py-1.5">
                            <span class="font-mono text-xs text-zinc-500">{{ $config->nombre_conf }}</span>
                            <button type="button"
                                onclick="document.getElementById('form-eliminar-{{ $config->id }}').submit()"
                                class="text-xs text-red-500 hover:text-red-700 px-2 py-1 rounded-sm transition-colors border border-red-100 hover:border-red-300">
                                Eliminar
                            </button>
                        </div>
                        <form id="form-eliminar-{{ $config->id }}" method="POST" action="{{ route('admin.configuraciones.destroy', $config) }}"
                            onsubmit="return confirm('¿Eliminar la configuración {{ $config->nombre_conf }}?')"
                            class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @empty
                        <p class="text-xs text-zinc-400">No hay configuraciones en este grupo.</p>
                    @endforelse
                </div>
            </div>
        </details>

    </div>

    <script>
        function initConfigAutoSave() {
            const rows = document.querySelectorAll('[data-config-row]');

            rows.forEach((row) => {
                const input = row.querySelector('[data-config-input]');
                const indicator = row.querySelector('[data-save-indicator]');
                const boolLabel = row.querySelector('[data-bool-label]');
                const url = row.dataset.updateUrl;
                let debounceTimer = null;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const showIndicator = (text, color) => {
                    if (!indicator) return;
                    indicator.textContent = text;
                    indicator.className = `text-xs ${color} transition-opacity duration-200 opacity-100`;
                };

                const fadeIndicator = () => {
                    if (!indicator) return;
                    setTimeout(() => {
                        indicator.classList.remove('opacity-100');
                        indicator.classList.add('opacity-0');
                    }, 1200);
                };

                const save = () => {
                    const type = input.dataset.type;
                    const value = type === 'boolean'
                        ? (input.checked ? 'true' : 'false')
                        : input.value;

                    showIndicator('Guardando…', 'text-zinc-400');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': 'PATCH',
                        },
                        body: JSON.stringify({ contenido_conf: value }),
                    })
                        .then((res) => {
                            if (!res.ok) throw new Error('save failed');
                            return res.json();
                        })
                        .then(() => {
                            showIndicator('Guardado', 'text-green-600');
                            fadeIndicator();
                        })
                        .catch(() => {
                            showIndicator('Error al guardar', 'text-red-500');
                        });
                };

                if (input.dataset.type === 'boolean') {
                    input.addEventListener('change', () => {
                        if (boolLabel) {
                            boolLabel.textContent = input.checked ? 'Activado' : 'Desactivado';
                        }
                        save();
                    });
                } else {
                    input.addEventListener('input', () => {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(save, 600);
                    });
                    input.addEventListener('blur', () => {
                        clearTimeout(debounceTimer);
                        save();
                    });
                }
            });
        }

        initConfigAutoSave();
    </script>
</x-layouts::app>