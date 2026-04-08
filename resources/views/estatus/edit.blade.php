@extends('layouts.app')

@section('page-title', 'Editar Estatus')
@section('page-description', 'Modificar información del estado')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('estatus.update', $estatus) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Entidad</label>
                <select name="entidad" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Seleccione una entidad</option>
                    <option value="curso" {{ old('entidad', $estatus->entidad) == 'curso' ? 'selected' : '' }}>Curso</option>
                    <option value="cohorte" {{ old('entidad', $estatus->entidad) == 'cohorte' ? 'selected' : '' }}>Cohorte</option>
                    <option value="inscripcion" {{ old('entidad', $estatus->entidad) == 'inscripcion' ? 'selected' : '' }}>Inscripción</option>
                    <option value="certificado" {{ old('entidad', $estatus->entidad) == 'certificado' ? 'selected' : '' }}>Certificado</option>
                </select>
                @error('entidad')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $estatus->nombre) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('descripcion', $estatus->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Orden Visual</label>
                <input type="number" name="orden_visual" value="{{ old('orden_visual', $estatus->orden_visual) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    min="0">
                @error('orden_visual')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Actualizar Estatus
                </button>
                <a href="{{ route('estatus.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
