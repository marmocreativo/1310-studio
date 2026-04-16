<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'categoria',
        'resumen',
        'contenido',
        'imagen',
        'estado',
    ];

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen && \Storage::disk('public')->exists($this->imagen)) {
            return \Storage::disk('public')->url($this->imagen);
        }

        return asset('images/pagina_default.webp');
    }

    public function isPublicado(): bool
    {
        return $this->estado === 'publicado';
    }
}