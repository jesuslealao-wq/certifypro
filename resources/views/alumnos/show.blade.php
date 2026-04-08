@extends('layouts.app')

@section('page-title', 'Detalle del Alumno')
@section('page-description', $alumno->nombre_completo)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $alumno->nombre_completo }}</h3>
                <p class="text-slate-500 mt-1">{{ $alumno->identificacion_nacional }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('alumnos.edit', $alumno) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
                <a href="{{ route('alumnos.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-slate-500">Email</p>
                <p class="text-lg font-semibold text-slate-800">{{ $alumno->email ?? 'No registrado' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Teléfono</p>
                <p class="text-lg font-semibold text-slate-800">{{ $alumno->telefono ?? 'No registrado' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Certificados</h4>
        @if($alumno->certificados->count() > 0)
            <div class="space-y-3">
                @foreach($alumno->certificados as $certificado)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-semibold text-slate-800">{{ $certificado->cohorte->curso->nombre_curso }}</h5>
                                <p class="text-sm text-slate-500">Código: {{ $certificado->codigo_verificacion_app }}</p>
                                <p class="text-sm text-slate-500">Fecha: {{ $certificado->fecha_emision->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('certificados.show', $certificado) }}" class="text-blue-600 hover:text-blue-800 text-sm">Ver detalle</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-500 text-center py-4">No hay certificados emitidos para este alumno</p>
        @endif
    </div>
</div>
@endsection
