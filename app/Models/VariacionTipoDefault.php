<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariacionTipoDefault extends Model
{
    protected $table = 'variacion_tipos_default';

    protected $fillable = ['nombre', 'orden'];

    protected $casts = ['orden' => 'integer'];

    public function opciones(): HasMany
    {
        return $this->hasMany(VariacionOpcionDefault::class, 'id_tipo_default')
                    ->orderBy('orden');
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}