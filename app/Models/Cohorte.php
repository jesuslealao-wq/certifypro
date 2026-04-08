<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent para las cohortes de cursos.
 *
 * Una cohorte representa una instancia especifica de un curso que se dicta
 * en un periodo determinado, con un grupo particular de alumnos.
 * Por ejemplo, el curso "Diplomado en Gestion de Proyectos" puede tener
 * la cohorte "COH-2024-001" (enero-junio 2024) y "COH-2024-002" (julio-diciembre 2024).
 *
 * Campos principales:
 * - curso_id: FK al curso academico que se dicta en esta cohorte.
 * - instructor_id: FK a la autoridad que actua como instructor.
 * - fecha_inicio, fecha_fin: Periodo en que se imparte la cohorte.
 * - codigo_cohorte: Codigo identificador unico de la cohorte.
 * - estado_id: FK al catalogo de estados (activa, finalizada, etc.).
 * - modalidad: Forma de dictado (presencial, online, hibrido, etc.).
 * - firma_default_1/2/3_id: Firmantes por defecto para los certificados de esta cohorte.
 * - plantilla_pdf_id: Plantilla PDF asignada a nivel de cohorte.
 *
 * Relaciones:
 * - curso(): El curso academico al que pertenece esta cohorte.
 * - instructor(): La autoridad que actua como instructor.
 * - estado(): El estado actual de la cohorte.
 * - firmaDefault1/2/3(): Autoridades firmantes por defecto.
 * - plantillaPdf(): Plantilla PDF asignada a la cohorte.
 * - certificados(): Todos los certificados emitidos en esta cohorte.
 * - alumnos(): Alumnos inscritos (relacion muchos a muchos via alumno_cohorte).
 *
 * @see CohorteController  Controlador que gestiona las operaciones sobre cohortes.
 */
class Cohorte extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos.
     *
     * @var string
     */
    protected $table = 'cohortes';

    /**
     * Campos que se pueden asignar masivamente mediante create() o update().
     *
     * @var array<string>
     */
    protected $fillable = [
        'curso_id',
        'instructor_id',
        'fecha_inicio',
        'fecha_fin',
        'codigo_cohorte',
        'estado_id',
        'modalidad',
        'firma_default_1_id',
        'firma_default_2_id',
        'firma_default_3_id',
        'plantilla_pdf_id',
    ];

    /**
     * Conversiones de tipo para atributos del modelo.
     * Las fechas se castean a Carbon para facilitar su formateo y manipulacion.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Relacion: El curso academico al que pertenece esta cohorte.
     * Un curso puede tener multiples cohortes en diferentes periodos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Relacion: La autoridad que actua como instructor de la cohorte.
     * Referencia a la tabla autoridades mediante la FK instructor_id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(Autoridad::class, 'instructor_id');
    }

    /**
     * Relacion: El estado actual de la cohorte desde el catalogo de estados.
     * Los estados de cohorte se filtran por entidad = 'cohorte' en el catalogo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estado()
    {
        return $this->belongsTo(Estatus::class, 'estado_id');
    }

    /**
     * Relacion: Primera autoridad firmante por defecto de la cohorte.
     * Este firmante se sugiere automaticamente al generar certificados masivos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaDefault1()
    {
        return $this->belongsTo(Autoridad::class, 'firma_default_1_id');
    }

    /**
     * Relacion: Segunda autoridad firmante por defecto de la cohorte.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaDefault2()
    {
        return $this->belongsTo(Autoridad::class, 'firma_default_2_id');
    }

    /**
     * Relacion: Tercera autoridad firmante por defecto de la cohorte.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaDefault3()
    {
        return $this->belongsTo(Autoridad::class, 'firma_default_3_id');
    }

    /**
     * Relacion: Plantilla PDF asignada a la cohorte.
     *
     * Todos los certificados de esta cohorte que no tengan una plantilla individual
     * usaran esta plantilla al generar el PDF. Es el segundo nivel de prioridad
     * (despues de la plantilla individual del certificado y antes de la predeterminada).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plantillaPdf()
    {
        return $this->belongsTo(PlantillaPdf::class);
    }

    /**
     * Relacion: Todos los certificados emitidos en esta cohorte.
     * Cada alumno que completa la cohorte recibe un certificado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }

    /**
     * Relacion: Alumnos inscritos en esta cohorte.
     *
     * Es una relacion muchos a muchos a traves de la tabla pivote 'alumno_cohorte'.
     * La tabla pivote incluye el campo 'fecha_inscripcion' que registra cuando
     * se inscribio el alumno en la cohorte. Tambien se cargan los timestamps
     * de la tabla pivote (created_at, updated_at).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_cohorte')
            ->withPivot('fecha_inscripcion')
            ->withTimestamps();
    }
}
