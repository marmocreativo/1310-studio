<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        // IDs de categorías (ajusta si difieren en tu DB)
        $catRamos     = DB::table('categorias')->where('slug', 'ramos')->value('id');
        $catOrquideas = DB::table('categorias')->where('slug', 'orquideas')->value('id');

        // IDs de flores
        $flores = DB::table('directorio_floral')->pluck('id', 'slug');

        $productos = [

            // ── RAMOS ────────────────────────────────────────────────────

            [
                'producto' => [
                    'nombre'       => 'BURGUNDY',
                    'slug'         => 'burgundy',
                    'descripcion'  => 'Un ramo con carácter, construido con anémonas, gerberas, rosas y crisantemos, donde los tonos profundos generan una narrativa más intensa y emocional.',
                    'detalles'     => "Cada flor es seleccionada por su estructura y color, logrando un arreglo con presencia y profundidad visual.\n\nEnvuelto en papel coreano premium y firmado con el listón 1310 Studio.\n\nEvoca: pasión, intensidad, elegancia.\nIdeal para: ocasiones románticas, aniversarios, momentos significativos.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 850.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['anemona', 'gerbera', 'rosa', 'crisantemo'],
            ],

            [
                'producto' => [
                    'nombre'       => 'VERDANT',
                    'slug'         => 'verdant',
                    'descripcion'  => 'Una composición orgánica que integra anémona, lilies, rosa, hipericum, lisianthus, kalanchoe y yuko, logrando un balance entre lo floral y lo verde.',
                    'detalles'     => "Este ramo destaca por su riqueza en texturas y su estética natural, cuidadosamente estructurada para mantener un look sofisticado sin perder frescura.\n\nPresentado en papel coreano premium con listón 1310 Studio.\n\nEvoca: naturaleza, armonía, frescura.\nIdeal para: regalos relajados pero elegantes, decoración de espacios, bienestar.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 950.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['anemona', 'lilies', 'rosa', 'hipericum', 'lisianthus', 'kalanchoe', 'yuko'],
            ],

            [
                'producto' => [
                    'nombre'       => 'TULIP NO.1',
                    'slug'         => 'tulip-no-1',
                    'descripcion'  => 'Una interpretación pura y directa del tulipán como protagonista absoluto.',
                    'detalles'     => "Seleccionamos tulipanes de tallo largo de la más alta calidad, buscando uniformidad, apertura progresiva y elegancia natural.\n\nEl minimalismo del arreglo se eleva con papel coreano premium y el distintivo listón 1310 Studio.\n\nEvoca: simplicidad, elegancia, modernidad.\nIdeal para: regalos sofisticados, estética minimalista, statement floral.",
                    'destacado'    => true,
                    'precio_lista' => null,
                    'precio_venta' => 750.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['tulipan'],
            ],

            [
                'producto' => [
                    'nombre'       => 'AZURE',
                    'slug'         => 'azure',
                    'descripcion'  => 'Un arreglo icónico que combina hortensias y ranúnculos, donde el volumen de la hortensia se equilibra con la precisión de los ranúnculos.',
                    'detalles'     => "La forma redondeada genera una pieza visualmente contundente y altamente estética.\n\nEnvuelto en papel coreano premium y terminado con listón 1310 Studio.\n\nEvoca: serenidad, profundidad, sofisticación.\nIdeal para: regalos memorables, espacios elegantes, momentos especiales.",
                    'destacado'    => true,
                    'precio_lista' => null,
                    'precio_venta' => 890.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['hortensia', 'ranunculo'],
            ],

            [
                'producto' => [
                    'nombre'       => 'POISE',
                    'slug'         => 'poise',
                    'descripcion'  => 'Una composición elegante y contemporánea a base de lisianthus y kalanchoe, donde las líneas limpias y la delicadeza de cada flor crean un arreglo sobrio pero profundamente estético.',
                    'detalles'     => "El contraste entre tonos y formas se acentúa con nuestro papel coreano de alta gama y el distintivo listón 1310 Studio.\n\nEvoca: calma, equilibrio, sofisticación silenciosa.\nIdeal para: regalos formales, espacios interiores, detalles de buen gusto.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 780.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['lisianthus', 'kalanchoe'],
            ],

            [
                'producto' => [
                    'nombre'       => 'EMBER',
                    'slug'         => 'ember',
                    'descripcion'  => 'Un arreglo cálido y envolvente que mezcla kalanchoe, lisianthus, snapdragon y ranúnculos, destacando por sus tonos encendidos y composición dinámica.',
                    'detalles'     => "La selección floral prioriza apertura, volumen y contraste para generar un impacto visual inmediato.\n\nEnvuelto en papel coreano premium en tonos neutros y terminado con listón 1310 Studio.\n\nEvoca: calidez, cercanía, energía emocional.\nIdeal para: agradecimientos, celebraciones íntimas, regalos con personalidad.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 820.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['kalanchoe', 'lisianthus', 'snapdragon', 'ranunculo'],
            ],

            [
                'producto' => [
                    'nombre'       => 'CORAL',
                    'slug'         => 'coral',
                    'descripcion'  => 'Un arreglo luminoso y expresivo que combina rosa inglesa, lilies, gerbera, rosa y crisantemo, seleccionado bajo un estándar de frescura y apertura ideal para lograr volumen y textura.',
                    'detalles'     => "Envuelto en papel coreano premium de acabado suave y estructural, y terminado con el listón textil 1310 Studio, este ramo equilibra sofisticación con un toque vibrante.\n\nEvoca: alegría, energía, celebración.\nIdeal para: cumpleaños, momentos de reconocimiento, regalos espontáneos con intención.",
                    'destacado'    => true,
                    'precio_lista' => null,
                    'precio_venta' => 900.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['rosa-inglesa', 'lilies', 'gerbera', 'rosa', 'crisantemo'],
            ],

            [
                'producto' => [
                    'nombre'       => 'IVORY BLUSH',
                    'slug'         => 'ivory-blush',
                    'descripcion'  => 'Una pieza delicada y refinada que combina tulipanes y ranúnculos en una paleta suave y elegante.',
                    'detalles'     => "La pureza de las formas y la armonía de tonos crean un arreglo atemporal, con una estética limpia y sofisticada.\n\nPresentado en papel coreano premium y listón 1310 Studio.\n\nEvoca: ternura, elegancia, serenidad.\nIdeal para: regalos románticos, detalles delicados, ocasiones especiales.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 800.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['tulipan', 'ranunculo'],
            ],

            [
                'producto' => [
                    'nombre'       => 'APRICOT',
                    'slug'         => 'apricot',
                    'descripcion'  => 'Un ramo cálido y equilibrado compuesto por tulipanes y ranúnculos, donde los tonos durazno generan una sensación acogedora y contemporánea.',
                    'detalles'     => "La composición busca volumen suave y transición de color natural, logrando un arreglo visualmente fluido.\n\nEnvuelto en papel coreano premium con el sello del listón 1310 Studio.\n\nEvoca: calidez, cercanía, armonía.\nIdeal para: regalos personales, momentos íntimos, decoración sutil.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 780.00,
                    'estado'       => true,
                ],
                'categoria' => $catRamos,
                'flores'    => ['tulipan', 'ranunculo'],
            ],

            // ── ORQUÍDEAS ────────────────────────────────────────────────

            [
                'producto' => [
                    'nombre'       => 'ORIGIN',
                    'slug'         => 'origin',
                    'descripcion'  => 'Una pieza insignia de la casa. Composición escultórica con 4 orquídeas Phalaenopsis doble de la más alta calidad, seleccionadas por su apertura, simetría y duración excepcional.',
                    'detalles'     => "Montadas en una maceta forrada en piel, diseñada como objeto decorativo, con base elevada que aporta presencia arquitectónica al espacio.\n\nCada elemento está pensado para trascender lo floral y convertirse en pieza de diseño. Terminada con el listón textil 1310 Studio.\n\nEvoca: lujo, permanencia, statement.\nIdeal para: espacios principales, regalos de alto impacto, proyectos de interiorismo.",
                    'destacado'    => true,
                    'precio_lista' => null,
                    'precio_venta' => 2800.00,
                    'estado'       => true,
                ],
                'categoria' => $catOrquideas,
                'flores'    => ['orquidea-phalaenopsis'],
            ],

            [
                'producto' => [
                    'nombre'       => 'GROUND',
                    'slug'         => 'ground',
                    'descripcion'  => 'Una composición sólida y sofisticada con 3 orquídeas Phalaenopsis doble, equilibradas sobre una base transparente con sustrato natural que resalta la raíz y origen de la planta.',
                    'detalles'     => "El contraste entre lo orgánico y lo limpio genera una estética contemporánea y atemporal.\n\nFirmada con el listón 1310 Studio, manteniendo el lenguaje minimalista de la marca.\n\nEvoca: conexión, profundidad, naturalidad elegante.\nIdeal para: interiores modernos, oficinas, regalos sobrios pero con intención.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 2200.00,
                    'estado'       => true,
                ],
                'categoria' => $catOrquideas,
                'flores'    => ['orquidea-phalaenopsis'],
            ],

            [
                'producto' => [
                    'nombre'       => 'SILK',
                    'slug'         => 'silk',
                    'descripcion'  => 'Una pieza etérea y refinada con 3 orquídeas Phalaenopsis doble en tonalidad blanca, seleccionadas por su pureza y uniformidad.',
                    'detalles'     => "La maceta de líneas suaves complementa la caída natural de las flores, generando una composición fluida y ligera.\n\nAcompañada del listón 1310 Studio y terminada con materiales de alta calidad.\n\nEvoca: paz, serenidad, elegancia silenciosa.\nIdeal para: regalos delicados, espacios de descanso, ambientes sofisticados.",
                    'destacado'    => false,
                    'precio_lista' => null,
                    'precio_venta' => 2100.00,
                    'estado'       => true,
                ],
                'categoria' => $catOrquideas,
                'flores'    => ['orquidea-phalaenopsis'],
            ],

            [
                'producto' => [
                    'nombre'       => 'AURA',
                    'slug'         => 'aura',
                    'descripcion'  => 'Una pieza equilibrada y luminosa con 2 orquídeas Phalaenopsis doble, donde el color y la apertura crean una presencia armónica.',
                    'detalles'     => "La composición logra un punto medio entre impacto visual y ligereza, convirtiéndola en una opción versátil y altamente estética.\n\nPresentada con materiales premium y el distintivo listón 1310 Studio.\n\nEvoca: energía sutil, balance, calidez.\nIdeal para: regalos elegantes, espacios habitados, decoración cotidiana con intención.",
                    'destacado'    => true,
                    'precio_lista' => null,
                    'precio_venta' => 1600.00,
                    'estado'       => true,
                ],
                'categoria' => $catOrquideas,
                'flores'    => ['orquidea-phalaenopsis'],
            ],

            [
                'producto' => [
                    'nombre'       => 'STEM',
                    'slug'         => 'stem',
                    'descripcion'  => 'La expresión más pura de la orquídea. Una composición minimalista con 1 orquídea Phalaenopsis doble, donde cada línea, curva y flor se aprecia sin distracciones.',
                    'detalles'     => "El diseño busca resaltar la belleza natural del tallo y la flor, con una presentación limpia y precisa.\n\nFirmada con el listón 1310 Studio.\n\nEvoca: simplicidad, claridad, sofisticación moderna.\nIdeal para: detalles elegantes, espacios pequeños, estética minimalista.",
                    'destacado'    => true,
                    'precio_lista' => 150.00,
                    'precio_venta' => 100.00,
                    'estado'       => true,
                ],
                'categoria' => $catOrquideas,
                'flores'    => ['orquidea-phalaenopsis'],
            ],
        ];

        foreach ($productos as $item) {
            // Verificar si ya existe por slug
            $existe = DB::table('productos')->where('slug', $item['producto']['slug'])->exists();
            if ($existe) continue;

            // Insertar producto
            $productoId = DB::table('productos')->insertGetId([
                ...$item['producto'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar categoría
            DB::table('productos_categorias')->insertOrIgnore([
                'id_producto'  => $productoId,
                'id_categoria' => $item['categoria'],
            ]);

            // Asignar flores
            foreach ($item['flores'] as $florSlug) {
                $florId = $flores[$florSlug] ?? null;
                if ($florId) {
                    DB::table('productos_flores')->insertOrIgnore([
                        'id_producto' => $productoId,
                        'id_flor'     => $florId,
                    ]);
                }
            }
        }
    }
}