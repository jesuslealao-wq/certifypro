@extends('layouts.app')

@section('page-title', 'Editar Alumno')
@section('page-description', 'Modificar información del alumno')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('alumnos.update', $alumno) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Identificación Nacional</label>
                <input type="text" name="identificacion_nacional" value="{{ old('identificacion_nacional', $alumno->identificacion_nacional) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('identificacion_nacional')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre Completo</label>
                <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $alumno->nombre_completo) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('nombre_completo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $alumno->email) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $alumno->telefono) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('telefono')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Actualizar Alumno
                </button>
                <a href="{{ route('alumnos.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
