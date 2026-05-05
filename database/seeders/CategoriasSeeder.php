<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'titulo'   => 'Ramos',
                'slug'     => 'ramos',
                'resumen'  => 'Ramos artesanales elaborados con flores de temporada, envueltos en papel coreano premium y firmados con el listón 1310 Studio.',
                'id_padre' => null,
                'estado'   => 'publicado',
            ],
            [
                'titulo'   => 'Orquídeas',
                'slug'     => 'orquideas',
                'resumen'  => 'Composiciones minimalistas con orquídeas Phalaenopsis de la más alta calidad, diseñadas para trascender lo floral.',
                'id_padre' => null,
                'estado'   => 'publicado',
            ],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias')->insertOrIgnore([
                ...$categoria,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}