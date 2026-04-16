<?php

namespace Database\Seeders;

use App\Models\Pagina;
use Illuminate\Database\Seeder;

class PaginasSeeder extends Seeder
{
    public function run(): void
    {
        Pagina::insert([
            [
                'titulo'    => 'Términos y Condiciones',
                'slug'      => 'terminos-y-condiciones',
                'categoria' => 'legal',
                'resumen'   => 'Conoce los términos y condiciones que rigen el uso de nuestros servicios.',
                'contenido' => 'Al acceder y utilizar este sitio web, aceptas los presentes términos y condiciones. Nos reservamos el derecho de modificarlos en cualquier momento. El uso continuado del sitio implica la aceptación de los cambios.',
                'imagen'    => null,
                'estado'    => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo'    => 'Aviso de Privacidad',
                'slug'      => 'aviso-de-privacidad',
                'categoria' => 'legal',
                'resumen'   => 'Información sobre cómo recopilamos, usamos y protegemos tus datos personales.',
                'contenido' => 'Nos comprometemos a proteger tu privacidad. Los datos personales que recopilamos son utilizados únicamente para mejorar tu experiencia en el sitio y no serán compartidos con terceros sin tu consentimiento.',
                'imagen'    => null,
                'estado'    => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}