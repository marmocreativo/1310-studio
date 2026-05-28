<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DireccionUsuario extends Model
{
    protected $table = 'direcciones_usuario';

    protected $fillable = [
        'id_usuario', 'alias', 'nombre_contacto', 'telefono',
        'calle', 'numero_ext', 'numero_int', 'colonia', 'cp',
        'id_estado', 'id_municipio', 'referencias', 'predeterminada',
    ];

    protected $casts = ['predeterminada' => 'boolean'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function getDireccionCompletaAttribute(): string
    {
        $linea1 = "{$this->calle} {$this->numero_ext}";
        if ($this->numero_int) $linea1 .= " Int. {$this->numero_int}";
        return "{$linea1}, {$this->colonia}, {$this->municipio?->nombre}, {$this->estado?->nombre} CP {$this->cp}";
    }
}