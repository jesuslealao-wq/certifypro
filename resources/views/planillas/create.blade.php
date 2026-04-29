@extends('layouts.app')

@section('page-title', 'Nueva Planilla')
@section('page-description', 'Selecciona fondos para página 1 y 2 (solo desde la carpeta planilla)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6 border border-slate-200">
        <form action="{{ route('planillas.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required>
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fondo página 1</label>
                    <select name="fondo_pagina_1" class="w-full px-4 py-2 border border-slate-300 rounded-lg">
                        <option value="">Sin fondo</option>
                        @foreach($fondos as $f)
                            <option value="{{ $f['file'] }}" {{ old('fondo_pagina_1') === $f['file'] ? 'selected' : '' }}>
                                {{ $f['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('fondo_pagina_1')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fondo página 2</label>
                    <select name="fondo_pagina_2" class="w-full px-4 py-2 border border-slate-300 rounded-lg">
                        <option value="">Sin fondo</option>
                        @foreach($fondos as $f)
                            <option value="{{ $f['file'] }}" {{ old('fondo_pagina_2') === $f['file'] ? 'selected' : '' }}>
                                {{ $f['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('fondo_pagina_2')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-6 mb-6">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="activa" value="1" class="w-4 h-4 text-blue-600 rounded" {{ old('activa', true) ? 'checked' : '' }}>
                    Activa
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="es_predeterminada" value="1" class="w-4 h-4 text-amber-600 rounded" {{ old('es_predeterminada') ? 'checked' : '' }}>
                    Predeterminada
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Crear Planilla
                </button>
                <a href="{{ route('planillas.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>

        @if(empty($fondos))
            <div class="mt-6 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg p-4">
                No se encontraron imágenes en la carpeta `planilla/`. Agrega archivos `.jpg/.png/.webp` allí para poder seleccionarlos como fondo.
            </div>
        @endif
    </div>
</div>
@endsection

