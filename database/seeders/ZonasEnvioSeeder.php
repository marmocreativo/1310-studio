<?php

namespace Database\Seeders;

use App\Models\ZonaEnvio;
use App\Models\ZonaEnvioAlcaldia;
use Illuminate\Database\Seeder;

class ZonasEnvioSeeder extends Seeder
{
    public function run(): void
    {
        $zonas = [
            [
                'nombre'      => 'Zona Centro',
                'descripcion' => 'Alcaldías centrales de CDMX',
                'precio'      => 80.00,
                'activa'      => true,
                'orden'       => 1,
                'alcaldias'   => [
                    'Cuauhtémoc',
                    'Miguel Hidalgo',
                    'Benito Juárez',
                    'Venustiano Carranza',
                ],
            ],
            [
                'nombre'      => 'Zona Norte',
                'descripcion' => 'Alcaldías al norte de CDMX',
                'precio'      => 100.00,
                'activa'      => true,
                'orden'       => 2,
                'alcaldias'   => [
                    'Gustavo A. Madero',
                    'Azcapotzalco',
                    'Tlalnepantla de Baz',
                    'Ecatepec de Morelos',
                    'Naucalpan de Juárez',
                ],
            ],
            [
                'nombre'      => 'Zona Sur',
                'descripcion' => 'Alcaldías al sur de CDMX',
                'precio'      => 110.00,
                'activa'      => true,
                'orden'       => 3,
                'alcaldias'   => [
                    'Coyoacán',
                    'Tlalpan',
                    'Xochimilco',
                    'Milpa Alta',
                    'Tláhuac',
                ],
            ],
            [
                'nombre'      => 'Zona Oriente',
                'descripcion' => 'Alcaldías al oriente de CDMX y municipios del EDOMEX',
                'precio'      => 120.00,
                'activa'      => true,
                'orden'       => 4,
                'alcaldias'   => [
                    'Iztapalapa',
                    'Iztacalco',
                    'Nezahualcóyotl',
                    'Chimalhuacán',
                    'La Paz',
                    'Los Reyes La Paz',
                ],
            ],
            [
                'nombre'      => 'Zona Poniente',
                'descripcion' => 'Alcaldías al poniente de CDMX y municipios del EDOMEX',
                'precio'      => 110.00,
                'activa'      => true,
                'orden'       => 5,
                'alcaldias'   => [
                    'Álvaro Obregón',
                    'Cuajimalpa de Morelos',
                    'Magdalena Contreras',
                    'Huixquilucan',
                    'Atizapán de Zaragoza',
                ],
            ],
            [
                'nombre'      => 'Zona Sur-Oriente',
                'descripcion' => 'Municipios del sur oriente del EDOMEX',
                'precio'      => 130.00,
                'activa'      => true,
                'orden'       => 6,
                'alcaldias'   => [
                    'Chalco',
                    'Valle de Chalco',
                    'Ixtapaluca',
                    'Amecameca',
                ],
            ],
        ];

        foreach ($zonas as $zonaData) {
            $alcaldias = $zonaData['alcaldias'];
            unset($zonaData['alcaldias']);

            $zona = ZonaEnvio::create($zonaData);

            foreach ($alcaldias as $nombre) {
                ZonaEnvioAlcaldia::create([
                    'id_zona' => $zona->id,
                    'nombre'  => $nombre,
                ]);
            }
        }
    }
}