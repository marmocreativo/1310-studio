<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['nombre_conf', 'contenido_conf', 'grupo'];

    // Devuelve el valor casteado automáticamente
    public function getValorAttribute(): mixed
    {
        $raw = $this->contenido_conf;

        if (is_null($raw)) return null;
        if ($raw === 'true')  return true;
        if ($raw === 'false') return false;
        if (is_numeric($raw)) return $raw + 0;

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) return $decoded;

        return $raw;
    }

    // Helper estático para leer una config por nombre
    public static function get(string $nombre, mixed $default = null): mixed
    {
        $conf = static::where('nombre_conf', $nombre)->first();
        return $conf ? $conf->valor : $default;
    }

    // Helper estático para escribir
    public static function set(string $nombre, mixed $valor): void
    {
        $contenido = is_array($valor) || is_object($valor)
            ? json_encode($valor, JSON_UNESCAPED_UNICODE)
            : (string) $valor;

        static::updateOrCreate(
            ['nombre_conf' => $nombre],
            ['contenido_conf' => $contenido]
        );
    }
}