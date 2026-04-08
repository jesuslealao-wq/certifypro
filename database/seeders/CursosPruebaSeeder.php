<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CursosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $estatusPublicadoId = DB::table('estatus')
            ->where('entidad', 'curso')
            ->where('nombre', 'Publicado')
            ->value('id');

        if (!$estatusPublicadoId) {
            $estatusPublicadoId = DB::table('estatus')
                ->where('entidad', 'curso')
                ->orderBy('orden_visual')
                ->value('id');
        }

        $columnaEstadoCurso = 'estatus_id';
        if (Schema::hasColumn('cursos', 'estado_id')) {
            $columnaEstadoCurso = 'estado_id';
        }

        $cursos = [
            [
                'nombre_curso' => 'Diplomado en Gestion de Proyectos',
                'horas_academicas' => 120,
                'descripcion' => 'Programa integral orientado a metodologias y buenas practicas para la direccion de proyectos.',
            ],
            [
                'nombre_curso' => 'Excel Avanzado para Analisis de Datos',
                'horas_academicas' => 60,
                'descripcion' => 'Formacion practica en funciones, tablas dinamicas y modelado de datos en Excel.',
            ],
        ];

        foreach ($cursos as $curso) {
            DB::table('cursos')->updateOrInsert(
                ['nombre_curso' => $curso['nombre_curso']],
                [
                    $columnaEstadoCurso => $estatusPublicadoId,
                    'nombre_curso' => $curso['nombre_curso'],
                    'horas_academicas' => $curso['horas_academicas'],
                    'descripcion' => $curso['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
