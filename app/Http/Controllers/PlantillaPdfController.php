<?php

namespace App\Http\Controllers;

use App\Models\PlantillaPdf;
use Illuminate\Http\Request;

/**
 * Controlador para la gestion de plantillas PDF de certificados.
 *
 * Las plantillas PDF definen el diseno visual de los certificados generados.
 * Cada plantilla contiene HTML y CSS editable con variables dinamicas
 * que se sustituyen por los datos reales del certificado al momento de la impresion.
 *
 * Funcionalidades principales:
 * - CRUD completo de plantillas (sin vista show, se usa el editor en su lugar).
 * - Editor visual con CodeMirror para editar HTML y CSS en tiempo real.
 * - Vista previa con datos de prueba para verificar el diseno.
 * - Duplicar plantillas existentes para crear variaciones.
 * - Subir y eliminar imagenes de fondo por pagina (pagina 1 y pagina 2).
 * - Gestion de plantilla predeterminada (solo una a la vez).
 *
 * Las imagenes de fondo se almacenan en storage/app/public/plantillas-pdf/fondos/
 * y se sirven a traves del enlace simbolico public/storage/.
 *
 * @see PlantillaPdf  Modelo Eloquent de plantillas PDF.
 */
class PlantillaPdfController extends Controller
{
    /**
     * Muestra la lista paginada de todas las plantillas PDF.
     *
     * Las plantillas se ordenan por fecha de creacion descendente (las mas recientes primero)
     * y se paginan de 12 en 12 para mostrarlas en un grid de tarjetas.
     *
     * @return \Illuminate\View\View  Vista plantillas-pdf.index con la coleccion paginada.
     */
    public function index()
    {
        $plantillas = PlantillaPdf::latest()->paginate(12);
        return view('plantillas-pdf.index', compact('plantillas'));
    }

    /**
     * Muestra el formulario para crear una nueva plantilla.
     *
     * Carga la lista de variables disponibles desde el modelo PlantillaPdf
     * para mostrarlas en el formulario como referencia para el usuario.
     *
     * @return \Illuminate\View\View  Vista plantillas-pdf.create con las variables disponibles.
     */
    public function create()
    {
        $variables = PlantillaPdf::variablesDisponibles();
        return view('plantillas-pdf.create', compact('variables'));
    }

    /**
     * Almacena una nueva plantilla PDF en la base de datos.
     *
     * Valida los campos requeridos (nombre, contenido_html, orientacion, tamano_papel)
     * y los opcionales (descripcion, estilos_css).
     *
     * Si la plantilla se marca como predeterminada (es_predeterminada = true),
     * primero se desmarcan todas las demas plantillas predeterminadas para
     * garantizar que solo haya una plantilla predeterminada a la vez.
     *
     * Despues de crear la plantilla, redirige al editor visual para que el
     * usuario pueda comenzar a disenar el contenido HTML/CSS.
     *
     * @param  Request  $request  La peticion HTTP con los datos del formulario.
     * @return \Illuminate\Http\RedirectResponse  Redirige al editor de la plantilla creada.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido_html' => 'required|string',
            'estilos_css' => 'nullable|string',
            'orientacion' => 'required|in:portrait,landscape',
            'tamano_papel' => 'required|in:a4,letter,legal',
        ]);

        $validated['activa'] = $request->boolean('activa', true);
        $validated['es_predeterminada'] = $request->boolean('es_predeterminada', false);

        if ($validated['es_predeterminada']) {
            PlantillaPdf::where('es_predeterminada', true)->update(['es_predeterminada' => false]);
        }

        $plantilla = PlantillaPdf::create($validated);

        return redirect()->route('plantillas-pdf.edit', $plantilla)
            ->with('success', 'Plantilla creada exitosamente.');
    }

    /**
     * Muestra el editor visual de la plantilla.
     *
     * El editor incluye:
     * - Paneles de edicion con CodeMirror para HTML y CSS.
     * - Panel lateral con las variables disponibles (clic para insertar).
     * - Tab de fondos para subir imagenes de fondo por pagina.
     * - Vista previa en vivo que se actualiza al escribir.
     *
     * Tambien carga los datos de prueba que se usan en la vista previa
     * para mostrar valores realistas mientras se edita.
     *
     * Nota: El parametro se llama $plantillas_pdf (con guion bajo) porque Laravel
     * genera el nombre del parametro a partir del nombre de la ruta 'plantillas-pdf',
     * convirtiendo los guiones en guiones bajos.
     *
     * @param  PlantillaPdf  $plantillas_pdf  Instancia inyectada por route model binding.
     * @return \Illuminate\View\View  Vista plantillas-pdf.editor con la plantilla y datos auxiliares.
     */
    public function edit(PlantillaPdf $plantillas_pdf)
    {
        $variables = PlantillaPdf::variablesDisponibles();
        $datosPrueba = PlantillaPdf::datosDePrueba();
        return view('plantillas-pdf.editor', compact('plantillas_pdf', 'variables', 'datosPrueba'));
    }

    /**
     * Actualiza una plantilla PDF existente.
     *
     * Valida los mismos campos que store() y actualiza el registro.
     * Si se marca como predeterminada, desmarca las demas (excluyendo la actual).
     *
     * Este metodo se llama desde el editor visual cuando el usuario guarda los cambios
     * en el HTML, CSS, nombre, orientacion o tamano de papel.
     *
     * @param  Request       $request        La peticion HTTP con los datos actualizados.
     * @param  PlantillaPdf  $plantillas_pdf Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige al editor con mensaje de exito.
     */
    public function update(Request $request, PlantillaPdf $plantillas_pdf)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido_html' => 'required|string',
            'estilos_css' => 'nullable|string',
            'orientacion' => 'required|in:portrait,landscape',
            'tamano_papel' => 'required|in:a4,letter,legal',
        ]);

        $validated['activa'] = $request->boolean('activa', true);
        $validated['es_predeterminada'] = $request->boolean('es_predeterminada', false);

        if ($validated['es_predeterminada']) {
            PlantillaPdf::where('id', '!=', $plantillas_pdf->id)
                ->where('es_predeterminada', true)
                ->update(['es_predeterminada' => false]);
        }

        $plantillas_pdf->update($validated);

        return redirect()->route('plantillas-pdf.edit', $plantillas_pdf)
            ->with('success', 'Plantilla actualizada.');
    }

    /**
     * Elimina una plantilla PDF (soft delete).
     *
     * La plantilla se marca como eliminada pero no se borra permanentemente.
     * Los certificados que la tenian asignada dejaran de tener plantilla individual
     * y usaran la de la cohorte o la predeterminada al generar PDF.
     *
     * @param  PlantillaPdf  $plantillas_pdf  Instancia inyectada por route model binding.
     * @return \Illuminate\Http\RedirectResponse  Redirige al index con mensaje de exito.
     */
    public function destroy(PlantillaPdf $plantillas_pdf)
    {
        $plantillas_pdf->delete();
        return redirect()->route('plantillas-pdf.index')
            ->with('success', 'Plantilla eliminada.');
    }

    /**
     * Muestra una vista previa de la plantilla con datos de prueba.
     *
     * Sustituye todas las variables del HTML con los datos de prueba definidos
     * en PlantillaPdf::datosDePrueba() (por ejemplo, alumno_nombre = 'MARIA VALENTINA...').
     *
     * Tambien pasa las URLs de las imagenes de fondo (si existen) a la vista
     * para que se apliquen via JavaScript a cada pagina del preview.
     *
     * La vista se abre en una nueva pestana del navegador y renderiza el HTML
     * completo como una pagina web, simulando como se veria el PDF final.
     *
     * @param  PlantillaPdf  $plantillas_pdf  Instancia inyectada por route model binding.
     * @return \Illuminate\View\View  Vista plantillas-pdf.preview con el HTML renderizado.
     */
    public function preview(PlantillaPdf $plantillas_pdf)
    {
        $datos = PlantillaPdf::datosDePrueba();
        $html = $plantillas_pdf->contenido_html;

        foreach ($datos as $key => $value) {
            $html = str_replace('{{ $' . $key . ' }}', $value, $html);
            $html = str_replace('{{$' . $key . '}}', $value, $html);
        }

        $css = $plantillas_pdf->estilos_css ?? '';
        $orientacion = $plantillas_pdf->orientacion;
        $tamano = $plantillas_pdf->tamano_papel;
        $fondos = [
            1 => $plantillas_pdf->fondo_pagina_1 ? asset('storage/' . $plantillas_pdf->fondo_pagina_1) : null,
            2 => $plantillas_pdf->fondo_pagina_2 ? asset('storage/' . $plantillas_pdf->fondo_pagina_2) : null,
        ];

        return view('plantillas-pdf.preview', compact('html', 'css', 'orientacion', 'tamano', 'fondos'));
    }

    /**
     * Duplica una plantilla PDF existente.
     *
     * Crea una copia exacta de la plantilla con todos sus campos (HTML, CSS, fondos, etc.)
     * pero con el nombre modificado agregando ' (Copia)' al final.
     * La copia nunca se marca como predeterminada para evitar conflictos.
     *
     * Util para crear variaciones de un diseno existente sin modificar el original.
     *
     * @param  PlantillaPdf  $plantillas_pdf  Plantilla original a duplicar.
     * @return \Illuminate\Http\RedirectResponse  Redirige al editor de la nueva copia.
     */
    public function duplicar(PlantillaPdf $plantillas_pdf)
    {
        $nueva = $plantillas_pdf->replicate();
        $nueva->nombre = $plantillas_pdf->nombre . ' (Copia)';
        $nueva->es_predeterminada = false;
        $nueva->save();

        return redirect()->route('plantillas-pdf.edit', $nueva)
            ->with('success', 'Plantilla duplicada.');
    }

    /**
     * Sube una imagen de fondo para una pagina especifica de la plantilla.
     *
     * Acepta imagenes JPG, JPEG, PNG y WEBP con un tamano maximo de 5 MB.
     * El parametro 'pagina' indica a que pagina corresponde el fondo (1 o 2).
     *
     * Si ya existia un fondo anterior para esa pagina, se elimina el archivo
     * fisico del disco antes de guardar el nuevo.
     *
     * Las imagenes se guardan en storage/app/public/plantillas-pdf/fondos/
     * y se sirven mediante el enlace simbolico public/storage/.
     *
     * Retorna una respuesta JSON con la URL publica de la imagen subida,
     * que el editor visual usa para actualizar la vista previa inmediatamente.
     *
     * @param  Request       $request        Contiene el archivo 'imagen' y el numero de 'pagina'.
     * @param  PlantillaPdf  $plantillas_pdf Plantilla a la que se asigna el fondo.
     * @return \Illuminate\Http\JsonResponse  JSON con success, url y path del archivo.
     */
    public function uploadFondo(Request $request, PlantillaPdf $plantillas_pdf)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'pagina' => 'required|in:1,2',
        ]);

        $pagina = $request->input('pagina');
        $campo = 'fondo_pagina_' . $pagina;

        // Eliminar fondo anterior si existe
        if ($plantillas_pdf->$campo) {
            $oldPath = storage_path('app/public/' . $plantillas_pdf->$campo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $path = $request->file('imagen')->store('plantillas-pdf/fondos', 'public');
        $plantillas_pdf->update([$campo => $path]);

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
            'path' => $path,
        ]);
    }

    /**
     * Elimina la imagen de fondo de una pagina especifica de la plantilla.
     *
     * Borra el archivo fisico del disco y establece el campo correspondiente
     * (fondo_pagina_1 o fondo_pagina_2) a null en la base de datos.
     *
     * @param  Request       $request        Contiene el numero de 'pagina' (1 o 2).
     * @param  PlantillaPdf  $plantillas_pdf Plantilla de la que se elimina el fondo.
     * @return \Illuminate\Http\JsonResponse  JSON con success = true.
     */
    public function removeFondo(Request $request, PlantillaPdf $plantillas_pdf)
    {
        $request->validate([
            'pagina' => 'required|in:1,2',
        ]);

        $pagina = $request->input('pagina');
        $campo = 'fondo_pagina_' . $pagina;

        if ($plantillas_pdf->$campo) {
            $oldPath = storage_path('app/public/' . $plantillas_pdf->$campo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $plantillas_pdf->update([$campo => null]);
        }

        return response()->json(['success' => true]);
    }
}
