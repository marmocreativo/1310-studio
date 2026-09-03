<x-layouts::public title="Checkout — Pago">
<div class="py-16 px-8 max-w-[800px] mx-auto">

    <p class="text-[11px] tracking-[0.2em] uppercase text-on-surface-variant mb-10">
        <a href="{{ route('home') }}" class="hover:text-on-surface transition-colors">Inicio</a>
        <span class="mx-2">·</span>
        <a href="{{ route('carrito.index') }}" class="hover:text-on-surface transition-colors">Carrito</a>
        <span class="mx-2">·</span>
        Checkout
    </p>

    <h1 class="font-serif text-3xl text-on-surface mb-2">Pago</h1>
    <p class="text-sm text-on-surface-variant font-light mb-8">
        Pedido {{ $pedido->numero }} · Total ${{ number_format($pedido->total, 2) }}
    </p>

    <div id="pago-error" class="hidden mb-6 border border-red-200 bg-red-50 px-6 py-4">
        <p class="text-sm text-red-700" id="pago-error-msg"></p>
    </div>

    <div class="border border-outline-variant p-10 text-center space-y-6">
        <svg class="animate-spin w-8 h-8 mx-auto text-on-surface" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        <p class="text-sm text-on-surface-variant font-light">Redirigiendo a Mercado Pago…</p>
    </div>

</div>

<script>
fetch('{{ route('pagos.preferencia') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ metodo: @json($metodo) }),
})
    .then(res => res.json())
    .then(data => {
        if (data.ok && data.init_point) {
            window.location.href = data.init_point;
        } else {
            document.getElementById('pago-error-msg').textContent = data.message ?? 'No se pudo iniciar el pago.';
            document.getElementById('pago-error').classList.remove('hidden');
        }
    })
    .catch(() => {
        document.getElementById('pago-error-msg').textContent = 'No se pudo iniciar el pago.';
        document.getElementById('pago-error').classList.remove('hidden');
    });
</script>
</x-layouts::public>