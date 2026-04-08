<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clase extends Model
{
    use SoftDeletes;

    protected $table = 'clases';

    protected $fillable = [
        'modulo_id',
        'titulo_clase',
        'orden',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
