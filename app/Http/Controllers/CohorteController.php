<?php

namespace App\Http\Controllers;

use App\Models\Cohorte;
use App\Models\Curso;
use App\Models\Autoridad;
use App\Models\Estatus;
use App\Models\PlantillaPdf;
use App\Models\Certificado;
use App\Services\CertificadoPdfService;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

/**
 * Controlador para la gestion de cohortes y sus operaciones asociadas.
 *
 * Una cohorte representa un grupo de alumnos que toman un curso especifico
 * en un periodo determinado. Este controlador maneja:
 *
 * CRUD basico:
 * - index(): Listar cohortes con paginacion.
 * - create()/store(): Crear una nueva cohorte asociada a un curso.
 * - show(): Ver el detalle de la cohorte con alumnos, certificados y configuracion.
 * - edit()/update(): Editar los datos de una cohorte.
 * - destroy(): Eliminar una cohorte (soft delete).
 *
 * Gestion de alumnos:
 * - agregarAlumnos(): Inscribir alumnos en la cohorte (tabla pivote alumno_cohorte).
 * - removerAlumno(): Desinscribir un alumno de la cohorte.
 *
 * Gestion de certificados:
 * - generarCertificadosMasivo(): Crear certificados para multiples alumnos a la vez.
 * - configurarCertificadosMasivo(): Actualizar campos en lote (libro, folio, firmas, etc.).
 * - actualizarCertificadoIndividual(): Editar un certificado especifico dentro de la cohorte.
 * - asignarPlantilla(): Asignar una plantilla PDF a la cohorte.
 *
 * Generacion de PDF:
 * - generarPdfsMasivo(): Ver todos los certificados de la cohorte en un solo PDF.
 * - descargarPdfsMasivo(): Descargar el PDF combinado de todos los certificados.
 *
 * @see CertificadoPdfService  Servicio de generacion de PDF.
 * @see Cohorte                Modelo Eloquent de cohortes.
 */
class CohorteController extends Controller
{
    use HasTrash;

    /**
     * Muestra la lista paginada de todas las cohortes.
     *
     * Carga las relaciones curso, instructor y estado mediante eager loading
     * para mostrar la informacion completa en la tabla del index.
     *
     * @return \Illuminate\View\View  Vista cohortes.index con la coleccion paginada.
     */
    public function index()
    {
        $cohortes = Cohorte::with('curso', 'instructor', 'estado')->paginate(15);
        return view('cohortes.index', compact('cohortes'));
    }

    /**
     * Muestra el formulario para crear una nueva cohorte.
     *
     * Carga las listas necesarias para los selectores:
     * - Cursos: Todos los cursos disponibles.
     * - Autoridades: Solo las activas, para asignar instructor y firmas por defecto.
     * - Estados: Solo los estados cuya entidad es 'cohorte'.
     *
     * @return \Illuminate\View\View  Vista cohortes.create con los datos del formulario.
     */
    public function create()
    {
        $cursos = Curso::all();
        $autoridades = Autoridad::where('activo', true)->get();
        $estados = Estatus::where('entidad', 'cohorte')->get();
        return view('cohortes.create', compact('cursos', 'autoridades', 'estados'));
    }

    /**
     * Almacena una nueva cohorte en la base de datos.
     *
     * Valida el curso (requerido) y los campos opcionales como instructor, fechas,
     * codigo de cohorte, estado, modalidad y firmas por defecto.
     *
     * Si no se proporciona un estado_id, se asigna automaticamente el valor
     * definido en la tabla configuraciones con la clave 'estado_default_cohorte'.
     *
     * @param  Request  $request  La peticion HTTP con los datos del formulario.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'instructor_id' => 'nullable|exists:autoridades,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'codigo_cohorte' => 'nullable|string|max:255',
            'estado_id' => 'nullable|exists:estatus,id',
            'modalidad' => 'nullable|string|max:255',
            'firma_default_1_id' => 'nullable|exists:autoridades,id',
            'firma_default_2_id' => 'nullable|exists:autoridades,id',
            'firma_default_3_id' => 'nullable|exists:autoridades,id',
        ]);

        // Asignar estado por defecto si no se proporciona
        if (empty($validated['estado_id'])) {
            $validated['estado_id'] = \App\Models\Configuracion::obtener('estado_default_cohorte', 1);
        }

        Cohorte::create($validated);

        return redirect()->route('cohortes.index')->with('success', 'Cohorte creada exitosamente.');
    }

    /**
     * Muestra la vista detallada de una cohorte.
     *
     * Esta es la vista mas compleja del sistema. Carga y muestra:
     * - Informacion general de la cohorte (curso, instructor, fechas, modalidad).
     * - Lista de alumnos inscritos con opcion de agregar y remover.
     * - Panel de asignacion de plantilla PDF.
     * - Panel de configuracion masiva de certificados.
     * - Tabla de certificados generados con acciones individuales y PDF.
     * - Botones para generar/descargar PDF masivo de todos los certificados.
     *
     * Tambien carga listas auxiliares necesarias para los modales:
     * - alumnosNoInscritos: Alumnos que aun no estan en esta cohorte.
     * - plantillas: Plantillas PDF activas para asignar.
     * - autoridades: Autoridades activas para asignar como firmantes.
     * - estados: Estados disponibles para certificados.
     *
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Illuminate\View\View  Vista cohortes.show con todos los datos necesarios.
     */
    public function show(Cohorte $cohorte)
    {
        $cohorte->load('alumnos', 'certificados.alumno', 'certificados.estadoRelacion', 'certificados.firma1', 'certificados.firma2', 'certificados.firma3', 'plantillaPdf');
        
        $alumnosConCertificado = $cohorte->certificados()->pluck('alumno_id')->unique();
        
        $alumnosDisponibles = $cohorte->alumnos->count() > 0 
            ? $cohorte->alumnos 
            : \App\Models\Alumno::all();
        
        $alumnosNoInscritos = \App\Models\Alumno::whereNotIn('id', $cohorte->alumnos->pluck('id'))->get();
        
        $plantillas = PlantillaPdf::where('activa', true)->get();
        $autoridades = Autoridad::where('activo', true)->get();
        $estados = Estatus::where('entidad', 'certificado')->get();
        
        return view('cohortes.show', compact(
            'cohorte', 'alumnosDisponibles', 'alumnosConCertificado', 'alumnosNoInscritos',
            'plantillas', 'autoridades', 'estados'
        ));
    }

    /**
     * Agregar alumnos a la cohorte
     */
    public function agregarAlumnos(Request $request, Cohorte $cohorte)
    {
        $validated = $request->validate([
            'alumnos' => 'required|array|min:1',
            'alumnos.*' => 'exists:alumnos,id',
        ]);

        foreach ($validated['alumnos'] as $alumnoId) {
            // Evitar duplicados
            if (!$cohorte->alumnos()->where('alumno_id', $alumnoId)->exists()) {
                $cohorte->alumnos()->attach($alumnoId, [
                    'fecha_inscripcion' => now()
                ]);
            }
        }

        return back()->with('success', 'Alumno(s) agregado(s) a la cohorte exitosamente.');
    }

    /**
     * Remover alumno de la cohorte
     */
    public function removerAlumno(Cohorte $cohorte, $alumnoId)
    {
        $cohorte->alumnos()->detach($alumnoId);
        return back()->with('success', 'Alumno removido de la cohorte exitosamente.');
    }

    /**
     * Muestra el formulario para editar una cohorte existente.
     *
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Illuminate\View\View  Vista cohortes.edit con la cohorte y datos del formulario.
     */
    public function edit(Cohorte $cohorte)
    {
        $cursos = Curso::all();
        $autoridades = Autoridad::where('activo', true)->get();
        $estados = Estatus::where('entidad', 'cohorte')->get();
        return view('cohortes.edit', compact('cohorte', 'cursos', 'autoridades', 'estados'));
    }

    /**
     * Actualiza una cohorte existente en la base de datos.
     *
     * Valida los mismos campos que store() y actualiza el registro.
     *
     * @param  Request  $request  La peticion HTTP con los datos actualizados.
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function update(Request $request, Cohorte $cohorte)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'instructor_id' => 'nullable|exists:autoridades,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'codigo_cohorte' => 'nullable|string|max:255',
            'estado_id' => 'nullable|exists:estatus,id',
            'modalidad' => 'nullable|string|max:255',
            'firma_default_1_id' => 'nullable|exists:autoridades,id',
            'firma_default_2_id' => 'nullable|exists:autoridades,id',
            'firma_default_3_id' => 'nullable|exists:autoridades,id',
        ]);

        $cohorte->update($validated);

        return redirect()->route('cohortes.index')->with('success', 'Cohorte actualizada exitosamente.');
    }

    /**
     * Elimina una cohorte (soft delete).
     *
     * La cohorte se marca como eliminada pero permanece en la base de datos.
     * Se puede restaurar desde la papelera.
     *
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function destroy(Cohorte $cohorte)
    {
        $cohorte->delete();
        return redirect()->route('cohortes.index')->with('success', 'Cohorte eliminada exitosamente.');
    }

    /**
     * Retorna la clase del modelo asociado. Requerido por el trait HasTrash.
     *
     * @return string  Nombre completo de la clase Cohorte.
     */
    protected function getModelClass(): string
    {
        return Cohorte::class;
    }

    /**
     * Retorna el nombre base de las vistas. Requerido por el trait HasTrash.
     *
     * @return string  Prefijo del directorio de vistas.
     */
    protected function getViewName(): string
    {
        return 'cohortes';
    }

    /**
     * Retorna el nombre base de las rutas. Requerido por el trait HasTrash.
     *
     * @return string  Prefijo de las rutas.
     */
    protected function getRouteName(): string
    {
        return 'cohortes';
    }

    /**
     * Asigna una plantilla PDF a la cohorte.
     *
     * Todos los certificados de esta cohorte que no tengan una plantilla individual
     * asignada usaran esta plantilla al momento de generar el PDF.
     * Si se envia null, se desasigna la plantilla y se usara la predeterminada del sistema.
     *
     * @param  Request  $request  Contiene plantilla_pdf_id (nullable).
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige hacia atras con mensaje de exito.
     */
    public function asignarPlantilla(Request $request, Cohorte $cohorte)
    {
        $validated = $request->validate([
            'plantilla_pdf_id' => 'nullable|exists:plantillas_pdf,id',
        ]);

        $cohorte->update($validated);

        return back()->with('success', 'Plantilla asignada a la cohorte.');
    }

    /**
     * Configura en lote los certificados de una cohorte.
     *
     * Permite actualizar multiples campos de todos los certificados de la cohorte
     * en una sola operacion. Los campos a actualizar se seleccionan mediante
     * el array 'campos' del formulario (checkboxes en la vista).
     *
     * Campos soportados:
     * - libro: Se aplica el mismo valor a todos los certificados.
     * - folio: Se auto-incrementa a partir de folio_inicio, formateado con 6 digitos (000001, 000002, etc.).
     * - codigo_registro_manual: Se genera con un prefijo + guion + folio de 4 digitos.
     * - fecha_emision: Se aplica la misma fecha a todos.
     * - estado_id: Se aplica el mismo estado a todos.
     * - firma_1_id, firma_2_id, firma_3_id: Se aplican las mismas firmas a todos.
     *
     * Los certificados se procesan en orden de ID (el mas antiguo primero).
     *
     * @param  Request  $request  Contiene los valores y el array de campos seleccionados.
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige hacia atras con mensaje de exito o error.
     */
    public function configurarCertificadosMasivo(Request $request, Cohorte $cohorte)
    {
        $validated = $request->validate([
            'libro' => 'nullable|string|max:255',
            'folio_inicio' => 'nullable|integer|min:1',
            'codigo_registro_prefijo' => 'nullable|string|max:255',
            'fecha_emision' => 'nullable|date',
            'estado_id' => 'nullable|exists:estatus,id',
            'firma_1_id' => 'nullable|exists:autoridades,id',
            'firma_2_id' => 'nullable|exists:autoridades,id',
            'firma_3_id' => 'nullable|exists:autoridades,id',
            'campos' => 'required|array|min:1',
        ]);

        $certificados = $cohorte->certificados()->orderBy('id')->get();
        
        if ($certificados->isEmpty()) {
            return back()->withErrors(['error' => 'No hay certificados en esta cohorte.']);
        }

        $campos = $validated['campos'];
        $folioActual = (int) ($validated['folio_inicio'] ?? 1);
        $actualizados = 0;

        foreach ($certificados as $cert) {
            $data = [];

            if (in_array('libro', $campos) && !empty($validated['libro'])) {
                $data['libro'] = $validated['libro'];
            }
            if (in_array('folio', $campos)) {
                $data['folio'] = str_pad($folioActual, 6, '0', STR_PAD_LEFT);
                $folioActual++;
            }
            if (in_array('codigo_registro_manual', $campos) && !empty($validated['codigo_registro_prefijo'])) {
                $data['codigo_registro_manual'] = $validated['codigo_registro_prefijo'] . '-' . str_pad($cert->folio ?? $actualizados + 1, 4, '0', STR_PAD_LEFT);
            }
            if (in_array('fecha_emision', $campos) && !empty($validated['fecha_emision'])) {
                $data['fecha_emision'] = $validated['fecha_emision'];
            }
            if (in_array('estado_id', $campos) && !empty($validated['estado_id'])) {
                $data['estado_id'] = $validated['estado_id'];
            }
            if (in_array('firma_1_id', $campos)) {
                $data['firma_1_id'] = $validated['firma_1_id'] ?? null;
            }
            if (in_array('firma_2_id', $campos)) {
                $data['firma_2_id'] = $validated['firma_2_id'] ?? null;
            }
            if (in_array('firma_3_id', $campos)) {
                $data['firma_3_id'] = $validated['firma_3_id'] ?? null;
            }

            if (!empty($data)) {
                $cert->update($data);
                $actualizados++;
            }
        }

        return back()->with('success', "Se actualizaron {$actualizados} certificado(s) masivamente.");
    }

    /**
     * Actualiza un certificado individual desde la vista de la cohorte.
     *
     * Este metodo se usa desde el modal de edicion individual en la vista cohortes.show.
     * Permite editar todos los campos del certificado sin salir de la vista de la cohorte.
     *
     * @param  Request      $request      Contiene los campos a actualizar.
     * @param  Cohorte      $cohorte      La cohorte padre (para mantener el contexto de la URL).
     * @param  Certificado  $certificado  El certificado a actualizar.
     * @return \Illuminate\Http\RedirectResponse  Redirige hacia atras con mensaje de exito.
     */
    public function actualizarCertificadoIndividual(Request $request, Cohorte $cohorte, Certificado $certificado)
    {
        $validated = $request->validate([
            'libro' => 'nullable|string|max:255',
            'folio' => 'nullable|string|max:255',
            'codigo_registro_manual' => 'nullable|string|max:255',
            'fecha_emision' => 'nullable|date',
            'estado_id' => 'nullable|exists:estatus,id',
            'firma_1_id' => 'nullable|exists:autoridades,id',
            'firma_2_id' => 'nullable|exists:autoridades,id',
            'firma_3_id' => 'nullable|exists:autoridades,id',
            'plantilla_pdf_id' => 'nullable|exists:plantillas_pdf,id',
        ]);

        $certificado->update($validated);

        return back()->with('success', 'Certificado actualizado individualmente.');
    }

    /**
     * Genera y muestra un PDF combinado con todos los certificados de la cohorte.
     *
     * Carga todos los certificados de la cohorte con sus relaciones necesarias,
     * luego usa CertificadoPdfService::generarPdfMultiple() para crear un unico PDF
     * donde cada certificado ocupa sus propias paginas separadas por page-breaks.
     *
     * El PDF se muestra directamente en el navegador (stream) para su impresion.
     *
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function generarPdfsMasivo(Cohorte $cohorte)
    {
        $certificados = $cohorte->certificados()->with('alumno', 'cohorte.curso', 'firma1', 'firma2', 'firma3')->get();

        if ($certificados->isEmpty()) {
            return back()->withErrors(['error' => 'No hay certificados en esta cohorte.']);
        }

        $service = new CertificadoPdfService();
        $pdf = $service->generarPdfMultiple($certificados->all());

        if (!$pdf) {
            return back()->withErrors(['error' => 'No se encontro una plantilla PDF asignada. Asigna una plantilla a la cohorte.']);
        }

        $nombre = 'certificados_' . ($cohorte->codigo_cohorte ?? $cohorte->id) . '.pdf';
        return $pdf->stream($nombre);
    }

    /**
     * Genera y descarga un PDF combinado con todos los certificados de la cohorte.
     *
     * Funciona igual que generarPdfsMasivo() pero fuerza la descarga del archivo
     * con el nombre 'certificados_CODIGO_COHORTE.pdf'.
     *
     * @param  Cohorte  $cohorte  Instancia inyectada por route model binding.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function descargarPdfsMasivo(Cohorte $cohorte)
    {
        $certificados = $cohorte->certificados()->with('alumno', 'cohorte.curso', 'firma1', 'firma2', 'firma3')->get();

        if ($certificados->isEmpty()) {
            return back()->withErrors(['error' => 'No hay certificados en esta cohorte.']);
        }

        $service = new CertificadoPdfService();
        $pdf = $service->generarPdfMultiple($certificados->all());

        if (!$pdf) {
            return back()->withErrors(['error' => 'No se encontro una plantilla PDF asignada.']);
        }

        $nombre = 'certificados_' . ($cohorte->codigo_cohorte ?? $cohorte->id) . '.pdf';
        return $pdf->download($nombre);
    }

    /**
     * Generar certificados masivamente para todos los alumnos de una cohorte
     */
    public function generarCertificadosMasivo(Request $request, Cohorte $cohorte)
    {
        $validated = $request->validate([
            'alumnos' => 'required|array|min:1',
            'alumnos.*.alumno_id' => 'required|exists:alumnos,id',
            'alumnos.*.calificacion_final' => 'required|numeric|min:0|max:100',
        ]);

        $estadoDefaultId = \App\Models\Configuracion::obtener('estado_default_certificado', 1);
        $estadoValido = \App\Models\Configuracion::obtener('certificado_estado_valido', 'valido');

        $certificadosGenerados = 0;
        $errores = [];

        foreach ($validated['alumnos'] as $alumnoData) {
            try {
                // Verificar si ya existe certificado para este alumno en esta cohorte
                $existente = \App\Models\Certificado::where('alumno_id', $alumnoData['alumno_id'])
                    ->where('cohorte_id', $cohorte->id)
                    ->first();

                if ($existente) {
                    $alumno = \App\Models\Alumno::find($alumnoData['alumno_id']);
                    $errores[] = "El alumno {$alumno->nombre_completo} ya tiene un certificado generado.";
                    continue;
                }

                // Generar libro y folio automáticamente
                $year = now()->year;
                $lastCertificado = \App\Models\Certificado::whereYear('fecha_emision', $year)
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();
                
                $folio = $lastCertificado ? ((int)$lastCertificado->folio + 1) : 1;
                $libro = $year;

                // Crear certificado
                \App\Models\Certificado::create([
                    'alumno_id' => $alumnoData['alumno_id'],
                    'cohorte_id' => $cohorte->id,
                    'libro' => $libro,
                    'folio' => str_pad($folio, 6, '0', STR_PAD_LEFT),
                    'codigo_verificacion_app' => 'CERT-' . strtoupper(uniqid()),
                    'fecha_emision' => now(),
                    'estatus_id' => $estadoDefaultId,
                    'calificacion_final' => $alumnoData['calificacion_final'],
                    'temario_snapshot' => json_encode($cohorte->curso->modulos()->with('clases')->get()),
                ]);

                $certificadosGenerados++;

            } catch (\Exception $e) {
                $alumno = \App\Models\Alumno::find($alumnoData['alumno_id']);
                $errores[] = "Error al generar certificado para {$alumno->nombre_completo}: " . $e->getMessage();
            }
        }

        $mensaje = "Se generaron {$certificadosGenerados} certificado(s) exitosamente.";
        
        if (count($errores) > 0) {
            $mensaje .= " Errores: " . implode(', ', $errores);
            return back()->with('warning', $mensaje);
        }

        return back()->with('success', $mensaje);
    }
}
