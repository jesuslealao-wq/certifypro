@extends('layouts.app')

@section('page-title', 'Detalle del Módulo')
@section('page-description', $modulo->titulo_modulo)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $modulo->titulo_modulo }}</h3>
                <p class="text-slate-500 mt-1">{{ $modulo->curso->nombre_curso }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('modulos.edit', $modulo) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
                <a href="{{ route('modulos.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-slate-500">Horas del Módulo</p>
                <p class="text-lg font-semibold text-slate-800">{{ $modulo->horas_modulo ?? 'No especificado' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Orden</p>
                <p class="text-lg font-semibold text-slate-800">{{ $modulo->orden }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Clases del Módulo</h4>
        @if($modulo->clases->count() > 0)
            <div class="space-y-2">
                @foreach($modulo->clases as $clase)
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                        <div>
                            <p class="font-medium text-slate-800">{{ $clase->orden }}. {{ $clase->titulo_clase }}</p>
                        </div>
                        <a href="{{ route('clases.show', $clase) }}" class="text-blue-600 hover:text-blue-800 text-sm">Ver detalle</a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-500 text-center py-4">No hay clases registradas para este módulo</p>
        @endif
    </div>
</div>
@endsection
