<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarritoItem extends Model
{
    protected $table = 'carrito_items';

    protected $fillable = [
        'id_carrito',
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

    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'id_carrito');
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
        // Primero imagen del SKU, luego primera de la galería del producto
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