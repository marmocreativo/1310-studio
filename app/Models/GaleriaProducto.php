<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriaProducto extends Model
{
    protected $table = 'galeria_productos';

    protected $fillable = [
        'id_producto',
        'imagen',
        'estado',
        'orden',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'orden'  => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }
}