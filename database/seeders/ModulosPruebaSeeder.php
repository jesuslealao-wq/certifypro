<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $cursoGestionId = DB::table('cursos')->where('nombre_curso', 'Diplomado en Gestion de Proyectos')->value('id');
        $cursoExcelId = DB::table('cursos')->where('nombre_curso', 'Excel Avanzado para Analisis de Datos')->value('id');

        $map = [];
        if ($cursoGestionId) {
            $map[$cursoGestionId] = [
                ['titulo_modulo' => 'Fundamentos de Proyectos', 'orden' => 1],
                ['titulo_modulo' => 'Planificacion y Alcance', 'orden' => 2],
                ['titulo_modulo' => 'Cronograma y Costos', 'orden' => 3],
            ];
        }
        if ($cursoExcelId) {
            $map[$cursoExcelId] = [
                ['titulo_modulo' => 'Funciones y Formulas Avanzadas', 'orden' => 1],
                ['titulo_modulo' => 'Tablas Dinamicas y Power Query', 'orden' => 2],
            ];
        }

        foreach ($map as $cursoId => $modulos) {
            foreach ($modulos as $modulo) {
                DB::table('modulos')->updateOrInsert(
                    ['curso_id' => $cursoId, 'titulo_modulo' => $modulo['titulo_modulo']],
                    [
                        'curso_id' => $cursoId,
                        'titulo_modulo' => $modulo['titulo_modulo'],
                        'orden' => $modulo['orden'],
                    ]
                );
            }
        }
    }
}
