@extends('layouts.app')

@section('page-title', 'Detalle del Certificado')
@section('page-description', 'Código: ' . $certificado->codigo_verificacion_app)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Certificado</h3>
                <p class="text-slate-500 mt-1 font-mono">{{ $certificado->codigo_verificacion_app }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('certificados.pdf', $certificado) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Ver PDF
                </a>
                <a href="{{ route('certificados.descargar-pdf', $certificado) }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition flex items-center gap-1">
                    <i data-lucide="download" class="w-4 h-4"></i> Descargar
                </a>
                <a href="{{ route('certificados.edit', $certificado) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
                <a href="{{ route('certificados.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-500">Alumno</p>
                <p class="text-lg font-semibold text-slate-800">{{ $certificado->alumno->nombre_completo }}</p>
                <p class="text-sm text-slate-500">{{ $certificado->alumno->identificacion_nacional }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Curso</p>
                <p class="text-lg font-semibold text-slate-800">{{ $certificado->cohorte->curso->nombre_curso }}</p>
                <p class="text-sm text-slate-500">Cohorte: {{ $certificado->cohorte->codigo_cohorte }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-sm text-slate-500">Libro</p>
                <p class="text-lg font-semibold text-slate-800">{{ $certificado->libro }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Folio</p>
                <p class="text-lg font-semibold text-slate-800">{{ $certificado->folio }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Fecha de Emisión</p>
                <p class="text-lg font-semibold text-slate-800">{{ $certificado->fecha_emision->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-slate-500 mb-2">Estado</p>
            <span class="px-3 py-1 {{ $certificado->estado == 'valido' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-sm font-semibold">
                {{ ucfirst($certificado->estado) }}
            </span>
        </div>

        @if($certificado->firma1 || $certificado->firma2 || $certificado->firma3)
            <div class="border-t pt-6">
                <h4 class="text-lg font-semibold text-slate-800 mb-4">Firmas Autorizadas</h4>
                <div class="grid grid-cols-3 gap-4">
                    @if($certificado->firma1)
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Firma 1</p>
                            <p class="font-semibold text-slate-800">{{ $certificado->firma1->nombre_completo }}</p>
                            <p class="text-sm text-slate-600">{{ $certificado->firma1->cargo }}</p>
                        </div>
                    @endif
                    @if($certificado->firma2)
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Firma 2</p>
                            <p class="font-semibold text-slate-800">{{ $certificado->firma2->nombre_completo }}</p>
                            <p class="text-sm text-slate-600">{{ $certificado->firma2->cargo }}</p>
                        </div>
                    @endif
                    @if($certificado->firma3)
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Firma 3</p>
                            <p class="font-semibold text-slate-800">{{ $certificado->firma3->nombre_completo }}</p>
                            <p class="text-sm text-slate-600">{{ $certificado->firma3->cargo }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($certificado->uuid_seguridad)
            <div class="border-t pt-6 mt-6">
                <p class="text-sm text-slate-500">UUID de Seguridad</p>
                <p class="text-sm font-mono text-slate-700">{{ $certificado->uuid_seguridad }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
