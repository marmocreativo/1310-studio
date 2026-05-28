<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estado;
use App\Models\Municipio;

class EstadosMunicipiosSeeder extends Seeder
{
    public function run(): void
    {
        // ── CDMX ──────────────────────────────────────
        $cdmx = Estado::create(['nombre' => 'Ciudad de México', 'clave' => 'CDMX', 'orden' => 1]);

        $alcaldias = [
            'Álvaro Obregón', 'Azcapotzalco', 'Benito Juárez', 'Coyoacán',
            'Cuajimalpa de Morelos', 'Cuauhtémoc', 'Gustavo A. Madero',
            'Iztacalco', 'Iztapalapa', 'La Magdalena Contreras',
            'Miguel Hidalgo', 'Milpa Alta', 'Tláhuac', 'Tlalpan',
            'Venustiano Carranza', 'Xochimilco',
        ];

        foreach ($alcaldias as $i => $nombre) {
            Municipio::create([
                'id_estado' => $cdmx->id,
                'nombre'    => $nombre,
                'tipo'      => 'alcaldia',
                'orden'     => $i + 1,
            ]);
        }

        // ── Estado de México (municipios conurbados) ───
        $edomex = Estado::create(['nombre' => 'Estado de México', 'clave' => 'MEX', 'orden' => 2]);

        $municipios = [
            'Atizapán de Zaragoza', 'Coacalco de Berriozábal', 'Cuautitlán',
            'Cuautitlán Izcalli', 'Chalco', 'Chimalhuacán', 'Ecatepec de Morelos',
            'Huixquilucan', 'Ixtapaluca', 'La Paz', 'Naucalpan de Juárez',
            'Nezahualcóyotl', 'Nicolás Romero', 'Tecámac', 'Texcoco',
            'Tlalnepantla de Baz', 'Toluca', 'Tultitlán', 'Valle de Chalco Solidaridad',
        ];

        foreach ($municipios as $i => $nombre) {
            Municipio::create([
                'id_estado' => $edomex->id,
                'nombre'    => $nombre,
                'tipo'      => 'municipio',
                'orden'     => $i + 1,
            ]);
        }
    }
}