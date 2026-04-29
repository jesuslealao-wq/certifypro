<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planilla extends Model
{
    use SoftDeletes;

    protected $table = 'planillas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estructura_html',
        'estilos_css',
        'fondo_pagina_1',
        'fondo_pagina_2',
        'activa',
        'es_predeterminada',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'es_predeterminada' => 'boolean',
    ];
}
