<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Manuel',
            'lastname' => 'Marmolejo',
            'email' => 'marmocreativo@gmail.com',
            'password' => 'Angeles1#',
            'role' => 'admin'
        ]);

        $this->call(PaginasSeeder::class);
        $this->call(CategoriasSeeder::class);
        $this->call(DirectorioFloralSeeder::class);
        $this->call(ProductosSeeder::class);
        $this->call(TalleresSeeder::class);
        $this->call(ZonasEnvioSeeder::class);
        $this->call(EstadosMunicipiosSeeder::class);
        
    }
}
