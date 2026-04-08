<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlumnosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $alumnos = [
            [
                'identificacion_nacional' => 'V-12345678',
                'nombre_completo' => 'Maria Valentina Rodriguez Perez',
                'email' => 'maria.rodriguez@email.com',
                'telefono' => '0412-0000001',
            ],
            [
                'identificacion_nacional' => 'V-23456789',
                'nombre_completo' => 'Jose Antonio Martinez Gomez',
                'email' => 'jose.martinez@email.com',
                'telefono' => '0412-0000002',
            ],
            [
                'identificacion_nacional' => 'V-34567890',
                'nombre_completo' => 'Luisa Fernanda Hernandez Silva',
                'email' => 'luisa.hernandez@email.com',
                'telefono' => '0412-0000003',
            ],
        ];

        foreach ($alumnos as $row) {
            DB::table('alumnos')->updateOrInsert(
                ['identificacion_nacional' => $row['identificacion_nacional']],
                $row
            );
        }
    }
}
