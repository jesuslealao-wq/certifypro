<?php

namespace App\Http\Controllers;

use App\Models\Autoridad;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

class AutoridadController extends Controller
{
    use HasTrash;
    public function index()
    {
        $autoridades = Autoridad::paginate(15);
        return view('autoridades.index', compact('autoridades'));
    }

    public function create()
    {
        return view('autoridades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'especialidad' => 'nullable|string|max:255',
            'sello' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'firma' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'activo' => 'nullable|boolean',
        ]);

        // Manejar subida de sello
        if ($request->hasFile('sello')) {
            $validated['sello_path'] = $request->file('sello')->store('autoridades/sellos', 'public');
        }

        // Manejar subida de firma
        if ($request->hasFile('firma')) {
            $validated['firma_path'] = $request->file('firma')->store('autoridades/firmas', 'public');
        }

        Autoridad::create($validated);

        return redirect()->route('autoridades.index')->with('success', 'Autoridad creada exitosamente.');
    }

    public function show(Autoridad $autoridad)
    {
        return view('autoridades.show', compact('autoridad'));
    }

    public function edit(Autoridad $autoridad)
    {
        return view('autoridades.edit', compact('autoridad'));
    }

    public function update(Request $request, Autoridad $autoridad)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'especialidad' => 'nullable|string|max:255',
            'sello' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'firma' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'activo' => 'nullable|boolean',
        ]);

        // Manejar subida de sello
        if ($request->hasFile('sello')) {
            // Eliminar sello anterior si existe
            if ($autoridad->sello_path && \Storage::disk('public')->exists($autoridad->sello_path)) {
                \Storage::disk('public')->delete($autoridad->sello_path);
            }
            $validated['sello_path'] = $request->file('sello')->store('autoridades/sellos', 'public');
        }

        // Manejar subida de firma
        if ($request->hasFile('firma')) {
            // Eliminar firma anterior si existe
            if ($autoridad->firma_path && \Storage::disk('public')->exists($autoridad->firma_path)) {
                \Storage::disk('public')->delete($autoridad->firma_path);
            }
            $validated['firma_path'] = $request->file('firma')->store('autoridades/firmas', 'public');
        }

        $autoridad->update($validated);

        return redirect()->route('autoridades.index')->with('success', 'Autoridad actualizada exitosamente.');
    }

    public function destroy(Autoridad $autoridad)
    {
        $autoridad->delete();
        return redirect()->route('autoridades.index')->with('success', 'Autoridad eliminada exitosamente.');
    }

    protected function getModelClass(): string
    {
        return Autoridad::class;
    }

    protected function getViewName(): string
    {
        return 'autoridades';
    }

    protected function getRouteName(): string
    {
        return 'autoridades';
    }
}
