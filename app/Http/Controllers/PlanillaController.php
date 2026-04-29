<?php

namespace App\Http\Controllers;

use App\Models\Planilla;
use Illuminate\Http\Request;

class PlanillaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $planillas = Planilla::latest()->paginate(12);
        return view('planillas.index', compact('planillas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fondos = $this->listarFondosDisponibles();
        return view('planillas.create', compact('fondos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fondo_pagina_1' => 'nullable|string|max:255',
            'fondo_pagina_2' => 'nullable|string|max:255',
        ]);

        $validated['activa'] = $request->boolean('activa', true);
        $validated['es_predeterminada'] = $request->boolean('es_predeterminada', false);

        if ($validated['es_predeterminada']) {
            Planilla::where('es_predeterminada', true)->update(['es_predeterminada' => false]);
        }

        $validated['estructura_html'] = $this->leerEstructuraHtmlBase();
        $validated['estilos_css'] = $this->leerCssBase();

        // Normalizar: guardar solo el nombre del archivo si viene como ruta.
        foreach (['fondo_pagina_1', 'fondo_pagina_2'] as $campo) {
            if (!empty($validated[$campo])) {
                $validated[$campo] = basename(str_replace('\\', '/', $validated[$campo]));
            }
        }

        // Validar que el fondo elegido exista dentro de la carpeta permitida (planilla/)
        $fondosDisponibles = collect($this->listarFondosDisponibles())->pluck('file')->all();
        foreach (['fondo_pagina_1', 'fondo_pagina_2'] as $campo) {
            if (!empty($validated[$campo]) && !in_array($validated[$campo], $fondosDisponibles, true)) {
                return back()
                    ->withInput()
                    ->withErrors([$campo => 'El fondo seleccionado no existe en la carpeta de planillas.']);
            }
        }

        $planilla = Planilla::create($validated);

        return redirect()->route('planillas.edit', $planilla)
            ->with('success', 'Planilla creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $planilla = Planilla::findOrFail($id);
        return view('planillas.show', compact('planilla'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $planilla = Planilla::findOrFail($id);
        $fondos = $this->listarFondosDisponibles();
        return view('planillas.edit', compact('planilla', 'fondos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $planilla = Planilla::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fondo_pagina_1' => 'nullable|string|max:255',
            'fondo_pagina_2' => 'nullable|string|max:255',
        ]);

        $validated['activa'] = $request->boolean('activa', true);
        $validated['es_predeterminada'] = $request->boolean('es_predeterminada', false);

        if ($validated['es_predeterminada']) {
            Planilla::where('id', '!=', $planilla->id)
                ->where('es_predeterminada', true)
                ->update(['es_predeterminada' => false]);
        }

        foreach (['fondo_pagina_1', 'fondo_pagina_2'] as $campo) {
            if (!empty($validated[$campo])) {
                $validated[$campo] = basename(str_replace('\\', '/', $validated[$campo]));
            }
        }

        $fondosDisponibles = collect($this->listarFondosDisponibles())->pluck('file')->all();
        foreach (['fondo_pagina_1', 'fondo_pagina_2'] as $campo) {
            if (!empty($validated[$campo]) && !in_array($validated[$campo], $fondosDisponibles, true)) {
                return back()
                    ->withInput()
                    ->withErrors([$campo => 'El fondo seleccionado no existe en la carpeta de planillas.']);
            }
        }

        $planilla->update($validated);

        return redirect()->route('planillas.edit', $planilla)
            ->with('success', 'Planilla actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $planilla = Planilla::findOrFail($id);
        $planilla->delete();
        return redirect()->route('planillas.index')->with('success', 'Planilla eliminada.');
    }

    /**
     * Retorna la ruta absoluta permitida para fondos de planilla.
     * Preferimos base_path('planilla') (carpeta del repositorio) y caemos a public/planilla.
     */
    private function obtenerDirectorioFondos(): ?string
    {
        $candidatos = [
            base_path('planilla'),
            public_path('planilla'),
        ];

        foreach ($candidatos as $dir) {
            if (is_dir($dir)) return $dir;
        }

        return null;
    }

    /**
     * Lista imágenes disponibles dentro de la carpeta permitida.
     * Devuelve: [ ['file' => 'x.jpg', 'label' => 'x.jpg'], ... ]
     */
    private function listarFondosDisponibles(): array
    {
        $dir = $this->obtenerDirectorioFondos();
        if (!$dir) return [];

        $files = @scandir($dir) ?: [];
        $imagenes = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($path)) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) continue;

            $imagenes[] = [
                'file' => $file,
                'label' => $file,
            ];
        }

        usort($imagenes, fn ($a, $b) => strcmp($a['file'], $b['file']));
        return $imagenes;
    }

    private function leerEstructuraHtmlBase(): string
    {
        $path = base_path('planilla/estructura_planilla.html');
        if (is_file($path)) {
            return file_get_contents($path) ?: '';
        }
        return '';
    }

    private function leerCssBase(): string
    {
        $path = base_path('planilla/style_estructura_planilla.css');
        if (is_file($path)) {
            return file_get_contents($path) ?: '';
        }
        return '';
    }
}
