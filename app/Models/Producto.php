<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\VariacionTipo;
use App\Models\VariacionSku;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'detalles',
        'destacado',
        'precio_lista',
        'precio_venta',
        'estado',
    ];

    protected $casts = [
        'destacado'    => 'boolean',
        'estado'       => 'boolean',
        'precio_lista' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    // ─── Relaciones ───────────────────────────────

    public function galeria(): HasMany
    {
        return $this->hasMany(GaleriaProducto::class, 'id_producto')
                    ->orderBy('orden');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            Categoria::class,
            'productos_categorias',
            'id_producto',
            'id_categoria'
        );
    }

    public function flores(): BelongsToMany
    {
        return $this->belongsToMany(
            DirectorioFloral::class,
            'productos_flores',
            'id_producto',
            'id_flor'
        );
    }

    public function variacionTipos(): HasMany
    {
        return $this->hasMany(VariacionTipo::class, 'id_producto')
                    ->orderBy('orden');
    }

    public function variacionSkus(): HasMany
    {
        return $this->hasMany(VariacionSku::class, 'id_producto');
    }

    public function tieneVariaciones(): bool
    {
        return $this->variacionTipos()->exists();
    }

    // ─── Accessors ────────────────────────────────

    public function getTieneDescuentoAttribute(): bool
    {
        return $this->precio_lista !== null
            && $this->precio_lista > $this->precio_venta;
    }

    public function getPorcentajeDescuentoAttribute(): int
    {
        if (! $this->tiene_descuento) return 0;

        return (int) round(
            (($this->precio_lista - $this->precio_venta) / $this->precio_lista) * 100
        );
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}