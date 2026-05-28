<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FechaImportante extends Model
{
    protected $table = 'fechas_importantes';

    protected $fillable = ['id_usuario', 'dia', 'mes', 'etiqueta'];

    protected $casts = [
        'dia' => 'integer',
        'mes' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function getMesNombreAttribute(): string
    {
        return \Carbon\Carbon::create(null, $this->mes, 1)->translatedFormat('F');
    }
}