@extends('layouts.app')

@section('page-title', 'Editar Módulo')
@section('page-description', 'Modificar información del módulo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('modulos.update', $modulo) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Curso</label>
                <select name="curso_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Seleccione un curso</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}" {{ old('curso_id', $modulo->curso_id) == $curso->id ? 'selected' : '' }}>
                            {{ $curso->nombre_curso }}
                        </option>
                    @endforeach
                </select>
                @error('curso_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Título del Módulo</label>
                <input type="text" name="titulo_modulo" value="{{ old('titulo_modulo', $modulo->titulo_modulo) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('titulo_modulo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Horas del Módulo</label>
                    <input type="number" name="horas_modulo" value="{{ old('horas_modulo', $modulo->horas_modulo) }}" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        min="0">
                    @error('horas_modulo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="number" name="orden" value="{{ old('orden', $modulo->orden) }}" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        min="1">
                    @error('orden')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Actualizar Módulo
                </button>
                <a href="{{ route('modulos.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
