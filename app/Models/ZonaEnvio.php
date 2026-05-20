<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZonaEnvio extends Model
{
    protected $table = 'zonas_envio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'activa',
        'orden',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activa' => 'boolean',
        'orden'  => 'integer',
    ];

    public function alcaldias(): HasMany
    {
        return $this->hasMany(ZonaEnvioAlcaldia::class, 'id_zona')
                    ->orderBy('nombre');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_zona');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }
}