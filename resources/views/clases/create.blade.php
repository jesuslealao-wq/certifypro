@extends('layouts.app')

@section('page-title', 'Crear Clase')
@section('page-description', 'Registrar una nueva clase')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('clases.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Módulo</label>
                <select name="modulo_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Seleccione un módulo</option>
                    @foreach($modulos as $modulo)
                        <option value="{{ $modulo->id }}" {{ old('modulo_id') == $modulo->id ? 'selected' : '' }}>
                            {{ $modulo->curso->nombre_curso }} - {{ $modulo->titulo_modulo }}
                        </option>
                    @endforeach
                </select>
                @error('modulo_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Título de la Clase</label>
                <input type="text" name="titulo_clase" value="{{ old('titulo_clase') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('titulo_clase')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                <input type="number" name="orden" value="{{ old('orden', 1) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    min="1">
                @error('orden')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Crear Clase
                </button>
                <a href="{{ route('clases.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
