<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Planilla;

/**
 * Modelo Eloquent para los certificados emitidos a los alumnos.
 *
 * Un certificado representa la constancia formal de que un alumno completo
 * satisfactoriamente un curso dentro de una cohorte especifica. Contiene
 * informacion de registro (libro, folio), codigos de verificacion, fecha
 * de emision, estado, firmantes y la plantilla PDF asignada.
 *
 * Generacion automatica en el evento 'creating':
 * - codigo_verificacion_app: Codigo alfanumerico unico de 10 caracteres (CERT-XXXXXXXXXX).
 * - uuid_seguridad: UUID v4 para verificacion adicional de autenticidad.
 *
 * Relaciones:
 * - alumno(): El alumno al que pertenece el certificado.
 * - cohorte(): La cohorte en la que se emitio el certificado.
 * - estadoRelacion(): El estado actual del certificado (valido, anulado, etc.).
 * - firma1(), firma2(), firma3(): Las tres autoridades firmantes.
 * - plantillaPdf(): Plantilla PDF asignada individualmente (opcional).
 *
 * @see CertificadoPdfService  Servicio que genera el PDF usando este modelo.
 */
class Certificado extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos.
     *
     * @var string
     */
    protected $table = 'certificados';

    /**
     * Campos que se pueden asignar masivamente.
     *
     * Incluye todos los campos editables del certificado:
     * - alumno_id, cohorte_id: Relaciones obligatorias.
     * - libro, folio: Registro fisico del certificado.
     * - codigo_registro_manual: Codigo de registro institucional opcional.
     * - codigo_verificacion_app: Codigo unico generado por el sistema (auto en boot).
     * - uuid_seguridad: UUID para verificacion (auto en boot).
     * - fecha_emision: Fecha de emision del certificado.
     * - estado_id: FK al catalogo de estados (tabla estatus).
     * - estado: Campo de texto para el estado (ej: 'valido', 'anulado').
     * - creado_por_usuario_id: Usuario que creo el certificado (opcional).
     * - firma_1_id, firma_2_id, firma_3_id: FKs a las autoridades firmantes.
     * - temario_snapshot: JSON con el temario del curso al momento de la emision.
     * - pdf_path, qr_path: Rutas de archivos generados (PDF y QR).
     * - plantilla_pdf_id: FK a la plantilla PDF asignada individualmente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'alumno_id',
        'cohorte_id',
        'libro',
        'folio',
        'codigo_registro_manual',
        'codigo_verificacion_app',
        'uuid_seguridad',
        'fecha_emision',
        'estado_id',
        'estado',
        'creado_por_usuario_id',
        'firma_1_id',
        'firma_2_id',
        'firma_3_id',
        'temario_snapshot',
        'pdf_path',
        'qr_path',
        'plantilla_pdf_id',
        'planilla_id',
    ];

    /**
     * Conversiones de tipo para atributos del modelo.
     *
     * - fecha_emision se castea a Carbon para poder usar metodos de fecha (format, diffForHumans, etc.).
     * - temario_snapshot se castea a array para decodificar automaticamente el JSON almacenado.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_emision' => 'date',
        'temario_snapshot' => 'array',
    ];

    /**
     * Metodo boot del modelo. Se ejecuta una vez cuando el modelo se inicializa.
     *
     * Registra un observer para el evento 'creating' que genera automaticamente:
     * - codigo_verificacion_app: Cadena alfanumerica aleatoria de 10 caracteres en mayusculas.
     *   Se usa como identificador legible del certificado (ej: 'A3B7K9M2X1').
     *   Solo se genera si el campo esta vacio, permitiendo asignar uno manualmente.
     * - uuid_seguridad: UUID v4 estandar (ej: '550e8400-e29b-41d4-a716-446655440000').
     *   Se usa como token de seguridad para verificacion de autenticidad.
     *   Solo se genera si el campo esta vacio.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificado) {
            if (empty($certificado->codigo_verificacion_app)) {
                $certificado->codigo_verificacion_app = strtoupper(Str::random(10));
            }
            if (empty($certificado->uuid_seguridad)) {
                $certificado->uuid_seguridad = Str::uuid();
            }
        });
    }

    /**
     * Relacion: El alumno al que pertenece este certificado.
     * Un alumno puede tener multiples certificados (uno por cada cohorte completada).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    /**
     * Relacion: La cohorte en la que se emitio este certificado.
     * A traves de la cohorte se accede al curso (cohorte.curso) y a la plantilla
     * PDF de la cohorte (cohorte.plantilla_pdf_id) como segundo nivel de prioridad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class);
    }

    /**
     * Relacion: El estado del certificado desde el catalogo de estados.
     * Se llama 'estadoRelacion' (no 'estado') porque ya existe un campo de texto
     * llamado 'estado' en el modelo, y usar el mismo nombre causaria conflictos.
     * La FK es 'estado_id' apuntando a la tabla 'estatus'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estadoRelacion()
    {
        return $this->belongsTo(Estatus::class, 'estado_id');
    }

    /**
     * Relacion: Primera autoridad firmante del certificado.
     * Referencia a la tabla autoridades mediante la FK firma_1_id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firma1()
    {
        return $this->belongsTo(Autoridad::class, 'firma_1_id');
    }

    /**
     * Relacion: Segunda autoridad firmante del certificado.
     * Referencia a la tabla autoridades mediante la FK firma_2_id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firma2()
    {
        return $this->belongsTo(Autoridad::class, 'firma_2_id');
    }

    /**
     * Relacion: Tercera autoridad firmante del certificado.
     * Referencia a la tabla autoridades mediante la FK firma_3_id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firma3()
    {
        return $this->belongsTo(Autoridad::class, 'firma_3_id');
    }

    /**
     * Relacion: Plantilla PDF asignada individualmente a este certificado.
     *
     * Esta plantilla tiene la mayor prioridad al generar el PDF.
     * Si es null, se usa la plantilla de la cohorte; si esa tambien es null,
     * se usa la plantilla predeterminada del sistema.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plantillaPdf()
    {
        return $this->belongsTo(PlantillaPdf::class);
    }

    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }
}
