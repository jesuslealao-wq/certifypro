<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estatus extends Model
{
    use SoftDeletes;

    protected $table = 'estatus';

    protected $fillable = [
        'entidad',
        'nombre',
        'descripcion',
        'orden_visual',
    ];
}
