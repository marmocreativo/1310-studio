<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    protected $table = 'pedido_items';

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'id_sku',
        'nombre_snapshot',
        'precio_snapshot',
        'cantidad',
        'opciones_snapshot',
    ];

    protected $casts = [
        'precio_snapshot'  => 'decimal:2',
        'cantidad'         => 'integer',
        'opciones_snapshot'=> 'array',
    ];

    // ─── Relaciones ───────────────────────────────

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(VariacionSku::class, 'id_sku');
    }

    // ─── Accessors ────────────────────────────────

    public function getSubtotalAttribute(): float
    {
        return (float) $this->precio_snapshot * $this->cantidad;
    }

    public function getImagenUrlAttribute(): ?string
    {
        if ($this->sku?->imagen) {
            return \Storage::disk('public')->url($this->sku->imagen);
        }

        $portada = $this->producto?->galeria->first();
        if ($portada) {
            return \Storage::disk('public')->url($portada->imagen);
        }

        return null;
    }
}