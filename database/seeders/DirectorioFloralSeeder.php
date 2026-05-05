<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectorioFloralSeeder extends Seeder
{
    public function run(): void
    {
        $flores = [
            [
                'nombre'    => 'Anémona',
                'slug'      => 'anemona',
                'categoria' => 'Silvestre',
                'descripcion' => 'Flor delicada de pétalos suaves y centro oscuro. Aporta drama y contraste en composiciones florales.',
                'estado'    => true,
                'orden'     => 1,
            ],
            [
                'nombre'    => 'Gerbera',
                'slug'      => 'gerbera',
                'categoria' => 'Tropical',
                'descripcion' => 'Flor radiante con pétalos amplios y colores vibrantes. Símbolo de alegría y energía positiva.',
                'estado'    => true,
                'orden'     => 2,
            ],
            [
                'nombre'    => 'Rosa',
                'slug'      => 'rosa',
                'categoria' => 'Clásica',
                'descripcion' => 'La flor más icónica del mundo floral. Disponible en múltiples variedades y colores, símbolo universal de elegancia.',
                'estado'    => true,
                'orden'     => 3,
            ],
            [
                'nombre'    => 'Crisantemo',
                'slug'      => 'crisantemo',
                'categoria' => 'Clásica',
                'descripcion' => 'Flor densa y voluminosa con larga vida útil. Aporta textura y cuerpo a cualquier composición.',
                'estado'    => true,
                'orden'     => 4,
            ],
            [
                'nombre'    => 'Lilies',
                'slug'      => 'lilies',
                'categoria' => 'Clásica',
                'descripcion' => 'Flor de gran presencia y fragancia característica. Aporta verticalidad y elegancia a los arreglos.',
                'estado'    => true,
                'orden'     => 5,
            ],
            [
                'nombre'    => 'Hipericum',
                'slug'      => 'hipericum',
                'categoria' => 'Follaje',
                'descripcion' => 'Baya decorativa que aporta textura y color natural. Complemento ideal en composiciones orgánicas.',
                'estado'    => true,
                'orden'     => 6,
            ],
            [
                'nombre'    => 'Lisianthus',
                'slug'      => 'lisianthus',
                'categoria' => 'Silvestre',
                'descripcion' => 'Flor de pétalos ondulados y apariencia delicada. Evoca elegancia romántica y sofisticación discreta.',
                'estado'    => true,
                'orden'     => 7,
            ],
            [
                'nombre'    => 'Kalanchoe',
                'slug'      => 'kalanchoe',
                'categoria' => 'Silvestre',
                'descripcion' => 'Flor pequeña y vibrante, agrupada en racimos. Aporta color y alegría con su larga duración.',
                'estado'    => true,
                'orden'     => 8,
            ],
            [
                'nombre'    => 'Yuko',
                'slug'      => 'yuko',
                'categoria' => 'Follaje',
                'descripcion' => 'Follaje decorativo de origen asiático. Aporta estructura y naturalidad a composiciones orgánicas.',
                'estado'    => true,
                'orden'     => 9,
            ],
            [
                'nombre'    => 'Tulipán',
                'slug'      => 'tulipan',
                'categoria' => 'Clásica',
                'descripcion' => 'Flor icónica de tallo largo y forma limpia. Símbolo de minimalismo y elegancia contemporánea.',
                'estado'    => true,
                'orden'     => 10,
            ],
            [
                'nombre'    => 'Hortensia',
                'slug'      => 'hortensia',
                'categoria' => 'Clásica',
                'descripcion' => 'Flor de gran volumen formada por pequeñas flores agrupadas. Ideal para composiciones de gran presencia visual.',
                'estado'    => true,
                'orden'     => 11,
            ],
            [
                'nombre'    => 'Ranúnculo',
                'slug'      => 'ranunculo',
                'categoria' => 'Clásica',
                'descripcion' => 'Flor de múltiples capas de pétalos con apariencia lujosa. Aporta profundidad y sofisticación a los arreglos.',
                'estado'    => true,
                'orden'     => 12,
            ],
            [
                'nombre'    => 'Snapdragon',
                'slug'      => 'snapdragon',
                'categoria' => 'Silvestre',
                'descripcion' => 'Flor vertical de formas dramáticas que aporta altura y dinamismo a las composiciones.',
                'estado'    => true,
                'orden'     => 13,
            ],
            [
                'nombre'    => 'Rosa inglesa',
                'slug'      => 'rosa-inglesa',
                'categoria' => 'Clásica',
                'descripcion' => 'Variedad premium de rosa con pétalos densamente agrupados. Máxima expresión del lujo floral.',
                'estado'    => true,
                'orden'     => 14,
            ],
            [
                'nombre'    => 'Orquídea Phalaenopsis',
                'slug'      => 'orquidea-phalaenopsis',
                'categoria' => 'Orquídeas',
                'descripcion' => 'La reina de las orquídeas. Elegancia arquitectónica, larga duración y presencia escultórica inigualable.',
                'estado'    => true,
                'orden'     => 15,
            ],
        ];

        foreach ($flores as $flor) {
            DB::table('directorio_floral')->insertOrIgnore([
                ...$flor,
                'contenido'  => null,
                'imagen'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}