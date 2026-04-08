@extends('layouts.app')

@section('page-title', 'Papelera de Cursos')
@section('page-description', 'Elementos eliminados')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-slate-800">Papelera de Cursos</h3>
        <p class="text-sm text-slate-500 mt-1">Elementos eliminados que pueden ser restaurados</p>
    </div>
    <a href="{{ route('cursos.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Volver a Cursos
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Horas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Eliminado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($cursos as $curso)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $curso->nombre_curso }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $curso->horas_academicas }}h</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $curso->deleted_at->format('d/m/Y H:i') }}
                        <span class="text-xs text-slate-400">({{ $curso->deleted_at->diffForHumans() }})</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <form action="{{ route('cursos.restore', $curso->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3 inline-flex items-center gap-1">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                Restaurar
                            </button>
                        </form>
                        <form action="{{ route('cursos.forceDelete', $curso->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center gap-1" 
                                onclick="return confirm('¿Eliminar permanentemente? Esta acción no se puede deshacer.')">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Eliminar Definitivamente
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                        <p class="text-slate-500 text-lg">La papelera está vacía</p>
                        <p class="text-slate-400 text-sm">No hay cursos eliminados</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $cursos->links() }}
</div>
@endsection
