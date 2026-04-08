<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    use HasTrash;
    public function index()
    {
        $alumnos = Alumno::paginate(15);
        return view('alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        return view('alumnos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'identificacion_nacional' => 'required|string|max:255|unique:alumnos',
            'nombre_completo' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:255',
        ]);

        Alumno::create($validated);

        return redirect()->route('alumnos.index')->with('success', 'Alumno creado exitosamente.');
    }

    public function show(Alumno $alumno)
    {
        $alumno->load('certificados');
        return view('alumnos.show', compact('alumno'));
    }

    public function edit(Alumno $alumno)
    {
        return view('alumnos.edit', compact('alumno'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $validated = $request->validate([
            'identificacion_nacional' => 'required|string|max:255|unique:alumnos,identificacion_nacional,' . $alumno->id,
            'nombre_completo' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:255',
        ]);

        $alumno->update($validated);

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado exitosamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('alumnos.index')->with('success', 'Alumno eliminado exitosamente.');
    }

    protected function getModelClass(): string
    {
        return Alumno::class;
    }

    protected function getViewName(): string
    {
        return 'alumnos';
    }

    protected function getRouteName(): string
    {
        return 'alumnos';
    }
}
