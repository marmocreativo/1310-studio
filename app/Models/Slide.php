<?php

namespace App\Models;

use App\Enums\TipoSlide;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Slide extends Model
{
    protected $table = 'slides';

    protected $fillable = [
        'titulo',
        'tipo',
        'caption',
        'texto_boton',
        'enlace_boton',
        'imagen_fondo',
        'logo',
        'video',
        'video_youtube',
        'imagen_overlay',
        'estado',
        'orden',
    ];

    protected $casts = [
        'tipo'   => TipoSlide::class,
        'estado' => 'boolean',
        'orden'  => 'integer',
    ];

    // ─── Accessors ────────────────────────────────

    public function getImagenFondoUrlAttribute(): ?string
    {
        if ($this->imagen_fondo && Storage::disk('public')->exists($this->imagen_fondo)) {
            return Storage::disk('public')->url($this->imagen_fondo);
        }
        return null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->url($this->logo);
        }
        return null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        if ($this->video && Storage::disk('public')->exists($this->video)) {
            return Storage::disk('public')->url($this->video);
        }
        return null;
    }

    public function getImagenOverlayUrlAttribute(): ?string
    {
        if ($this->imagen_overlay && Storage::disk('public')->exists($this->imagen_overlay)) {
            return Storage::disk('public')->url($this->imagen_overlay);
        }
        return null;
    }

    public function getEsYoutubeAttribute(): bool
    {
        return !is_null($this->video_youtube);
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('id');
    }
}