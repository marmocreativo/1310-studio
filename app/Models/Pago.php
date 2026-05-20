<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'id_pedido',
        'metodo',
        'mp_preference_id',
        'mp_payment_id',
        'mp_status',
        'monto',
        'estado',
        'datos_mp',
    ];

    protected $casts = [
        'monto'    => 'decimal:2',
        'datos_mp' => 'array',
    ];

    // ─── Relaciones ───────────────────────────────

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    // ─── Accessors ────────────────────────────────

    public function getMetodoLabelAttribute(): string
    {
        return match($this->metodo) {
            'tarjeta'         => 'Tarjeta de crédito/débito',
            'efectivo'        => 'Tienda de conveniencia',
            'contra_entrega'  => 'Pago contra entrega',
            default           => $this->metodo,
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pendiente'   => 'Pendiente',
            'aprobado'    => 'Aprobado',
            'rechazado'   => 'Rechazado',
            'reembolsado' => 'Reembolsado',
            default       => $this->estado,
        };
    }

    public function getAprobadoAttribute(): bool
    {
        return $this->estado === 'aprobado';
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }
}