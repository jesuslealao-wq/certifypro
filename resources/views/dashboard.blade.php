@extends('layouts.app')

@section('title', 'Dashboard - CertifyPro')
@section('page-title', 'Dashboard')
@section('page-description', 'Panel principal del sistema de certificados')

@section('content')
<div class="space-y-8">
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Cursos -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="book-open" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-600">Total Cursos</p>
                    <p class="text-2xl font-bold text-slate-900">{{ \App\Models\Curso::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total de Alumnos -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-600">Total Alumnos</p>
                    <p class="text-2xl font-bold text-slate-900">{{ \App\Models\Alumno::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total de Cohortes -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-600">Total Cohortes</p>
                    <p class="text-2xl font-bold text-slate-900">{{ \App\Models\Cohorte::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total de Certificados -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i data-lucide="award" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-600">Total Certificados</p>
                    <p class="text-2xl font-bold text-slate-900">{{ \App\Models\Certificado::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Acciones principales -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Acciones Principales</h3>
            <div class="space-y-3">
                <a href="{{ route('cursos.create') }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <i data-lucide="plus" class="w-5 h-5 text-blue-600"></i>
                    <span class="text-slate-700">Crear Nuevo Curso</span>
                </a>
                <a href="{{ route('alumnos.create') }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <i data-lucide="user-plus" class="w-5 h-5 text-green-600"></i>
                    <span class="text-slate-700">Registrar Alumno</span>
                </a>
                <a href="{{ route('cohortes.create') }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-purple-600"></i>
                    <span class="text-slate-700">Crear Cohorte</span>
                </a>
                <a href="{{ route('certificados.index') }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <i data-lucide="file-text" class="w-5 h-5 text-orange-600"></i>
                    <span class="text-slate-700">Ver Certificados</span>
                </a>
            </div>
        </div>

        <!-- Información del sistema -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Información del Sistema</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <i data-lucide="user" class="w-5 h-5 text-slate-500"></i>
                    <span class="text-slate-700">Usuario: <strong>{{ auth()->user()->name }}</strong></span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
                    <span class="text-slate-700">Email: {{ auth()->user()->email }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="clock" class="w-5 h-5 text-slate-500"></i>
                    <span class="text-slate-700">Último acceso: {{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="settings" class="w-5 h-5 text-slate-500"></i>
                    <span class="text-slate-700">
                        <a href="{{ route('account.edit') }}" class="text-blue-600 hover:text-blue-800">Configurar cuenta</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Actividad Reciente</h3>
        <div class="text-center py-8 text-slate-500">
            <i data-lucide="activity" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
            <p>Las actividades recientes se mostrarán aquí próximamente.</p>
        </div>
    </div>
</div>
@endsection
