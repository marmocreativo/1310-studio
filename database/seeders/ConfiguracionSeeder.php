<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Sistema
            [
                'nombre_conf'   => 'activar_tienda',
                'contenido_conf'=> 'true',
                'grupo'         => 'sistema',
            ],
            [
                'nombre_conf'   => 'activar_talleres',
                'contenido_conf'=> 'true',
                'grupo'         => 'sistema',
            ],

            // Contacto
            [
                'nombre_conf'   => 'whatsapp_contacto',
                'contenido_conf'=> '5215500000000',
                'grupo'         => 'contacto',
            ],
            [
                'nombre_conf'   => 'email_contacto',
                'contenido_conf'=> 'hola@1310studio.com',
                'grupo'         => 'contacto',
            ],
            [
                'nombre_conf'   => 'direccion_contacto',
                'contenido_conf'=> 'Ámsterdam 310, Hipódromo, CDMX',
                'grupo'         => 'contacto',
            ],
            [
                'nombre_conf'   => 'horario_contacto',
                'contenido_conf'=> 'Lunes a viernes 9:00 – 18:00 · Sábados 10:00 – 15:00',
                'grupo'         => 'contacto',
            ],

            // Apariencia
            [
                'nombre_conf'   => 'social_links',
                'contenido_conf'=> json_encode([
                    ['red' => 'instagram', 'url' => 'https://instagram.com/1310studio'],
                    ['red' => 'facebook',  'url' => 'https://facebook.com/1310studio'],
                    ['red' => 'tiktok',    'url' => 'https://tiktok.com/@1310studio'],
                ], JSON_UNESCAPED_UNICODE),
                'grupo'         => 'apariencia',
            ],
        ];

        foreach ($configs as $config) {
            Configuracion::updateOrCreate(
                ['nombre_conf' => $config['nombre_conf']],
                $config
            );
        }
    }
}