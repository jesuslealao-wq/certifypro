<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoridadesPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $autoridades = [
            [
                'nombre_completo' => 'Dra. Ana Maria Gutierrez',
                'cargo' => 'Directora Academica',
                'especialidad' => 'Educacion Superior',
                'firma_path' => null,
                'sello_path' => null,
                'activo' => true,
            ],
            [
                'nombre_completo' => 'MSc. Carlos Enrique Paredes',
                'cargo' => 'Coordinador de Programa',
                'especialidad' => 'Gestion de Proyectos',
                'firma_path' => null,
                'sello_path' => null,
                'activo' => true,
            ],
            [
                'nombre_completo' => 'Ing. Maria Fernanda Salas',
                'cargo' => 'Rectora',
                'especialidad' => 'Ingenieria Industrial',
                'firma_path' => null,
                'sello_path' => null,
                'activo' => true,
            ],
            [
                'nombre_completo' => 'Lic. Juan Pablo Medina',
                'cargo' => 'Instructor',
                'especialidad' => 'Docencia',
                'firma_path' => null,
                'sello_path' => null,
                'activo' => true,
            ],
        ];

        foreach ($autoridades as $row) {
            DB::table('autoridades')->updateOrInsert(
                ['nombre_completo' => $row['nombre_completo'], 'cargo' => $row['cargo']],
                $row
            );
        }
    }
}
