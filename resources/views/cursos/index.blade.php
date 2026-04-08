@extends('layouts.app')

@section('page-title', 'Cursos')
@section('page-description', 'Gestión de cursos académicos')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-slate-800">Lista de Cursos</h3>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('cursos.papelera') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition flex items-center gap-2">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            Papelera
        </a>
        <a href="{{ route('cursos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Curso
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Horas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($cursos as $curso)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $curso->id }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $curso->nombre_curso }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $curso->horas_academicas }}h</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($curso->estado)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $curso->estado->nombre }}</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Sin estado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('cursos.show', $curso) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                        <a href="{{ route('cursos.edit', $curso) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('cursos.destroy', $curso) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-slate-500">No hay cursos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $cursos->links() }}
</div>
@endsection
