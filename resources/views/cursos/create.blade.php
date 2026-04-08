@extends('layouts.app')

@section('page-title', 'Crear Curso')
@section('page-description', 'Registrar un nuevo curso académico')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('cursos.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre del Curso</label>
                <input type="text" name="nombre_curso" value="{{ old('nombre_curso') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('nombre_curso')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Horas Académicas</label>
                <input type="number" name="horas_academicas" value="{{ old('horas_academicas') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required min="1">
                @error('horas_academicas')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                <select name="estado_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccione un estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="4" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Crear Curso
                </button>
                <a href="{{ route('cursos.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
