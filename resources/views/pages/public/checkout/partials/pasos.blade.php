@php
    $pasos = [
        1 => ['label' => 'Contacto',  'route' => 'checkout.paso1'],
        2 => ['label' => 'Entrega',   'route' => 'checkout.paso2'],
        3 => ['label' => 'Pago',      'route' => 'checkout.paso3'],
    ];
@endphp

<div class="flex items-center mb-12">
    @foreach($pasos as $num => $info)
        <div class="flex items-center">
            <div class="flex items-center gap-3">
                @if($num < $pasoActual)
                    <a href="{{ route($info['route']) }}" class="flex items-center gap-3 group">
                        <div class="w-7 h-7 flex items-center justify-center bg-on-surface text-surface">
                            <flux:icon name="check" class="w-3.5 h-3.5" />
                        </div>
                        <span class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant hidden sm:block group-hover:text-on-surface transition-colors">
                            {{ $info['label'] }}
                        </span>
                    </a>
                @elseif($num === $pasoActual)
                    <div class="w-7 h-7 flex items-center justify-center bg-on-surface text-surface text-xs font-medium">
                        {{ $num }}
                    </div>
                    <span class="text-[11px] tracking-[0.15em] uppercase text-on-surface hidden sm:block">
                        {{ $info['label'] }}
                    </span>
                @else
                    <div class="w-7 h-7 flex items-center justify-center border border-outline-variant text-on-surface-variant text-xs">
                        {{ $num }}
                    </div>
                    <span class="text-[11px] tracking-[0.15em] uppercase text-on-surface-variant hidden sm:block">
                        {{ $info['label'] }}
                    </span>
                @endif
            </div>
        </div>
        @if($num < 3)
            <div class="w-12 md:w-20 h-px bg-outline-variant mx-3 shrink-0"></div>
        @endif
    @endforeach
</div>