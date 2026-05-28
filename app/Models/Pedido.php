<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Municipio;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'numero',
        'id_usuario',
        'id_carrito',
        'nombre',
        'email',
        'telefono',
        'tipo_entrega',
        'id_zona',
        'direccion',      // campo viejo
        'calle',          // ← nuevo
        'numero_ext',     // ← nuevo
        'numero_int',     // ← nuevo
        'colonia',
        'municipio',      // campo viejo
        'id_estado',      // ← nuevo
        'id_municipio',   // ← nuevo
        'cp',
        'referencias',
        'fecha_entrega',
        'bloque_entrega',
        'destinatario_nombre',
        'destinatario_telefono',
        'mensaje_tarjeta',
        'subtotal',
        'costo_envio',
        'total',
        'estado',
        'notas',
        'notas_cancelacion',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'costo_envio'   => 'decimal:2',
        'total'         => 'decimal:2',
        'fecha_entrega' => 'date',
    ];

    // ─── Relaciones ───────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'id_carrito');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(ZonaEnvio::class, 'id_zona');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'id_pedido');
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class, 'id_pedido')->latest();
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'id_pedido');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    // ─── Accessors ────────────────────────────────

    public function getBloqueEntregaLabelAttribute(): string
    {
        return match($this->bloque_entrega) {
            'manana' => 'Mañana (9:00 - 13:00)',
            'tarde'  => 'Tarde (13:00 - 18:00)',
            'noche'  => 'Noche (18:00 - 21:00)',
            default  => '—',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'Pendiente de pago',
            'pagado'     => 'Pagado',
            'preparando' => 'En preparación',
            'enviado'    => 'En camino',
            'entregado'  => 'Entregado',
            'cancelado'  => 'Cancelado',
            default      => $this->estado,
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'yellow',
            'pagado'     => 'blue',
            'preparando' => 'purple',
            'enviado'    => 'cyan',
            'entregado'  => 'green',
            'cancelado'  => 'red',
            default      => 'zinc',
        };
    }

    public function getEsEnvioAttribute(): bool
    {
        return $this->tipo_entrega === 'envio';
    }

    // ─── Scopes ───────────────────────────────────

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', ['entregado', 'cancelado']);
    }

    // ─── Helpers ──────────────────────────────────

    public static function generarNumero(): string
    {
        do {
            $numero = '1310-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('numero', $numero)->exists());

        return $numero;
    }
}