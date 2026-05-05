<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DirectorioFloral extends Model
{
    protected $table = 'directorio_floral';

    protected $fillable = [
        'nombre',
        'slug',
        'categoria',
        'descripcion',
        'contenido',
        'imagen',
        'estado',
        'orden',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'orden'  => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────

    public function galeria(): HasMany
    {
        return $this->hasMany(GaleriaFlor::class, 'id_flor')
                    ->orderBy('orden');
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class,
            'productos_flores',
            'id_flor',
            'id_producto'
        );
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}