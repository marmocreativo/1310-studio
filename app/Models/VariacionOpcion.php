<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VariacionOpcion extends Model
{
    protected $table = 'variacion_opciones';

    protected $fillable = ['id_tipo', 'id_opcion_default', 'nombre', 'imagen', 'orden'];

    protected $casts = [
        'orden'            => 'integer',
        'id_opcion_default'=> 'integer',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(VariacionTipo::class, 'id_tipo');
    }

    public function opcionDefault(): BelongsTo
    {
        return $this->belongsTo(VariacionOpcionDefault::class, 'id_opcion_default');
    }

    public function skus(): BelongsToMany
    {
        return $this->belongsToMany(VariacionSku::class, 'variacion_sku_opciones', 'id_opcion', 'id_sku');
    }
}