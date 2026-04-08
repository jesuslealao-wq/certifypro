<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Estatus;
use App\Models\Modulo;
use App\Models\Cohorte;

class Curso extends Model
{
    use SoftDeletes;

    protected $table = 'cursos';

    protected $fillable = [
        'nombre_curso',
        'horas_academicas',
        'estado_id',
        'descripcion',
    ];

    public function estado()
    {
        return $this->belongsTo(Estatus::class, 'estado_id');
    }

    public function modulos()
    {
        return $this->hasMany(Modulo::class);
    }

    public function cohortes()
    {
        return $this->hasMany(Cohorte::class);
    }
}