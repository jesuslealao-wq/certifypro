@extends('layouts.app')

@section('page-title', 'Detalle de la Clase')
@section('page-description', $clase->titulo_clase)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $clase->titulo_clase }}</h3>
                <p class="text-slate-500 mt-1">{{ $clase->modulo->titulo_modulo }}</p>
                <p class="text-sm text-slate-400">{{ $clase->modulo->curso->nombre_curso }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('clases.edit', $clase) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
                <a href="{{ route('clases.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div>
            <p class="text-sm text-slate-500">Orden</p>
            <p class="text-lg font-semibold text-slate-800">{{ $clase->orden }}</p>
        </div>
    </div>
</div>
@endsection
