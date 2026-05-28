<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZonaEnvioAlcaldia extends Model
{
    protected $table = 'zonas_envio_alcaldias';

    protected $fillable = [
        'id_zona',
        'nombre',
        'id_municipio',
    ];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(ZonaEnvio::class, 'id_zona');
    }
    
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }
}