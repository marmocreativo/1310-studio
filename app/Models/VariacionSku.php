<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class VariacionSku extends Model
{
    protected $table = 'variacion_skus';

    protected $fillable = [
        'id_producto',
        'precio_lista',
        'precio_venta',
        'estado',
        'imagen',
        'notas',
    ];

    protected $casts = [
        'precio_lista' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'estado'       => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function opciones(): BelongsToMany
    {
        return $this->belongsToMany(VariacionOpcion::class, 'variacion_sku_opciones', 'id_sku', 'id_opcion');
    }

    // Devuelve label legible: "3 ramas / Blanca / Rosa"
    public function getLabelAttribute(): string
    {
        return $this->opciones->pluck('nombre')->join(' / ');
    }

    public function getTieneDescuentoAttribute(): bool
    {
        return $this->precio_lista !== null && $this->precio_lista > $this->precio_venta;
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}