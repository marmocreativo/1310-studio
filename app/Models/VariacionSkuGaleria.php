<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariacionSkuGaleria extends Model
{
    protected $table = 'variacion_sku_galeria';

    protected $fillable = [
        'id_sku',
        'imagen',
        'estado',
        'orden',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'orden'  => 'integer',
    ];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(VariacionSku::class, 'id_sku');
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }
}