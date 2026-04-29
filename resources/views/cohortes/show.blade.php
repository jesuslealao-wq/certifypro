@extends('layouts.app')

@section('page-title', 'Detalle de Cohorte')
@section('page-description', $cohorte->codigo_cohorte ?? 'Cohorte')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Cabecera --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $cohorte->curso->nombre_curso }}</h3>
                <p class="text-slate-500 mt-1">{{ $cohorte->codigo_cohorte }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cohortes.edit', $cohorte) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Editar</a>
                <a href="{{ route('cohortes.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">Volver</a>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><p class="text-xs text-slate-500">Instructor</p><p class="font-semibold text-slate-800">{{ $cohorte->instructor->nombre_completo ?? 'No asignado' }}</p></div>
            <div><p class="text-xs text-slate-500">Modalidad</p><p class="font-semibold text-slate-800">{{ ucfirst($cohorte->modalidad ?? '-') }}</p></div>
            <div><p class="text-xs text-slate-500">Inicio</p><p class="font-semibold text-slate-800">{{ $cohorte->fecha_inicio ? $cohorte->fecha_inicio->format('d/m/Y') : '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Fin</p><p class="font-semibold text-slate-800">{{ $cohorte->fecha_fin ? $cohorte->fecha_fin->format('d/m/Y') : '-' }}</p></div>
        </div>
    </div>

    {{-- Configuracion Masiva --}}
    <div class="bg-white rounded-lg shadow p-6">
            <h4 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                <i data-lucide="settings-2" class="w-4 h-4"></i> Configuracion Masiva de Certificados
            </h4>
            @if($cohorte->certificados->count() > 0)
            <form action="{{ route('cohortes.configurar-certificados-masivo', $cohorte) }}" method="POST">
                @csrf
                <p class="text-xs text-slate-500 mb-3">Marca los campos que deseas actualizar en todos los certificados de esta cohorte.</p>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="libro" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Libro</span>
                        </label>
                        <input type="text" name="libro" placeholder="Ej: 2024" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="folio" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Folio (inicio secuencial)</span>
                        </label>
                        <input type="number" name="folio_inicio" placeholder="Ej: 1" min="1" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="codigo_registro_manual" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Cod. Registro (prefijo)</span>
                        </label>
                        <input type="text" name="codigo_registro_prefijo" placeholder="Ej: REG-2024" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="fecha_emision" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Fecha Emision</span>
                        </label>
                        <input type="date" name="fecha_emision" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="estado_id" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Estado</span>
                        </label>
                        <select name="estado_id" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                            <option value="">Seleccionar...</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="firma_1_id" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Firma 1</span>
                        </label>
                        <select name="firma_1_id" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                            <option value="">Ninguna</option>
                            @foreach($autoridades as $auth)
                                <option value="{{ $auth->id }}">{{ $auth->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="firma_2_id" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Firma 2</span>
                        </label>
                        <select name="firma_2_id" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                            <option value="">Ninguna</option>
                            @foreach($autoridades as $auth)
                                <option value="{{ $auth->id }}">{{ $auth->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="campos[]" value="firma_3_id" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm font-medium text-slate-700">Firma 3</span>
                        </label>
                        <select name="firma_3_id" class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm">
                            <option value="">Ninguna</option>
                            @foreach($autoridades as $auth)
                                <option value="{{ $auth->id }}">{{ $auth->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" onclick="return confirm('Esto actualizara TODOS los certificados de esta cohorte con los campos seleccionados. Continuar?')" class="bg-amber-600 text-white text-sm px-5 py-2 rounded-lg hover:bg-amber-700 transition">
                    Aplicar a {{ $cohorte->certificados->count() }} certificado(s)
                </button>
            </form>
            @else
                <p class="text-sm text-slate-500">Genera certificados primero para poder configurarlos masivamente.</p>
            @endif
    </div>

    {{-- Alumnos Inscritos --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4"></i> Alumnos Inscritos ({{ $cohorte->alumnos->count() }})
            </h4>
            <button onclick="openAgregarAlumnosModal()" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-1">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Agregar
            </button>
        </div>
        @if($cohorte->alumnos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($cohorte->alumnos as $alumno)
                    <div class="flex items-center justify-between p-2 bg-slate-50 rounded border border-slate-200">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $alumno->nombre_completo }}</p>
                                <p class="text-xs text-slate-500">{{ $alumno->identificacion_nacional }}</p>
                            </div>
                        </div>
                        <form action="{{ route('cohortes.alumnos.remover', [$cohorte, $alumno]) }}" method="POST" class="shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Remover alumno?')" class="text-red-500 hover:text-red-700 p-1">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200">
                <p class="text-slate-500 text-sm">No hay alumnos inscritos</p>
            </div>
        @endif
    </div>

    {{-- Certificados --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4"></i> Certificados ({{ $cohorte->certificados->count() }})
            </h4>
            <div class="flex gap-2">
                @if($cohorte->certificados->count() > 0)
                    <a href="{{ route('cohortes.generar-pdfs', $cohorte) }}" target="_blank" class="bg-purple-600 text-white px-3 py-1.5 rounded-lg hover:bg-purple-700 transition text-sm flex items-center gap-1">
                        <i data-lucide="printer" class="w-4 h-4"></i> Imprimir Todos
                    </a>
                    <a href="{{ route('cohortes.descargar-pdfs', $cohorte) }}" class="bg-amber-600 text-white px-3 py-1.5 rounded-lg hover:bg-amber-700 transition text-sm flex items-center gap-1">
                        <i data-lucide="download" class="w-4 h-4"></i> Descargar PDF
                    </a>
                @endif
                <button onclick="openGenerarMasivoModal()" class="bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 transition text-sm flex items-center gap-1" {{ $cohorte->alumnos->count() == 0 ? 'disabled' : '' }}>
                    <i data-lucide="file-plus" class="w-4 h-4"></i> Generar Masivo
                </button>
            </div>
        </div>

        @if($cohorte->certificados->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-200">
                            <th class="pb-2 text-xs font-semibold text-slate-500">Alumno</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500">Libro</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500">Folio</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500">Cod. Registro</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500">Fecha Emision</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500">Estado</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500">Firmas</th>
                            <th class="pb-2 text-xs font-semibold text-slate-500 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cohorte->certificados as $cert)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-2">
                                    <p class="font-medium text-slate-800">{{ $cert->alumno->nombre_completo }}</p>
                                    <p class="text-xs text-slate-400">{{ $cert->codigo_verificacion_app }}</p>
                                </td>
                                <td class="py-2 text-slate-600">{{ $cert->libro ?? '-' }}</td>
                                <td class="py-2 text-slate-600">{{ $cert->folio ?? '-' }}</td>
                                <td class="py-2 text-slate-600 text-xs">{{ $cert->codigo_registro_manual ?? '-' }}</td>
                                <td class="py-2 text-slate-600">{{ $cert->fecha_emision ? $cert->fecha_emision->format('d/m/Y') : '-' }}</td>
                                <td class="py-2">
                                    @if($cert->estadoRelacion)
                                        <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">{{ $cert->estadoRelacion->nombre }}</span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-2 text-xs text-slate-500">
                                    {{ $cert->firma1->nombre_completo ?? '-' }} /
                                    {{ $cert->firma2->nombre_completo ?? '-' }} /
                                    {{ $cert->firma3->nombre_completo ?? '-' }}
                                </td>
                                <td class="py-2 text-right whitespace-nowrap">
                                    <a href="{{ route('certificados.pdf', $cert) }}" target="_blank" class="text-green-600 hover:text-green-800 text-xs font-medium">PDF</a>
                                    <button type="button" onclick="openEditCertModal({{ $cert->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium ml-2">Editar</button>
                                    <a href="{{ route('certificados.show', $cert) }}" class="text-purple-600 hover:text-purple-800 text-xs font-medium ml-2">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-slate-500 text-center py-8 text-sm">No hay certificados generados aun</p>
        @endif
    </div>
</div>

<!-- Modal Agregar Alumnos a Cohorte -->
<div id="agregarAlumnosModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-xl font-semibold text-slate-800">Agregar Alumnos a la Cohorte</h3>
            <button onclick="closeAgregarAlumnosModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form action="{{ route('cohortes.alumnos.agregar', $cohorte) }}" method="POST" class="flex-1 overflow-y-auto">
            @csrf
            <div class="p-6">
                @if($alumnosNoInscritos->count() > 0)
                    <p class="text-sm text-slate-600 mb-4">Selecciona los alumnos que deseas agregar a esta cohorte:</p>
                    <div class="space-y-2">
                        @foreach($alumnosNoInscritos as $alumno)
                            <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" name="alumnos[]" value="{{ $alumno->id }}" 
                                    class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">{{ $alumno->nombre_completo }}</p>
                                    <p class="text-sm text-slate-500">{{ $alumno->identificacion_nacional }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="user-check" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                        <p class="text-slate-500">Todos los alumnos ya están inscritos en esta cohorte</p>
                    </div>
                @endif
            </div>

            @if($alumnosNoInscritos->count() > 0)
                <div class="flex gap-3 justify-end p-6 border-t bg-slate-50">
                    <button type="button" onclick="closeAgregarAlumnosModal()" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Agregar Seleccionados
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Modal Generar Certificados Masivo -->
<div id="generarMasivoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-xl font-semibold text-slate-800">Generar Certificados Masivamente</h3>
            <button onclick="closeGenerarMasivoModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="generarMasivoForm" action="{{ route('cohortes.generar-certificados-masivo', $cohorte) }}" method="POST" class="flex-1 overflow-y-auto">
            @csrf
            <div class="p-6">
                <p class="text-sm text-slate-600 mb-4">Selecciona los alumnos e ingresa sus calificaciones para generar los certificados.</p>
                
                @if($alumnosDisponibles->isEmpty())
                    <div class="text-center py-12">
                        <i data-lucide="users" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                        <p class="text-slate-500">No hay alumnos disponibles</p>
                        <p class="text-sm text-slate-400 mt-2">Crea alumnos primero para poder generar certificados</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($alumnosDisponibles as $index => $alumno)
                            @php
                                $yaTieneCertificado = $alumnosConCertificado->contains($alumno->id);
                            @endphp
                        <div class="flex items-center gap-4 p-3 border border-slate-200 rounded-lg {{ $yaTieneCertificado ? 'bg-slate-50 opacity-60' : 'hover:bg-slate-50' }}">
                            <input type="checkbox" 
                                name="alumnos[{{ $index }}][alumno_id]" 
                                value="{{ $alumno->id }}" 
                                id="alumno_{{ $alumno->id }}"
                                {{ $yaTieneCertificado ? 'disabled' : '' }}
                                onchange="toggleCalificacion({{ $alumno->id }})"
                                class="w-5 h-5 text-green-600 rounded focus:ring-green-500">
                            
                            <div class="flex-1">
                                <label for="alumno_{{ $alumno->id }}" class="font-medium text-slate-800 cursor-pointer">
                                    {{ $alumno->nombre_completo }}
                                </label>
                                <p class="text-sm text-slate-500">{{ $alumno->identificacion_nacional }}</p>
                                @if($yaTieneCertificado)
                                    <span class="text-xs text-amber-600 font-medium">Ya tiene certificado generado</span>
                                @endif
                            </div>
                            
                            <div class="w-32">
                                <input type="number" 
                                    name="alumnos[{{ $index }}][calificacion_final]" 
                                    id="calificacion_{{ $alumno->id }}"
                                    min="0" 
                                    max="100" 
                                    step="0.01"
                                    placeholder="Nota"
                                    {{ $yaTieneCertificado ? 'disabled' : '' }}
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 disabled:bg-slate-100">
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex gap-3 justify-between items-center p-6 border-t bg-slate-50">
                <div class="text-sm text-slate-600">
                    <span id="selectedCount">0</span> alumno(s) seleccionado(s)
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeGenerarMasivoModal()" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                        Generar Certificados
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- Modal Editar Certificado Individual --}}
@foreach($cohorte->certificados as $cert)
<div id="editCertModal-{{ $cert->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="text-lg font-semibold text-slate-800">Editar Certificado - {{ $cert->alumno->nombre_completo }}</h3>
            <button onclick="closeEditCertModal({{ $cert->id }})" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('cohortes.certificados.update', [$cohorte, $cert]) }}" method="POST" class="p-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Libro</label>
                    <input type="text" name="libro" value="{{ $cert->libro }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Folio</label>
                    <input type="text" name="folio" value="{{ $cert->folio }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Cod. Registro Manual</label>
                    <input type="text" name="codigo_registro_manual" value="{{ $cert->codigo_registro_manual }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha Emision</label>
                    <input type="date" name="fecha_emision" value="{{ $cert->fecha_emision ? $cert->fecha_emision->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                <select name="estado_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="">Sin estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ $cert->estado_id == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Firma 1</label>
                    <select name="firma_1_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="">Ninguna</option>
                        @foreach($autoridades as $auth)
                            <option value="{{ $auth->id }}" {{ $cert->firma_1_id == $auth->id ? 'selected' : '' }}>{{ $auth->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Firma 2</label>
                    <select name="firma_2_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="">Ninguna</option>
                        @foreach($autoridades as $auth)
                            <option value="{{ $auth->id }}" {{ $cert->firma_2_id == $auth->id ? 'selected' : '' }}>{{ $auth->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Firma 3</label>
                    <select name="firma_3_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="">Ninguna</option>
                        @foreach($autoridades as $auth)
                            <option value="{{ $auth->id }}" {{ $cert->firma_3_id == $auth->id ? 'selected' : '' }}>{{ $auth->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-3 border-t">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition text-sm">Guardar Cambios</button>
                <button type="button" onclick="closeEditCertModal({{ $cert->id }})" class="bg-slate-200 text-slate-700 px-5 py-2 rounded-lg hover:bg-slate-300 transition text-sm">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function openEditCertModal(certId) {
    document.getElementById('editCertModal-' + certId).classList.remove('hidden');
    lucide.createIcons();
}

function closeEditCertModal(certId) {
    document.getElementById('editCertModal-' + certId).classList.add('hidden');
}

function openAgregarAlumnosModal() {
    document.getElementById('agregarAlumnosModal').classList.remove('hidden');
    lucide.createIcons();
}

function closeAgregarAlumnosModal() {
    document.getElementById('agregarAlumnosModal').classList.add('hidden');
}

function openGenerarMasivoModal() {
    @if($cohorte->alumnos->count() == 0)
        alert('Debes agregar alumnos a la cohorte primero');
        return;
    @endif
    document.getElementById('generarMasivoModal').classList.remove('hidden');
    updateSelectedCount();
    lucide.createIcons();
}

function closeGenerarMasivoModal() {
    document.getElementById('generarMasivoModal').classList.add('hidden');
}

function toggleCalificacion(alumnoId) {
    const checkbox = document.getElementById('alumno_' + alumnoId);
    const calificacion = document.getElementById('calificacion_' + alumnoId);
    
    if (checkbox.checked) {
        calificacion.required = true;
        calificacion.focus();
    } else {
        calificacion.required = false;
        calificacion.value = '';
    }
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('#generarMasivoForm input[type="checkbox"]:checked:not(:disabled)');
    document.getElementById('selectedCount').textContent = checkboxes.length;
}

// Validar formulario antes de enviar
document.getElementById('generarMasivoForm').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('#generarMasivoForm input[type="checkbox"]:checked:not(:disabled)');
    
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Debe seleccionar al menos un alumno');
        return false;
    }
    
    // Validar que todos los seleccionados tengan calificación
    let valid = true;
    checkboxes.forEach(function(checkbox) {
        const alumnoId = checkbox.value;
        const calificacion = document.getElementById('calificacion_' + alumnoId);
        
        if (!calificacion.value || calificacion.value < 0 || calificacion.value > 100) {
            valid = false;
            calificacion.classList.add('border-red-500');
        } else {
            calificacion.classList.remove('border-red-500');
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert('Todos los alumnos seleccionados deben tener una calificación válida (0-100)');
        return false;
    }
    
    return confirm(`¿Generar certificados para ${checkboxes.length} alumno(s)?`);
});

// Cerrar modales al hacer clic fuera
document.getElementById('agregarAlumnosModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAgregarAlumnosModal();
    }
});

document.getElementById('generarMasivoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerarMasivoModal();
    }
});

// Inicializar iconos
lucide.createIcons();
</script>
@endpush
