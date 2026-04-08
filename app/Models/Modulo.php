<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
    use SoftDeletes;

    protected $table = 'modulos';

    protected $fillable = [
        'curso_id',
        'titulo_modulo',
        'horas_modulo',
        'orden',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function clases()
    {
        return $this->hasMany(Clase::class);
    }
}
