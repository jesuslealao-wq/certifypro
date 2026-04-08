<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClasesPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $modulos = DB::table('modulos')->select('id', 'titulo_modulo')->get();

        foreach ($modulos as $modulo) {
            $clases = [];

            if ($modulo->titulo_modulo === 'Fundamentos de Proyectos') {
                $clases = [
                    ['titulo_clase' => 'Introduccion y conceptos basicos', 'orden' => 1],
                    ['titulo_clase' => 'Ciclo de vida del proyecto', 'orden' => 2],
                ];
            } elseif ($modulo->titulo_modulo === 'Planificacion y Alcance') {
                $clases = [
                    ['titulo_clase' => 'Recoleccion de requerimientos', 'orden' => 1],
                    ['titulo_clase' => 'EDT y control de cambios', 'orden' => 2],
                ];
            } elseif ($modulo->titulo_modulo === 'Cronograma y Costos') {
                $clases = [
                    ['titulo_clase' => 'Ruta critica y estimaciones', 'orden' => 1],
                    ['titulo_clase' => 'Presupuesto y valor ganado', 'orden' => 2],
                ];
            } elseif ($modulo->titulo_modulo === 'Funciones y Formulas Avanzadas') {
                $clases = [
                    ['titulo_clase' => 'BUSCARX, LET y funciones de texto', 'orden' => 1],
                    ['titulo_clase' => 'Matrices dinamicas y funciones modernas', 'orden' => 2],
                ];
            } elseif ($modulo->titulo_modulo === 'Tablas Dinamicas y Power Query') {
                $clases = [
                    ['titulo_clase' => 'Modelo de datos y tablas dinamicas', 'orden' => 1],
                    ['titulo_clase' => 'Power Query: limpieza y transformacion', 'orden' => 2],
                ];
            }

            foreach ($clases as $clase) {
                DB::table('clases')->updateOrInsert(
                    ['modulo_id' => $modulo->id, 'titulo_clase' => $clase['titulo_clase']],
                    [
                        'modulo_id' => $modulo->id,
                        'titulo_clase' => $clase['titulo_clase'],
                        'orden' => $clase['orden'],
                    ]
                );
            }
        }
    }
}
