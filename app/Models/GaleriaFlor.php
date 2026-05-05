<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriaFlor extends Model
{
    protected $table = 'galeria_flores';

    protected $fillable = [
        'id_flor',
        'imagen',
        'estado',
        'orden',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'orden'  => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────

    public function flor(): BelongsTo
    {
        return $this->belongsTo(DirectorioFloral::class, 'id_flor');
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }
}