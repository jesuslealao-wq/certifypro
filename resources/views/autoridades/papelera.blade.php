@extends('layouts.app')

@section('page-title', 'Papelera de Autoridades')
@section('page-description', 'Elementos eliminados')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-slate-800">Papelera de Autoridades</h3>
        <p class="text-sm text-slate-500 mt-1">Elementos eliminados que pueden ser restaurados</p>
    </div>
    <a href="{{ route('autoridades.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Volver
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cargo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Eliminado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($autoridades as $autoridad)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $autoridad->nombre_completo }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $autoridad->cargo }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $autoridad->deleted_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <form action="{{ route('autoridades.restore', $autoridad->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Restaurar</button>
                        </form>
                        <form action="{{ route('autoridades.forceDelete', $autoridad->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                onclick="return confirm('¿Eliminar permanentemente?')">Eliminar Definitivamente</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">La papelera está vacía</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $autoridades->links() }}
</div>
@endsection
