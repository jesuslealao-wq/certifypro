@extends('layouts.app')

@section('page-title', 'Cohortes')
@section('page-description', 'Gestión de cohortes y ejecuciones de cursos')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold text-slate-800">Lista de Cohortes</h3>
    <a href="{{ route('cohortes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nueva Cohorte
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Curso</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Instructor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fechas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Modalidad</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($cohortes as $cohorte)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-900">{{ $cohorte->codigo_cohorte ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $cohorte->curso->nombre_curso }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $cohorte->instructor->nombre_completo ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                        {{ $cohorte->fecha_inicio ? $cohorte->fecha_inicio->format('d/m/Y') : 'N/A' }} - 
                        {{ $cohorte->fecha_fin ? $cohorte->fecha_fin->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $cohorte->modalidad ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('cohortes.show', $cohorte) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                        <a href="{{ route('cohortes.edit', $cohorte) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('cohortes.destroy', $cohorte) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-slate-500">No hay cohortes registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $cohortes->links() }}
</div>
@endsection
