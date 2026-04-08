@extends('layouts.app')

@section('page-title', 'Clases')
@section('page-description', 'Gestión de clases')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold text-slate-800">Lista de Clases</h3>
    <a href="{{ route('clases.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nueva Clase
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Título</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Módulo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Orden</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($clases as $clase)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $clase->id }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $clase->titulo_clase }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">
                        @if($clase->modulo)
                            {{ $clase->modulo->titulo_modulo }}
                        @else
                            <span class="text-slate-400 italic">Módulo eliminado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $clase->orden }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('clases.show', $clase) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                        <a href="{{ route('clases.edit', $clase) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('clases.destroy', $clase) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-slate-500">No hay clases registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $clases->links() }}
</div>
@endsection
