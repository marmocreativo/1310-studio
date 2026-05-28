@php
    $config = match($estado) {
        'pendiente'  => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-800',  'label' => 'Pendiente'],
        'pagado'     => ['bg' => 'bg-blue-100',    'text' => 'text-blue-800',    'label' => 'Pagado'],
        'preparando' => ['bg' => 'bg-purple-100',  'text' => 'text-purple-800',  'label' => 'Preparando'],
        'enviado'    => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-800',  'label' => 'Enviado'],
        'entregado'  => ['bg' => 'bg-green-100',   'text' => 'text-green-800',   'label' => 'Entregado'],
        'cancelado'  => ['bg' => 'bg-red-100',     'text' => 'text-red-800',     'label' => 'Cancelado'],
        default      => ['bg' => 'bg-zinc-100',    'text' => 'text-zinc-800',    'label' => ucfirst($estado)],
    };
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 text-[10px] tracking-[0.1em] uppercase font-medium {{ $config['bg'] }} {{ $config['text'] }}">
    {{ $config['label'] }}
</span>