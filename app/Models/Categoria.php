<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'id_padre',
        'imagen',
        'estado',
    ];

    public function padre(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_padre');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Categoria::class, 'id_padre');
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class,
            'productos_categorias',
            'id_categoria',
            'id_producto'
        );
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen && \Storage::disk('public')->exists($this->imagen)) {
            return \Storage::disk('public')->url($this->imagen);
        }

        return asset('images/categoria_default.webp');
    }

    public function isPublicado(): bool
    {
        return $this->estado === 'publicado';
    }

    // Solo categorías raíz (sin padre)
    public function scopeRaiz($query)
    {
        return $query->whereNull('id_padre');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}