@extends('layouts.app')

@section('page-title', 'Editar Certificado')
@section('page-description', 'Modificar información del certificado')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('certificados.update', $certificado) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alumno</label>
                <select name="alumno_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Seleccione un alumno</option>
                    @foreach($alumnos as $alumno)
                        <option value="{{ $alumno->id }}" {{ old('alumno_id', $certificado->alumno_id) == $alumno->id ? 'selected' : '' }}>
                            {{ $alumno->nombre_completo }} ({{ $alumno->identificacion_nacional }})
                        </option>
                    @endforeach
                </select>
                @error('alumno_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Cohorte</label>
                <select name="cohorte_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Seleccione una cohorte</option>
                    @foreach($cohortes as $cohorte)
                        <option value="{{ $cohorte->id }}" {{ old('cohorte_id', $certificado->cohorte_id) == $cohorte->id ? 'selected' : '' }}>
                            {{ $cohorte->curso->nombre_curso }} - {{ $cohorte->codigo_cohorte }}
                        </option>
                    @endforeach
                </select>
                @error('cohorte_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Libro</label>
                    <input type="text" name="libro" value="{{ old('libro', $certificado->libro) }}" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        required>
                    @error('libro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Folio</label>
                    <input type="text" name="folio" value="{{ old('folio', $certificado->folio) }}" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        required>
                    @error('folio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Código de Registro Manual</label>
                <input type="text" name="codigo_registro_manual" value="{{ old('codigo_registro_manual', $certificado->codigo_registro_manual) }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('codigo_registro_manual')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Emisión</label>
                <input type="date" name="fecha_emision" value="{{ old('fecha_emision', $certificado->fecha_emision ? $certificado->fecha_emision->format('Y-m-d') : '') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('fecha_emision')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                <select name="estado_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccione un estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ old('estado_id', $certificado->estado_id) == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Firmas</label>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Firma 1</label>
                        <select name="firma_1_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($autoridades as $autoridad)
                                <option value="{{ $autoridad->id }}" {{ old('firma_1_id', $certificado->firma_1_id) == $autoridad->id ? 'selected' : '' }}>{{ $autoridad->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Firma 2</label>
                        <select name="firma_2_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($autoridades as $autoridad)
                                <option value="{{ $autoridad->id }}" {{ old('firma_2_id', $certificado->firma_2_id) == $autoridad->id ? 'selected' : '' }}>{{ $autoridad->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Firma 3</label>
                        <select name="firma_3_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($autoridades as $autoridad)
                                <option value="{{ $autoridad->id }}" {{ old('firma_3_id', $certificado->firma_3_id) == $autoridad->id ? 'selected' : '' }}>{{ $autoridad->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Plantilla PDF</label>
                <select name="plantilla_pdf_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Usar la de la cohorte</option>
                    @foreach($plantillas as $plantilla)
                        <option value="{{ $plantilla->id }}" {{ old('plantilla_pdf_id', $certificado->plantilla_pdf_id) == $plantilla->id ? 'selected' : '' }}>
                            {{ $plantilla->nombre }} ({{ ucfirst($plantilla->orientacion) }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Si no seleccionas una, se usara la plantilla asignada a la cohorte.</p>
                @error('plantilla_pdf_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Actualizar Certificado
                </button>
                <a href="{{ route('certificados.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
