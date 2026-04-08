<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EstatusSeeder::class,
            PlantillaPdfSeeder::class,
            AutoridadesPruebaSeeder::class,
            CursosPruebaSeeder::class,
            ModulosPruebaSeeder::class,
            ClasesPruebaSeeder::class,
            AlumnosPruebaSeeder::class,
        ]);
    }
}
