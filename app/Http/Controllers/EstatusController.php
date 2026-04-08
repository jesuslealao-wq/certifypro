<?php

namespace App\Http\Controllers;

use App\Models\Estatus;
use App\Traits\HasTrash;
use Illuminate\Http\Request;

class EstatusController extends Controller
{
    use HasTrash;
    public function index()
    {
        $estatus = Estatus::orderBy('orden_visual')->paginate(15);
        return view('estatus.index', compact('estatus'));
    }

    public function create()
    {
        return view('estatus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entidad' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden_visual' => 'nullable|integer',
        ]);

        Estatus::create($validated);

        return redirect()->route('estatus.index')->with('success', 'Estatus creado exitosamente.');
    }

    public function show(Estatus $estatus)
    {
        return view('estatus.show', compact('estatus'));
    }

    public function edit(Estatus $estatus)
    {
        return view('estatus.edit', compact('estatus'));
    }

    public function update(Request $request, Estatus $estatus)
    {
        $validated = $request->validate([
            'entidad' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden_visual' => 'nullable|integer',
        ]);

        $estatus->update($validated);

        return redirect()->route('estatus.index')->with('success', 'Estatus actualizado exitosamente.');
    }

    public function destroy(Estatus $estatus)
    {
        $estatus->delete();
        return redirect()->route('estatus.index')->with('success', 'Estatus eliminado exitosamente.');
    }

    protected function getModelClass(): string
    {
        return Estatus::class;
    }

    protected function getViewName(): string
    {
        return 'estatus';
    }

    protected function getRouteName(): string
    {
        return 'estatus';
    }
}
