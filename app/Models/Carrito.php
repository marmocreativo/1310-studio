<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrito extends Model
{
    protected $table = 'carritos';

    protected $fillable = [
        'id_usuario',
        'sesion_id',
        'email',
        'nombre',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class, 'id_carrito')
                    ->with(['producto.galeria', 'sku.opciones.tipo']);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_carrito');
    }

    // ─── Accessors ────────────────────────────────

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->precio_snapshot * $item->cantidad);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('cantidad');
    }

    public function getEstaVacioAttribute(): bool
    {
        return $this->items->isEmpty();
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeNoExpirados($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}