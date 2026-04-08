<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Alumno;
use App\Models\Cohorte;
use App\Models\Autoridad;
use App\Models\Estatus;
use App\Services\CertificadoPdfService;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

/**
 * Controlador para la gestion de certificados individuales.
 *
 * Maneja las operaciones CRUD completas sobre certificados:
 * - Listar todos los certificados con paginacion.
 * - Crear un certificado nuevo asociado a un alumno y una cohorte.
 * - Ver el detalle de un certificado con todas sus relaciones.
 * - Editar los datos de un certificado existente.
 * - Eliminar un certificado (soft delete, se envia a la papelera).
 *
 * Adicionalmente, este controlador incluye la generacion de PDF:
 * - generarPdf(): Muestra el PDF en el navegador (stream).
 * - descargarPdf(): Descarga el archivo PDF directamente.
 *
 * Usa el trait HasTrash para las operaciones de papelera
 * (listar eliminados, restaurar y eliminar permanentemente).
 *
 * @see CertificadoPdfService  Servicio que maneja la logica de generacion de PDF.
 * @see Certificado             Modelo Eloquent de certificados.
 */
class CertificadoController extends Controller
{
    use HasTrash;

    /**
     * Muestra la lista paginada de todos los certificados.
     *
     * Carga las relaciones alumno, cohorte y estadoRelacion mediante eager loading
     * para evitar el problema N+1 en la vista index.
     * Pagina los resultados de 15 en 15.
     *
     * @return \Illuminate\View\View  Vista certificados.index con la coleccion paginada.
     */
    public function index()
    {
        $certificados = Certificado::with('alumno', 'cohorte', 'estadoRelacion')->paginate(15);
        return view('certificados.index', compact('certificados'));
    }

    /**
     * Muestra el formulario para crear un nuevo certificado.
     *
     * Carga todos los datos necesarios para los selectores del formulario:
     * - Alumnos: Todos los alumnos registrados.
     * - Cohortes: Todas las cohortes con su relacion curso (para mostrar el nombre del curso).
     * - Autoridades: Solo las autoridades activas, usadas como opciones para firmas.
     * - Estados: Solo los estados cuya entidad es 'certificado'.
     * - Plantillas: Solo las plantillas PDF activas, para asignar una plantilla individual.
     *
     * @return \Illuminate\View\View  Vista certificados.create con los datos para el formulario.
     */
    public function create()
    {
        $alumnos = Alumno::all();
        $cohortes = Cohorte::with('curso')->get();
        $autoridades = Autoridad::where('activo', true)->get();
        $estados = Estatus::where('entidad', 'certificado')->get();
        $plantillas = \App\Models\PlantillaPdf::where('activa', true)->get();
        return view('certificados.create', compact('alumnos', 'cohortes', 'autoridades', 'estados', 'plantillas'));
    }

    /**
     * Almacena un nuevo certificado en la base de datos.
     *
     * Valida los campos requeridos (alumno_id, cohorte_id, libro, folio, fecha_emision)
     * y los opcionales (codigo_registro_manual, estado_id, firmas, plantilla_pdf_id).
     *
     * Si no se proporciona un estado_id, se asigna automaticamente el estado por defecto
     * definido en la tabla configuraciones con la clave 'estado_default_certificado'.
     * Si no se proporciona un valor para el campo 'estado' (texto), se usa el valor
     * de la configuracion 'certificado_estado_valido' (por defecto 'valido').
     *
     * El modelo Certificado genera automaticamente el codigo_verificacion_app
     * y el uuid_seguridad en el evento 'creating' de Eloquent (ver Certificado::boot).
     *
     * @param  Request  $request  La peticion HTTP con los datos del formulario.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'cohorte_id' => 'required|exists:cohortes,id',
            'libro' => 'required|string|max:255',
            'folio' => 'required|string|max:255',
            'codigo_registro_manual' => 'nullable|string|max:255',
            'fecha_emision' => 'required|date',
            'estado_id' => 'nullable|exists:estatus,id',
            'estado' => 'nullable|string|max:255',
            'firma_1_id' => 'nullable|exists:autoridades,id',
            'firma_2_id' => 'nullable|exists:autoridades,id',
            'firma_3_id' => 'nullable|exists:autoridades,id',
            'plantilla_pdf_id' => 'nullable|exists:plantillas_pdf,id',
        ]);

        // Asignar estado por defecto si no se proporciona
        if (empty($validated['estado_id'])) {
            $validated['estado_id'] = \App\Models\Configuracion::obtener('estado_default_certificado', 1);
        }
        
        if (empty($validated['estado'])) {
            $validated['estado'] = \App\Models\Configuracion::obtener('certificado_estado_valido', 'valido');
        }

        Certificado::create($validated);

        return redirect()->route('certificados.index')->with('success', 'Certificado creado exitosamente.');
    }

    /**
     * Muestra el detalle completo de un certificado.
     *
     * Carga todas las relaciones necesarias para mostrar la informacion completa:
     * alumno, cohorte con su curso, estado, y las tres firmas.
     *
     * La vista incluye botones para ver el PDF, descargarlo, editarlo y volver al index.
     *
     * @param  Certificado  $certificado  Instancia inyectada por route model binding.
     * @return \Illuminate\View\View  Vista certificados.show con el certificado cargado.
     */
    public function show(Certificado $certificado)
    {
        $certificado->load('alumno', 'cohorte.curso', 'estadoRelacion', 'firma1', 'firma2', 'firma3');
        return view('certificados.show', compact('certificado'));
    }

    /**
     * Muestra el formulario para editar un certificado existente.
     *
     * Carga las mismas listas de datos que el metodo create() para poblar los selectores,
     * ademas del certificado actual con sus valores precargados en el formulario.
     *
     * @param  Certificado  $certificado  Instancia inyectada por route model binding.
     * @return \Illuminate\View\View  Vista certificados.edit con el certificado y datos del formulario.
     */
    public function edit(Certificado $certificado)
    {
        $alumnos = Alumno::all();
        $cohortes = Cohorte::with('curso')->get();
        $autoridades = Autoridad::where('activo', true)->get();
        $estados = Estatus::where('entidad', 'certificado')->get();
        $plantillas = \App\Models\PlantillaPdf::where('activa', true)->get();
        return view('certificados.edit', compact('certificado', 'alumnos', 'cohortes', 'autoridades', 'estados', 'plantillas'));
    }

    /**
     * Actualiza un certificado existente en la base de datos.
     *
     * Valida los mismos campos que el metodo store() y actualiza el registro.
     * Los campos nullable (firmas, plantilla_pdf_id, etc.) se pueden dejar vacios
     * para limpiar el valor anterior.
     *
     * @param  Request      $request      La peticion HTTP con los datos actualizados.
     * @param  Certificado  $certificado  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function update(Request $request, Certificado $certificado)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'cohorte_id' => 'required|exists:cohortes,id',
            'libro' => 'required|string|max:255',
            'folio' => 'required|string|max:255',
            'codigo_registro_manual' => 'nullable|string|max:255',
            'fecha_emision' => 'required|date',
            'estado_id' => 'nullable|exists:estatus,id',
            'estado' => 'nullable|string|max:255',
            'firma_1_id' => 'nullable|exists:autoridades,id',
            'firma_2_id' => 'nullable|exists:autoridades,id',
            'firma_3_id' => 'nullable|exists:autoridades,id',
            'plantilla_pdf_id' => 'nullable|exists:plantillas_pdf,id',
        ]);

        $certificado->update($validated);

        return redirect()->route('certificados.index')->with('success', 'Certificado actualizado exitosamente.');
    }

    /**
     * Elimina un certificado (soft delete).
     *
     * El certificado no se borra permanentemente de la base de datos, sino que se marca
     * con una fecha en la columna deleted_at (eliminacion suave). Se puede restaurar
     * desde la papelera usando el metodo restore() del trait HasTrash.
     *
     * @param  Certificado  $certificado  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function destroy(Certificado $certificado)
    {
        $certificado->delete();
        return redirect()->route('certificados.index')->with('success', 'Certificado eliminado exitosamente.');
    }

    /**
     * Genera y muestra el PDF del certificado directamente en el navegador.
     *
     * Instancia el servicio CertificadoPdfService, genera el PDF y lo devuelve
     * como stream (se abre en una nueva pestana del navegador).
     *
     * Si no hay plantilla asignada (ni al certificado, ni a la cohorte, ni predeterminada),
     * redirige hacia atras con un mensaje de error.
     *
     * El nombre del archivo se genera usando el codigo de verificacion del certificado.
     *
     * @param  Certificado  $certificado  Instancia inyectada por route model binding.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function generarPdf(Certificado $certificado)
    {
        $service = new CertificadoPdfService();
        $pdf = $service->generarPdf($certificado);

        if (!$pdf) {
            return back()->withErrors(['error' => 'No se encontro una plantilla PDF asignada. Asigna una plantilla a la cohorte o al certificado.']);
        }

        $nombre = 'certificado_' . ($certificado->codigo_verificacion_app ?? $certificado->id) . '.pdf';
        return $pdf->stream($nombre);
    }

    /**
     * Genera y descarga el PDF del certificado como archivo.
     *
     * Funciona igual que generarPdf() pero en lugar de mostrar el PDF en el navegador,
     * fuerza la descarga del archivo con el nombre 'certificado_CODIGO.pdf'.
     *
     * @param  Certificado  $certificado  Instancia inyectada por route model binding.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function descargarPdf(Certificado $certificado)
    {
        $service = new CertificadoPdfService();
        $pdf = $service->generarPdf($certificado);

        if (!$pdf) {
            return back()->withErrors(['error' => 'No se encontro una plantilla PDF asignada.']);
        }

        $nombre = 'certificado_' . ($certificado->codigo_verificacion_app ?? $certificado->id) . '.pdf';
        return $pdf->download($nombre);
    }

    /**
     * Retorna la clase del modelo asociado a este controlador.
     * Requerido por el trait HasTrash para las operaciones de papelera.
     *
     * @return string  Nombre completo de la clase Certificado.
     */
    protected function getModelClass(): string
    {
        return Certificado::class;
    }

    /**
     * Retorna el nombre base de las vistas de este modulo.
     * Requerido por el trait HasTrash para renderizar la vista de papelera.
     *
     * @return string  Prefijo de las vistas (directorio resources/views/certificados/).
     */
    protected function getViewName(): string
    {
        return 'certificados';
    }

    /**
     * Retorna el nombre base de las rutas de este modulo.
     * Requerido por el trait HasTrash para generar las URLs de restaurar/eliminar.
     *
     * @return string  Prefijo de las rutas (ej: certificados.papelera, certificados.restore).
     */
    protected function getRouteName(): string
    {
        return 'certificados';
    }
}
