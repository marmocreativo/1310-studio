<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    protected $fillable = ['nombre', 'clave', 'activo', 'orden'];

    protected $casts = ['activo' => 'boolean'];

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'id_estado')
                    ->where('activo', true)
                    ->orderBy('orden')
                    ->orderBy('nombre');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('nombre');
    }
}