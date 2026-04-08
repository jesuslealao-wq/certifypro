<?php

/**
 * Archivo de definicion de rutas web del sistema de certificados.
 *
 * Todas las rutas del sistema se definen aqui, organizadas por modulo.
 * Cada modulo tiene un grupo de rutas resource (CRUD estandar de Laravel)
 * mas rutas adicionales para funcionalidades especificas como papelera
 * (soft delete), generacion de PDF y operaciones masivas.
 *
 * Convenciones de rutas:
 * - Los recursos usan Route::resource() que genera las 7 rutas CRUD estandar.
 * - Las rutas de papelera siguen el patron: {recurso}/papelera/index, {recurso}/{id}/restore, {recurso}/{id}/force-delete.
 * - Las rutas de PDF usan GET porque solo consultan/generan datos sin modificar estado.
 * - Las rutas de acciones que modifican datos usan POST o PUT segun corresponda.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\EstatusController;
use App\Http\Controllers\AutoridadController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\CohorteController;
use App\Http\Controllers\CertificadoController;

// -- Pagina principal: Dashboard del sistema --
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// -- Modulo Estatus: Catalogo de estados reutilizable por entidad (curso, cohorte, certificado) --
Route::resource('estatus', EstatusController::class);
Route::get('estatus/papelera/index', [EstatusController::class, 'papelera'])->name('estatus.papelera');
Route::post('estatus/{id}/restore', [EstatusController::class, 'restore'])->name('estatus.restore');
Route::delete('estatus/{id}/force-delete', [EstatusController::class, 'forceDelete'])->name('estatus.forceDelete');

// -- Modulo Cursos: Gestion de cursos academicos --
Route::resource('cursos', CursoController::class);
Route::get('cursos/papelera/index', [CursoController::class, 'papelera'])->name('cursos.papelera');
Route::post('cursos/{id}/restore', [CursoController::class, 'restore'])->name('cursos.restore');
Route::delete('cursos/{id}/force-delete', [CursoController::class, 'forceDelete'])->name('cursos.forceDelete');

// -- Modulo Modulos: Modulos que componen un curso --
Route::resource('modulos', ModuloController::class);
Route::get('modulos/papelera/index', [ModuloController::class, 'papelera'])->name('modulos.papelera');
Route::post('modulos/{id}/restore', [ModuloController::class, 'restore'])->name('modulos.restore');
Route::delete('modulos/{id}/force-delete', [ModuloController::class, 'forceDelete'])->name('modulos.forceDelete');

// -- Modulo Clases: Clases individuales dentro de cada modulo --
Route::resource('clases', ClaseController::class);
Route::get('clases/papelera/index', [ClaseController::class, 'papelera'])->name('clases.papelera');
Route::post('clases/{id}/restore', [ClaseController::class, 'restore'])->name('clases.restore');
Route::delete('clases/{id}/force-delete', [ClaseController::class, 'forceDelete'])->name('clases.forceDelete');

// -- Modulo Autoridades: Personas con cargo institucional (firmantes, instructores) --
// El parametro personalizado 'autoridad' evita que Laravel use 'autoridade' (pluralizacion incorrecta)
Route::resource('autoridades', AutoridadController::class)->parameters(['autoridades' => 'autoridad']);
Route::get('autoridades/papelera/index', [AutoridadController::class, 'papelera'])->name('autoridades.papelera');
Route::post('autoridades/{id}/restore', [AutoridadController::class, 'restore'])->name('autoridades.restore');
Route::delete('autoridades/{id}/force-delete', [AutoridadController::class, 'forceDelete'])->name('autoridades.forceDelete');

// -- Modulo Alumnos: Estudiantes registrados en el sistema --
Route::resource('alumnos', AlumnoController::class);
Route::get('alumnos/papelera/index', [AlumnoController::class, 'papelera'])->name('alumnos.papelera');
Route::post('alumnos/{id}/restore', [AlumnoController::class, 'restore'])->name('alumnos.restore');
Route::delete('alumnos/{id}/force-delete', [AlumnoController::class, 'forceDelete'])->name('alumnos.forceDelete');

// -- Modulo Cohortes: Grupos de alumnos por curso y periodo --
Route::resource('cohortes', CohorteController::class);
Route::get('cohortes/papelera/index', [CohorteController::class, 'papelera'])->name('cohortes.papelera');
Route::post('cohortes/{id}/restore', [CohorteController::class, 'restore'])->name('cohortes.restore');
Route::delete('cohortes/{id}/force-delete', [CohorteController::class, 'forceDelete'])->name('cohortes.forceDelete');
// Rutas adicionales de cohortes: gestion de alumnos
Route::post('cohortes/{cohorte}/alumnos/agregar', [CohorteController::class, 'agregarAlumnos'])->name('cohortes.alumnos.agregar');
Route::delete('cohortes/{cohorte}/alumnos/{alumno}', [CohorteController::class, 'removerAlumno'])->name('cohortes.alumnos.remover');
// Rutas adicionales de cohortes: generacion y configuracion masiva de certificados
Route::post('cohortes/{cohorte}/generar-certificados-masivo', [CohorteController::class, 'generarCertificadosMasivo'])->name('cohortes.generar-certificados-masivo');
Route::post('cohortes/{cohorte}/asignar-plantilla', [CohorteController::class, 'asignarPlantilla'])->name('cohortes.asignar-plantilla');
Route::post('cohortes/{cohorte}/configurar-certificados-masivo', [CohorteController::class, 'configurarCertificadosMasivo'])->name('cohortes.configurar-certificados-masivo');
Route::put('cohortes/{cohorte}/certificados/{certificado}', [CohorteController::class, 'actualizarCertificadoIndividual'])->name('cohortes.certificados.update');
// Rutas de generacion de PDF masivo para todos los certificados de una cohorte
Route::get('cohortes/{cohorte}/generar-pdfs', [CohorteController::class, 'generarPdfsMasivo'])->name('cohortes.generar-pdfs');
Route::get('cohortes/{cohorte}/descargar-pdfs', [CohorteController::class, 'descargarPdfsMasivo'])->name('cohortes.descargar-pdfs');

// -- Modulo Certificados: Generacion de PDF individual (ver en navegador y descargar) --
// Estas rutas se definen ANTES del resource para que Laravel no las confunda con certificados/{certificado} (show)
Route::get('certificados/{certificado}/pdf', [CertificadoController::class, 'generarPdf'])->name('certificados.pdf');
Route::get('certificados/{certificado}/descargar-pdf', [CertificadoController::class, 'descargarPdf'])->name('certificados.descargar-pdf');
// CRUD estandar de certificados
Route::resource('certificados', CertificadoController::class);
Route::get('certificados/papelera/index', [CertificadoController::class, 'papelera'])->name('certificados.papelera');
Route::post('certificados/{id}/restore', [CertificadoController::class, 'restore'])->name('certificados.restore');
Route::delete('certificados/{id}/force-delete', [CertificadoController::class, 'forceDelete'])->name('certificados.forceDelete');

// -- Modulo Plantillas PDF: Editor visual de plantillas HTML/CSS para certificados --
// Se excluye la ruta 'show' porque se usa el editor (edit) como vista principal de la plantilla
Route::resource('plantillas-pdf', \App\Http\Controllers\PlantillaPdfController::class)->except(['show']);
// Rutas adicionales de plantillas: vista previa, duplicar, subir/eliminar fondos
Route::get('plantillas-pdf/{plantillas_pdf}/preview', [\App\Http\Controllers\PlantillaPdfController::class, 'preview'])->name('plantillas-pdf.preview');
Route::post('plantillas-pdf/{plantillas_pdf}/duplicar', [\App\Http\Controllers\PlantillaPdfController::class, 'duplicar'])->name('plantillas-pdf.duplicar');
Route::post('plantillas-pdf/{plantillas_pdf}/upload-fondo', [\App\Http\Controllers\PlantillaPdfController::class, 'uploadFondo'])->name('plantillas-pdf.upload-fondo');
Route::post('plantillas-pdf/{plantillas_pdf}/remove-fondo', [\App\Http\Controllers\PlantillaPdfController::class, 'removeFondo'])->name('plantillas-pdf.remove-fondo');

// -- Rutas anidadas: Modulos dentro de cursos y clases dentro de modulos --
Route::post('cursos/{curso}/modulos', [ModuloController::class, 'store'])->name('cursos.modulos.store');
Route::patch('modulos/{modulo}', [ModuloController::class, 'update'])->name('modulos.update');
Route::post('modulos/{modulo}/clases', [ClaseController::class, 'store'])->name('modulos.clases.store');

// -- Rutas API internas: Usadas por JavaScript para operaciones inline sin recargar la pagina --
Route::prefix('api')->group(function () {
    // Route::get('/cursos', [CursoApiController::class, 'index']);
    // Route::post('/cursos', [CursoApiController::class, 'store']);
    // Route::get('/estatus/curso', [CursoApiController::class, 'estatusCurso']);
    
    // API para gestión inline de módulos y clases
    Route::post('/cursos/{curso}/modulos', [ModuloController::class, 'storeApi']);
    Route::delete('/modulos/{modulo}', [ModuloController::class, 'destroyApi']);
    Route::post('/modulos/{modulo}/clases', [ClaseController::class, 'storeApi']);
    Route::delete('/clases/{clase}', [ClaseController::class, 'destroyApi']);
});