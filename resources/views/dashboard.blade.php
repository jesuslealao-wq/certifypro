@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-description', 'Resumen del sistema de gestión de certificados')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Total Cursos</p>
                <p class="text-3xl font-bold text-slate-900">{{ \App\Models\Curso::count() }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i data-lucide="book-open" class="w-8 h-8 text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Total Alumnos</p>
                <p class="text-3xl font-bold text-slate-900">{{ \App\Models\Alumno::count() }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i data-lucide="users" class="w-8 h-8 text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Cohortes Activas</p>
                <p class="text-3xl font-bold text-slate-900">{{ \App\Models\Cohorte::count() }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i data-lucide="calendar" class="w-8 h-8 text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Certificados Emitidos</p>
                <p class="text-3xl font-bold text-slate-900">{{ \App\Models\Certificado::count() }}</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-lg">
                <i data-lucide="award" class="w-8 h-8 text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Accesos Rápidos</h3>
        <div class="space-y-2">
            <a href="{{ route('cursos.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition">
                <i data-lucide="plus-circle" class="w-5 h-5 text-blue-600"></i>
                <span class="text-slate-700">Crear Nuevo Curso</span>
            </a>
            <a href="{{ route('alumnos.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition">
                <i data-lucide="user-plus" class="w-5 h-5 text-green-600"></i>
                <span class="text-slate-700">Registrar Alumno</span>
            </a>
            <a href="{{ route('cohortes.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition">
                <i data-lucide="calendar-plus" class="w-5 h-5 text-purple-600"></i>
                <span class="text-slate-700">Nueva Cohorte</span>
            </a>
            <a href="{{ route('certificados.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition">
                <i data-lucide="file-plus" class="w-5 h-5 text-yellow-600"></i>
                <span class="text-slate-700">Emitir Certificado</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Últimos Certificados</h3>
        <div class="space-y-3">
            @forelse(\App\Models\Certificado::with('alumno', 'cohorte.curso')->orderBy('id', 'desc')->take(5)->get() as $cert)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div>
                        <p class="font-medium text-slate-800">{{ $cert->alumno->nombre_completo }}</p>
                        <p class="text-sm text-slate-500">{{ $cert->cohorte->curso->nombre_curso }}</p>
                    </div>
                    <span class="text-xs text-slate-400">{{ $cert->fecha_emision->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="text-slate-500 text-center py-4">No hay certificados emitidos</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
