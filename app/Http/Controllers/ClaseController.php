<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Models\Modulo;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

class ClaseController extends Controller
{
    use HasTrash;
    public function index()
    {
        $clases = Clase::with('modulo')->paginate(15);
        return view('clases.index', compact('clases'));
    }

    public function create()
    {
        $modulos = Modulo::with('curso')->get();
        return view('clases.create', compact('modulos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'titulo_clase' => 'required|string|max:255',
            'orden' => 'nullable|integer|min:1',
        ]);

        Clase::create($validated);

        return redirect()->route('clases.index')->with('success', 'Clase creada exitosamente.');
    }

    public function show(Clase $clase)
    {
        $clase->load('modulo.curso');
        return view('clases.show', compact('clase'));
    }

    public function edit(Clase $clase)
    {
        $modulos = Modulo::with('curso')->get();
        return view('clases.edit', compact('clase', 'modulos'));
    }

    public function update(Request $request, Clase $clase)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'titulo_clase' => 'required|string|max:255',
            'orden' => 'nullable|integer|min:1',
        ]);

        $clase->update($validated);

        return redirect()->route('clases.index')->with('success', 'Clase actualizada exitosamente.');
    }

    public function destroy(Clase $clase)
    {
        $clase->delete();
        return redirect()->route('clases.index')->with('success', 'Clase eliminada exitosamente.');
    }

    // API Methods for AJAX
    public function storeApi(Request $request, $moduloId)
    {
        $validated = $request->validate([
            'titulo_clase' => 'required|string|max:255',
            'orden' => 'nullable|integer|min:1',
        ]);

        $validated['modulo_id'] = $moduloId;

        $clase = Clase::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Clase creada exitosamente',
            'clase' => $clase
        ], 201);
    }

    public function destroyApi(Clase $clase)
    {
        $clase->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Clase eliminada exitosamente'
        ]);
    }

    protected function getModelClass(): string
    {
        return Clase::class;
    }

    protected function getViewName(): string
    {
        return 'clases';
    }

    protected function getRouteName(): string
    {
        return 'clases';
    }
}
