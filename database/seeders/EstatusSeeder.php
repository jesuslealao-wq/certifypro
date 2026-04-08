<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusSeeder extends Seeder
{
public function run(): void
{
    $rows = [
        // Cursos
        ['entidad' => 'curso', 'nombre' => 'Borrador', 'descripcion' => 'Curso en preparación, no visible para emisión.', 'orden_visual' => 0],
        ['entidad' => 'curso', 'nombre' => 'Publicado', 'descripcion' => 'Curso habilitado para crear cohortes y emitir.', 'orden_visual' => 10],
        ['entidad' => 'curso', 'nombre' => 'Archivado', 'descripcion' => 'Curso fuera de uso, solo consulta.', 'orden_visual' => 20],

        // Cohortes
        ['entidad' => 'cohorte', 'nombre' => 'Planificada', 'descripcion' => 'Cohorte creada, aún no inicia.', 'orden_visual' => 0],
        ['entidad' => 'cohorte', 'nombre' => 'En curso', 'descripcion' => 'Cohorte actualmente en ejecución.', 'orden_visual' => 10],
        ['entidad' => 'cohorte', 'nombre' => 'Finalizada', 'descripcion' => 'Cohorte cerrada.', 'orden_visual' => 20],
        ['entidad' => 'cohorte', 'nombre' => 'Cancelada', 'descripcion' => 'Cohorte cancelada.', 'orden_visual' => 30],

        // Certificados
        ['entidad' => 'certificado', 'nombre' => 'Emitido', 'descripcion' => 'Certificado emitido y verificable.', 'orden_visual' => 10],
        ['entidad' => 'certificado', 'nombre' => 'Anulado', 'descripcion' => 'Certificado anulado.', 'orden_visual' => 20],
        
        // Autoridades
        ['entidad' => 'autoridad', 'nombre' => 'Activo', 'descripcion' => 'Autoridad activa.', 'orden_visual' => 10],
        ['entidad' => 'autoridad', 'nombre' => 'Inactivo', 'descripcion' => 'Autoridad inactiva.', 'orden_visual' => 20],
    
    ];
    

    \Illuminate\Support\Facades\DB::table('estatus')->upsert(
        $rows,
        ['entidad', 'nombre'],
        ['descripcion', 'orden_visual']
    );
}
}
