@extends('layouts.app')

@section('page-title', 'Editar Cohorte')
@section('page-description', 'Modificar información de la cohorte')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('cohortes.update', $cohorte) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Curso</label>
                <select name="curso_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Seleccione un curso</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}" {{ old('curso_id', $cohorte->curso_id) == $curso->id ? 'selected' : '' }}>
                            {{ $curso->nombre_curso }}
                        </option>
                    @endforeach
                </select>
                @error('curso_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Código de Cohorte</label>
                <input type="text" name="codigo_cohorte" value="{{ old('codigo_cohorte', $cohorte->codigo_cohorte) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('codigo_cohorte')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Instructor</label>
                <select name="instructor_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccione un instructor</option>
                    @foreach($autoridades as $autoridad)
                        <option value="{{ $autoridad->id }}" {{ old('instructor_id', $cohorte->instructor_id) == $autoridad->id ? 'selected' : '' }}>
                            {{ $autoridad->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('instructor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $cohorte->fecha_inicio?->format('Y-m-d')) }}" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('fecha_inicio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $cohorte->fecha_fin?->format('Y-m-d')) }}" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('fecha_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Modalidad</label>
                <select name="modalidad" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccione una modalidad</option>
                    <option value="presencial" {{ old('modalidad', $cohorte->modalidad) == 'presencial' ? 'selected' : '' }}>Presencial</option>
                    <option value="online_vivo" {{ old('modalidad', $cohorte->modalidad) == 'online_vivo' ? 'selected' : '' }}>Online en Vivo</option>
                    <option value="hibrido" {{ old('modalidad', $cohorte->modalidad) == 'hibrido' ? 'selected' : '' }}>Híbrido</option>
                </select>
                @error('modalidad')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Actualizar Cohorte
                </button>
                <a href="{{ route('cohortes.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
