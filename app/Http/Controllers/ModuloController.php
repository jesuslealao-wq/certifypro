<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Curso;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    use HasTrash;
    public function index()
    {
        $modulos = Modulo::with('curso')->paginate(15);
        return view('modulos.index', compact('modulos'));
    }

    public function create()
    {
        $cursos = Curso::all();
        return view('modulos.create', compact('cursos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'titulo_modulo' => 'required|string|max:255',
            'horas_modulo' => 'nullable|integer|min:0',
            'orden' => 'nullable|integer|min:1',
        ]);

        Modulo::create($validated);

        return redirect()->route('modulos.index')->with('success', 'Módulo creado exitosamente.');
    }

    public function show(Modulo $modulo)
    {
        $modulo->load('curso', 'clases');
        return view('modulos.show', compact('modulo'));
    }

    public function edit(Modulo $modulo)
    {
        $cursos = Curso::all();
        return view('modulos.edit', compact('modulo', 'cursos'));
    }

    public function update(Request $request, Modulo $modulo)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'titulo_modulo' => 'required|string|max:255',
            'horas_modulo' => 'nullable|integer|min:0',
            'orden' => 'nullable|integer|min:1',
        ]);

        $modulo->update($validated);

        return redirect()->route('modulos.index')->with('success', 'Módulo actualizado exitosamente.');
    }

    public function destroy(Modulo $modulo)
    {
        $modulo->delete();
        return redirect()->route('modulos.index')->with('success', 'Módulo eliminado exitosamente.');
    }

    // API Methods for AJAX
    public function storeApi(Request $request, $cursoId)
    {
        $validated = $request->validate([
            'titulo_modulo' => 'required|string|max:255',
            'horas_modulo' => 'nullable|integer|min:0',
            'orden' => 'nullable|integer|min:1',
        ]);

        $validated['curso_id'] = $cursoId;

        $modulo = Modulo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Módulo creado exitosamente',
            'modulo' => $modulo
        ], 201);
    }

    public function destroyApi(Modulo $modulo)
    {
        $modulo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Módulo eliminado exitosamente'
        ]);
    }

    protected function getModelClass(): string
    {
        return Modulo::class;
    }

    protected function getViewName(): string
    {
        return 'modulos';
    }

    protected function getRouteName(): string
    {
        return 'modulos';
    }
}
