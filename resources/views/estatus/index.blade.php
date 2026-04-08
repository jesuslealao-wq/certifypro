@extends('layouts.app')

@section('page-title', 'Estatus')
@section('page-description', 'Gestión de estados del sistema')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold text-slate-800">Lista de Estatus</h3>
    <a href="{{ route('estatus.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nuevo Estatus
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Entidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Orden</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($estatus as $estado)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $estado->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $estado->entidad }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $estado->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $estado->orden_visual }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('estatus.show', $estado) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                        <a href="{{ route('estatus.edit', $estado) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('estatus.destroy', $estado) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-slate-500">No hay estatus registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $estatus->links() }}
</div>
@endsection
