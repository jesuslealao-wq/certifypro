<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Autoridad extends Model
{
    use SoftDeletes;

    protected $table = 'autoridades';

    protected $fillable = [
        'nombre_completo',
        'cargo',
        'especialidad',
        'firma_path',
        'sello_path',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cohortesComoInstructor()
    {
        return $this->hasMany(Cohorte::class, 'instructor_id');
    }

    public function certificadosFirma1()
    {
        return $this->hasMany(Certificado::class, 'firma_1_id');
    }

    public function certificadosFirma2()
    {
        return $this->hasMany(Certificado::class, 'firma_2_id');
    }

    public function certificadosFirma3()
    {
        return $this->hasMany(Certificado::class, 'firma_3_id');
    }
}
