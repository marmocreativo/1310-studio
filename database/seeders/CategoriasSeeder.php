<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::insert([
            [
                'titulo'     => 'Ramos',
                'slug'       => 'ramos',
                'resumen'    => 'Hermosos ramos de flores para toda ocasión.',
                'id_padre'   => null,
                'imagen'     => null,
                'estado'     => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo'     => 'Orquídeas',
                'slug'       => 'orquideas',
                'resumen'    => 'Exquisitas orquídeas naturales para decorar tus espacios.',
                'id_padre'   => null,
                'imagen'     => null,
                'estado'     => 'publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}