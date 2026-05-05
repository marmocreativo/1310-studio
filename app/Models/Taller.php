<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    protected $table = 'talleres';

    protected $fillable = [
        'nombre',
        'slug',
        'detalles',
        'imagen',
        'fecha',
        'estado',
    ];

    protected $casts = [
        'fecha'  => 'datetime',
        'estado' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeProximos($query)
    {
        return $query->where('fecha', '>=', now())->orderBy('fecha');
    }

    public function scopePasados($query)
    {
        return $query->where('fecha', '<', now())->orderByDesc('fecha');
    }

    
}