@extends('layouts.app')

@section('page-title', 'Autoridades')
@section('page-description', 'Gestión de autoridades e instructores')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold text-slate-800">Lista de Autoridades</h3>
    <a href="{{ route('autoridades.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nueva Autoridad
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cargo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Especialidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($autoridades as $autoridad)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $autoridad->id }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $autoridad->nombre_completo }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $autoridad->cargo }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $autoridad->especialidad ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 {{ $autoridad->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs">
                            {{ $autoridad->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('autoridades.show', $autoridad) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                        <a href="{{ route('autoridades.edit', $autoridad) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('autoridades.destroy', $autoridad) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-slate-500">No hay autoridades registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $autoridades->links() }}
</div>
@endsection
