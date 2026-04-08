@extends('layouts.app')

@section('page-title', 'Plantillas PDF')
@section('page-description', 'Gestiona las plantillas para generar certificados en PDF')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold text-slate-800">Plantillas de Certificados</h3>
    <a href="{{ route('plantillas-pdf.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Nueva Plantilla
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($plantillas as $plantilla)
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition group">
            <div class="h-40 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                <div class="text-center text-slate-400">
                    <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-1"></i>
                    <span class="text-xs">{{ ucfirst($plantilla->orientacion) }} &bull; {{ strtoupper($plantilla->tamano_papel) }}</span>
                </div>
                @if($plantilla->es_predeterminada)
                    <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-0.5 rounded font-semibold">Predeterminada</span>
                @endif
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-xs font-semibold {{ $plantilla->activa ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $plantilla->activa ? 'Activa' : 'Inactiva' }}
                </span>
            </div>

            <div class="p-4">
                <h4 class="font-semibold text-slate-800 truncate">{{ $plantilla->nombre }}</h4>
                @if($plantilla->descripcion)
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $plantilla->descripcion }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-2">Actualizada {{ $plantilla->updated_at->diffForHumans() }}</p>

                <div class="flex gap-2 mt-3">
                    <a href="{{ route('plantillas-pdf.edit', $plantilla) }}" class="flex-1 text-center bg-blue-600 text-white text-xs px-3 py-2 rounded hover:bg-blue-700 transition">Editar</a>
                    <a href="{{ route('plantillas-pdf.preview', $plantilla) }}" target="_blank" class="flex-1 text-center bg-purple-600 text-white text-xs px-3 py-2 rounded hover:bg-purple-700 transition">Preview</a>
                    <form action="{{ route('plantillas-pdf.duplicar', $plantilla) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-slate-200 text-slate-700 text-xs px-3 py-2 rounded hover:bg-slate-300 transition">Duplicar</button>
                    </form>
                </div>
                <form action="{{ route('plantillas-pdf.destroy', $plantilla) }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Eliminar esta plantilla?')" class="w-full text-red-500 text-xs hover:text-red-700 transition">Eliminar</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
            <i data-lucide="file-text" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
            <p class="text-slate-500 mb-4">No hay plantillas creadas aun</p>
            <a href="{{ route('plantillas-pdf.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Crear Primera Plantilla
            </a>
        </div>
    @endforelse
</div>

@if($plantillas->hasPages())
<div class="mt-6">{{ $plantillas->links() }}</div>
@endif
@endsection
