<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumno extends Model
{
    use SoftDeletes;

    protected $table = 'alumnos';

    protected $fillable = [
        'identificacion_nacional',
        'nombre_completo',
        'email',
        'telefono',
    ];

    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }

    public function cohortes()
    {
        return $this->belongsToMany(Cohorte::class, 'alumno_cohorte')
            ->withPivot('fecha_inscripcion')
            ->withTimestamps();
    }
}
