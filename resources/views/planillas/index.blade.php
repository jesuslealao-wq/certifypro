@extends('layouts.app')

@section('page-title', 'Planillas')
@section('page-description', 'Crea y administra planillas (estructura base + fondos por página)')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-semibold text-slate-800">Listado</h3>
            <p class="text-sm text-slate-500">Las planillas se usan en certificados para aplicar la estructura base y los fondos.</p>
        </div>
        <a href="{{ route('planillas.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Nueva Planilla
        </a>
    </div>

    @if($planillas->count() === 0)
        <div class="bg-white rounded-lg shadow p-10 text-center border border-slate-200">
            <p class="text-slate-600">Aún no hay planillas creadas.</p>
            <a href="{{ route('planillas.create') }}" class="inline-block mt-4 bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                Crear la primera planilla
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($planillas as $planilla)
                <div class="bg-white rounded-lg shadow border border-slate-200 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $planilla->nombre }}</p>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $planilla->descripcion ?? 'Sin descripción' }}</p>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            @if($planilla->es_predeterminada)
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Predeterminada</span>
                            @endif
                            @if($planilla->activa)
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-700">Activa</span>
                            @else
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Inactiva</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-slate-600 space-y-1">
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Fondo pág. 1</span>
                            <span class="font-medium truncate">{{ $planilla->fondo_pagina_1 ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Fondo pág. 2</span>
                            <span class="font-medium truncate">{{ $planilla->fondo_pagina_2 ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <a href="{{ route('planillas.show', $planilla) }}" class="text-sm text-slate-600 hover:text-slate-900">Ver</a>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('planillas.edit', $planilla) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                            <form action="{{ route('planillas.destroy', $planilla) }}" method="POST" onsubmit="return confirm('¿Eliminar esta planilla?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $planillas->links() }}
        </div>
    @endif
</div>
@endsection

