@extends('layouts.app')

@section('page-title', 'Planilla')
@section('page-description', $planilla->nombre)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6 border border-slate-200">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h3 class="text-xl font-semibold text-slate-800">{{ $planilla->nombre }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $planilla->descripcion ?? 'Sin descripción' }}</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('planillas.edit', $planilla) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Editar</a>
                <a href="{{ route('planillas.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">Volver</a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <p class="text-xs text-slate-500">Fondo página 1</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ $planilla->fondo_pagina_1 ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <p class="text-xs text-slate-500">Fondo página 2</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ $planilla->fondo_pagina_2 ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-6 flex gap-2">
            @if($planilla->es_predeterminada)
                <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700">Predeterminada</span>
            @endif
            @if($planilla->activa)
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Activa</span>
            @else
                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">Inactiva</span>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border border-slate-200">
        <h4 class="text-sm font-semibold text-slate-800 mb-3">Estructura base (solo lectura)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-500 mb-2">HTML</p>
                <pre class="text-[11px] bg-slate-50 border border-slate-200 rounded-lg p-3 overflow-auto max-h-80">{{ $planilla->estructura_html }}</pre>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-2">CSS</p>
                <pre class="text-[11px] bg-slate-50 border border-slate-200 rounded-lg p-3 overflow-auto max-h-80">{{ $planilla->estilos_css }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

