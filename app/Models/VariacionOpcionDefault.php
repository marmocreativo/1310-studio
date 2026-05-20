<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariacionOpcionDefault extends Model
{
    protected $table = 'variacion_opciones_default';

    protected $fillable = ['id_tipo_default', 'nombre', 'imagen', 'orden'];

    protected $casts = ['orden' => 'integer'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(VariacionTipoDefault::class, 'id_tipo_default');
    }
}