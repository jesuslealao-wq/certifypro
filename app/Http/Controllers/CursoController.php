<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estatus;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    use HasTrash;
    public function index()
    {
        $cursos = Curso::with('estado')->paginate(15);
        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        $estados = Estatus::where('entidad', 'curso')->get();
        return view('cursos.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_curso' => 'required|string|max:255',
            'horas_academicas' => 'required|integer|min:1',
            'estado_id' => 'nullable|exists:estatus,id',
            'descripcion' => 'nullable|string',
        ]);

        // Asignar estado por defecto si no se proporciona
        if (empty($validated['estado_id'])) {
            $validated['estado_id'] = \App\Models\Configuracion::obtener('estado_default_curso', 1);
        }

        Curso::create($validated);

        return redirect()->route('cursos.index')->with('success', 'Curso creado exitosamente.');
    }

    public function show(Curso $curso)
    {
        $curso->load('modulos.clases', 'estado');
        return view('cursos.show', compact('curso'));
    }

    public function edit(Curso $curso)
    {
        $estados = Estatus::where('entidad', 'curso')->get();
        return view('cursos.edit', compact('curso', 'estados'));
    }

    public function update(Request $request, Curso $curso)
    {
        $validated = $request->validate([
            'nombre_curso' => 'required|string|max:255',
            'horas_academicas' => 'required|integer|min:1',
            'estado_id' => 'nullable|exists:estatus,id',
            'descripcion' => 'nullable|string',
        ]);

        $curso->update($validated);

        return redirect()->route('cursos.index')->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();
        return redirect()->route('cursos.index')->with('success', 'Curso eliminado exitosamente.');
    }

    protected function getModelClass(): string
    {
        return Curso::class;
    }

    protected function getViewName(): string
    {
        return 'cursos';
    }

    protected function getRouteName(): string
    {
        return 'cursos';
    }
}
