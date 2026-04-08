@extends('layouts.app')

@section('page-title', 'Detalle del Estatus')
@section('page-description', $estatus->nombre)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $estatus->nombre }}</h3>
                <p class="text-slate-500 mt-1">{{ $estatus->entidad }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('estatus.edit', $estatus) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
                <a href="{{ route('estatus.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-sm text-slate-500">Entidad</p>
                <p class="text-lg font-semibold text-slate-800">{{ ucfirst($estatus->entidad) }}</p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Orden Visual</p>
                <p class="text-lg font-semibold text-slate-800">{{ $estatus->orden_visual }}</p>
            </div>

            @if($estatus->descripcion)
                <div>
                    <p class="text-sm text-slate-500 mb-2">Descripción</p>
                    <p class="text-slate-700">{{ $estatus->descripcion }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
