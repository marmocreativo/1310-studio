<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariacionTipo extends Model
{
    protected $table = 'variacion_tipos';

    protected $fillable = ['id_producto', 'id_tipo_default', 'nombre', 'orden'];

    protected $casts = [
        'orden'           => 'integer',
        'id_tipo_default' => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function tipoDefault(): BelongsTo
    {
        return $this->belongsTo(VariacionTipoDefault::class, 'id_tipo_default');
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(VariacionOpcion::class, 'id_tipo')
                    ->orderBy('orden');
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}