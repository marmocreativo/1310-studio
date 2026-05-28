<x-layouts::cuenta title="Fechas importantes">
<div class="space-y-8 max-w-lg">

    <div class="space-y-1">
        <h1 class="font-serif text-3xl text-on-surface">Fechas importantes</h1>
        <p class="text-sm text-on-surface-variant font-light leading-relaxed">
            Registra fechas especiales y te avisaremos con anticipación para que puedas
            celebrarlas con el arreglo perfecto.
        </p>
    </div>

    @if(session('success'))
        <div class="border border-green-200 bg-green-50 px-5 py-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Lista --}}
    @forelse($fechas as $fecha)
        <div class="flex items-center justify-between border-b border-outline-variant pb-4"
             x-data>
            <div class="space-y-0.5">
                <p class="text-sm font-medium text-on-surface">{{ $fecha->etiqueta }}</p>
                <p class="text-xs text-on-surface-variant">
                    {{ $fecha->dia }} de {{ $fecha->mes_nombre }}
                </p>
            </div>
            <button
                x-on:click="
                    if(confirm('¿Eliminar esta fecha?')) {
                        fetch('{{ route('cuenta.fechas.destroy', $fecha) }}', {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).then(() => window.location.reload())
                    }
                "
                class="text-[10px] tracking-[0.1em] uppercase text-outline-variant hover:text-red-500 transition-colors underline">
                Eliminar
            </button>
        </div>
    @empty
        <div class="border border-outline-variant p-12 text-center space-y-3">
            <flux:icon name="calendar" class="w-10 h-10 mx-auto text-outline-variant" />
            <p class="text-on-surface-variant font-light">No tienes fechas registradas aún.</p>
        </div>
    @endforelse

    {{-- Formulario nueva fecha --}}
    <div class="space-y-5 pt-4" x-data="fechaForm()">
        <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant border-b border-outline-variant pb-3">
            Agregar fecha
        </p>

        <div class="grid grid-cols-3 gap-4">
            <div class="space-y-2">
                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Día *</label>
                <select x-model="dia"
                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface appearance-none">
                    <option value="">—</option>
                    <template x-for="d in diasDisponibles" :key="d">
                        <option :value="d" x-text="d"></option>
                    </template>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Mes *</label>
                <select x-model.number="mes"
                    x-on:change="ajustarDia()"
                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-on-surface appearance-none">
                    <option value="">—</option>
                    <template x-for="m in meses" :key="m.num">
                        <option :value="m.num" x-text="m.nombre"></option>
                    </template>
                </select>
            </div>

            <div class="space-y-2 col-span-3 sm:col-span-1">
                <label class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant">Etiqueta *</label>
                <input type="text" x-model="etiqueta" maxlength="100"
                    class="w-full border border-outline-variant bg-transparent px-4 py-3 text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-on-surface transition-colors"
                    placeholder="Cumpleaños mamá" />
            </div>
        </div>

        <p x-show="error" x-text="error" class="text-xs text-red-600"></p>

        <button type="button" x-on:click="guardar()"
            :disabled="guardando"
            class="bg-on-surface text-surface px-8 py-3 text-xs tracking-[0.3em] uppercase hover:opacity-80 transition-colors disabled:opacity-40">
            <span x-text="guardando ? 'Guardando…' : 'Agregar fecha'"></span>
        </button>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('fechaForm', () => ({
        dia:       '',
        mes:       '',
        etiqueta:  '',
        guardando: false,
        error:     '',

        meses: [
            { num: 1,  nombre: 'Enero' },
            { num: 2,  nombre: 'Febrero' },
            { num: 3,  nombre: 'Marzo' },
            { num: 4,  nombre: 'Abril' },
            { num: 5,  nombre: 'Mayo' },
            { num: 6,  nombre: 'Junio' },
            { num: 7,  nombre: 'Julio' },
            { num: 8,  nombre: 'Agosto' },
            { num: 9,  nombre: 'Septiembre' },
            { num: 10, nombre: 'Octubre' },
            { num: 11, nombre: 'Noviembre' },
            { num: 12, nombre: 'Diciembre' },
        ],

        get diasEnMes() {
            if (!this.mes) return 31;
            const diasPorMes = [31,29,31,30,31,30,31,31,30,31,30,31];
            return diasPorMes[this.mes - 1];
        },

        get diasDisponibles() {
            return Array.from({ length: this.diasEnMes }, (_, i) => i + 1);
        },

        ajustarDia() {
            if (this.dia > this.diasEnMes) this.dia = '';
        },

        async guardar() {
            this.error = '';

            if (!this.dia || !this.mes || !this.etiqueta.trim()) {
                this.error = 'Completa todos los campos.';
                return;
            }

            this.guardando = true;

            try {
                const res = await fetch('{{ route('cuenta.fechas.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        dia:      this.dia,
                        mes:      this.mes,
                        etiqueta: this.etiqueta,
                    }),
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    const data = await res.json();
                    this.error = data.message ?? 'Error al guardar.';
                }
            } catch (e) {
                this.error = 'Error de conexión.';
            } finally {
                this.guardando = false;
            }
        },
    }));
});
</script>
</x-layouts::cuenta>