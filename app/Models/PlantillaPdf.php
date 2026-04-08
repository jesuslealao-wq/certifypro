<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent para las plantillas PDF de certificados.
 *
 * Cada plantilla define el diseno visual de los certificados mediante HTML y CSS editables.
 * Las plantillas soportan variables dinamicas que se sustituyen por los datos reales
 * del certificado al momento de generar el PDF (por ejemplo, {{ $alumno_nombre }}).
 *
 * Campos principales:
 * - nombre: Nombre identificador de la plantilla.
 * - descripcion: Descripcion opcional del proposito o uso de la plantilla.
 * - contenido_html: Codigo HTML del cuerpo del certificado con variables.
 * - estilos_css: Codigo CSS personalizado para el diseno.
 * - fondo_pagina_1: Ruta relativa de la imagen de fondo para la pagina 1 (storage).
 * - fondo_pagina_2: Ruta relativa de la imagen de fondo para la pagina 2 (storage).
 * - orientacion: Orientacion del papel ('portrait' o 'landscape').
 * - tamano_papel: Tamano del papel ('a4', 'letter' o 'legal').
 * - activa: Indica si la plantilla esta disponible para ser asignada.
 * - es_predeterminada: Indica si es la plantilla por defecto del sistema (solo una a la vez).
 *
 * Relaciones inversas (no definidas aqui, pero existen en los modelos relacionados):
 * - Certificado::plantillaPdf() - Certificados que usan esta plantilla individualmente.
 * - Cohorte::plantillaPdf() - Cohortes que tienen esta plantilla asignada.
 *
 * @see CertificadoPdfService  Servicio que usa esta plantilla para generar los PDF.
 */
class PlantillaPdf extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos.
     * Se especifica explicitamente porque el nombre no sigue la convencion
     * de pluralizacion en ingles de Laravel (plantilla_pdfs vs plantillas_pdf).
     *
     * @var string
     */
    protected $table = 'plantillas_pdf';

    /**
     * Campos que se pueden asignar masivamente mediante create() o update().
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'contenido_html',
        'estilos_css',
        'fondo_pagina_1',
        'fondo_pagina_2',
        'orientacion',
        'tamano_papel',
        'activa',
        'es_predeterminada',
    ];

    /**
     * Conversiones de tipo para atributos del modelo.
     * Los campos booleanos se castean para que Eloquent los devuelva como true/false
     * en lugar de 1/0, facilitando su uso en condiciones y vistas Blade.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activa' => 'boolean',
        'es_predeterminada' => 'boolean',
    ];

    /**
     * Retorna la lista de todas las variables disponibles para usar en las plantillas.
     *
     * Cada clave del array es el nombre de la variable (sin los delimitadores {{ $ }}).
     * Cada valor es una descripcion legible que se muestra en el editor visual
     * para que el usuario sepa que dato representa cada variable.
     *
     * Estas variables se usan en CertificadoPdfService::extraerDatos() para construir
     * el array de datos que se sustituye en el HTML.
     *
     * Para agregar una nueva variable al sistema:
     * 1. Agregarla en este array con su descripcion.
     * 2. Agregarla en el metodo datosDePrueba() con un valor de ejemplo.
     * 3. Agregarla en CertificadoPdfService::extraerDatos() con la extraccion real del dato.
     * 4. Agregarla en CertificadoPdfService::extraerDatos()::$defaults con su valor por defecto.
     *
     * @return array<string, string>  Clave = nombre_variable, valor = descripcion.
     */
    public static function variablesDisponibles(): array
    {
        return [
            'alumno_nombre' => 'Nombre completo del alumno',
            'alumno_identificacion' => 'Cédula / Identificación',
            'alumno_email' => 'Correo electrónico del alumno',
            'curso_nombre' => 'Nombre del curso',
            'curso_horas' => 'Horas académicas',
            'curso_descripcion' => 'Descripción del curso',
            'cohorte_codigo' => 'Código de la cohorte',
            'cohorte_modalidad' => 'Modalidad (presencial, online, etc.)',
            'cohorte_fecha_inicio' => 'Fecha de inicio de la cohorte',
            'cohorte_fecha_fin' => 'Fecha de fin de la cohorte',
            'certificado_fecha_emision' => 'Fecha de emisión del certificado',
            'certificado_codigo' => 'Código de verificación',
            'certificado_libro' => 'Número de libro',
            'certificado_folio' => 'Número de folio',
            'certificado_registro' => 'Código de registro manual',
        ];
    }

    /**
     * Retorna datos de prueba para la vista previa de las plantillas.
     *
     * Estos datos se usan en dos lugares:
     * 1. En PlantillaPdfController::preview() para mostrar la vista previa con datos realistas.
     * 2. En el editor visual (JavaScript) para la vista previa en vivo mientras se edita.
     *
     * Los valores deben ser representativos de datos reales para que el usuario
     * pueda evaluar correctamente el diseno de la plantilla.
     *
     * @return array<string, string>  Clave = nombre_variable, valor = dato de ejemplo.
     */
    public static function datosDePrueba(): array
    {
        return [
            'alumno_nombre' => 'MARÍA VALENTINA RODRÍGUEZ PÉREZ',
            'alumno_identificacion' => 'V-12.345.678',
            'alumno_email' => 'maria.rodriguez@email.com',
            'curso_nombre' => 'Diplomado en Gestión de Proyectos',
            'curso_horas' => '120',
            'curso_descripcion' => 'Programa integral de formación en gestión y dirección de proyectos bajo estándares internacionales.',
            'cohorte_codigo' => 'COH-2024-001',
            'cohorte_modalidad' => 'Presencial',
            'cohorte_fecha_inicio' => '15/01/2024',
            'cohorte_fecha_fin' => '15/06/2024',
            'certificado_fecha_emision' => '20/06/2024',
            'certificado_codigo' => 'CERT-A1B2C3D4E5',
            'certificado_libro' => '1',
            'certificado_folio' => '42',
            'certificado_registro' => 'REG-2024-0042',
        ];
    }
}
