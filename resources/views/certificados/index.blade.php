@extends('layouts.app')

@section('page-title', 'Certificados')
@section('page-description', 'Gestión de certificados emitidos')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold text-slate-800">Lista de Certificados</h3>
    <a href="{{ route('certificados.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nuevo Certificado
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Alumno</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Curso</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha Emisión</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($certificados as $certificado)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-900">{{ $certificado->codigo_verificacion_app }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $certificado->alumno->nombre_completo }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900">{{ $certificado->cohorte->curso->nombre_curso }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $certificado->fecha_emision->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 {{ $certificado->estado == 'valido' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs">
                            {{ ucfirst($certificado->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('certificados.pdf', $certificado) }}" target="_blank" class="text-green-600 hover:text-green-900 mr-2" title="Ver PDF">PDF</a>
                        <a href="{{ route('certificados.descargar-pdf', $certificado) }}" class="text-amber-600 hover:text-amber-900 mr-2" title="Descargar PDF">
                            <i data-lucide="download" class="w-3.5 h-3.5 inline"></i>
                        </a>
                        <a href="{{ route('certificados.show', $certificado) }}" class="text-blue-600 hover:text-blue-900 mr-2">Ver</a>
                        <a href="{{ route('certificados.edit', $certificado) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                        <form action="{{ route('certificados.destroy', $certificado) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-slate-500">No hay certificados emitidos</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $certificados->links() }}
</div>
@endsection
