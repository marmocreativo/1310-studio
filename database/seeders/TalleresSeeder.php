<?php

namespace Database\Seeders;

use App\Models\Taller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TalleresSeeder extends Seeder
{
    public function run(): void
    {
        $talleres = [
            [
                'nombre'   => 'Introducción al Arte Floral',
                'detalles' => "Un taller diseñado para quienes dan sus primeros pasos en el mundo floral.\n\nAprenderás los fundamentos del diseño floral: selección de flores, técnicas de corte, acondicionamiento y composición básica. Saldrás con tu propio arreglo y con las herramientas conceptuales para seguir creando.\n\n**Incluye:** materiales, flores frescas y una copa de bienvenida.",
                'fecha'    => now()->addDays(12),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Ramos de Novia — Taller Avanzado',
                'detalles' => "Un taller intensivo enfocado en la creación de ramos nupciales con técnica europea.\n\nTrabajamos con flores de temporada de alta calidad: peonías, ranúnculos, jazmín y follaje silvestre. Aprenderás el armado a mano alzada, el balance visual y el acabado profesional con cinta.\n\n**Cupo limitado:** 8 personas.\n**Incluye:** flores, materiales y caja de traslado.",
                'fecha'    => now()->addDays(21),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Coronas Florales',
                'detalles' => "Aprende a crear coronas florales para eventos, sesiones fotográficas o como pieza decorativa.\n\nUtilizamos una base de mimbre que puedes reutilizar, trabajando con flores frescas y secas combinadas para lograr texturas y volúmenes únicos.\n\n**Incluye:** base, flores frescas, flores secas y materiales de sujeción.",
                'fecha'    => now()->addDays(35),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Diseño Floral para Eventos',
                'detalles' => "Un taller pensado para wedding planners, coordinadores de eventos y entusiastas que quieren aprender a diseñar centros de mesa y arreglos de gran formato.\n\nVeremos proporciones, paletas de color, estructuras y cómo trabajar con presupuestos reales.\n\n**Duración:** 4 horas.\n**Incluye:** materiales, flores y manual de referencia.",
                'fecha'    => now()->addDays(48),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Ikebana — El Arte Floral Japonés',
                'detalles' => "Una introducción a la filosofía y práctica del Ikebana, el arte floral japonés basado en la armonía, el espacio vacío y la naturaleza como guía.\n\nTrabajamos con kenzan (rana), ramas, follaje y flores de línea para crear composiciones meditativas y precisas.\n\n**Incluye:** todos los materiales y una guía de estilos básicos.",
                'fecha'    => now()->addDays(60),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Flores Secas y Preservadas',
                'detalles' => "Descubre el universo de las flores eternas. En este taller aprenderás técnicas de secado, preservación con glicerina y trabajo con flores liofilizadas.\n\nCrearás un marco botánico y un ramo de flores secas para llevar a casa, piezas que durarán meses o años.\n\n**Incluye:** flores secas, preservadas, marco y materiales.",
                'fecha'    => now()->addDays(75),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Taller de Temporada — Primavera',
                'detalles' => "Un taller especial de edición limitada que celebra las flores de primavera: tulipanes, anémonas, lilas y peonías.\n\nDiseñarás un arreglo libre inspirado en jardines ingleses, con total libertad creativa y guía personalizada.\n\n**Incluye:** flores de temporada, florero y materiales.",
                'fecha'    => now()->subDays(20),
                'estado'   => true,
            ],
            [
                'nombre'   => 'Navidad Floral',
                'detalles' => "Taller navideño donde aprenderás a crear una corona de puerta con materiales naturales: ramas de pino, eucalipto, acebo, canela y flores rojas de temporada.\n\nPerfecto para regalar o decorar tu hogar con un toque artesanal y aromático.\n\n**Incluye:** base metálica, todos los materiales y elementos decorativos.",
                'fecha'    => now()->subDays(60),
                'estado'   => true,
            ],
        ];

        foreach ($talleres as $data) {
            $data['slug'] = $this->generarSlug(Str::slug($data['nombre']));
            Taller::create($data);
        }
    }

    private function generarSlug(string $slug): string
    {
        $original = $slug;
        $count    = Taller::where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$original}-{$count}" : $slug;
    }
}