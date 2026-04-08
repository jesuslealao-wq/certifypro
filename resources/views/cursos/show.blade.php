@extends('layouts.app')

@section('page-title', 'Detalle del Curso')
@section('page-description', $curso->nombre_curso)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $curso->nombre_curso }}</h3>
                <p class="text-slate-500 mt-1">{{ $curso->horas_academicas }} horas académicas</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cursos.edit', $curso) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar Curso
                </a>
                <a href="{{ route('cursos.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-slate-500">Estado</p>
                @if($curso->estado)
                    <p class="text-lg font-semibold text-slate-800">{{ $curso->estado->nombre }}</p>
                @else
                    <p class="text-lg font-semibold text-slate-400">Sin estado</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-slate-500">Fecha de Creación</p>
                <p class="text-lg font-semibold text-slate-800">{{ $curso->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        @if($curso->descripcion)
            <div class="mb-6">
                <p class="text-sm text-slate-500 mb-2">Descripción</p>
                <p class="text-slate-700">{{ $curso->descripcion }}</p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-semibold text-slate-800">Módulos y Clases</h4>
            <button onclick="openModuloModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Agregar Módulo
            </button>
        </div>

        <div id="modulos-container" class="space-y-4">
            @forelse($curso->modulos->sortBy('orden') as $modulo)
                <div class="border border-slate-200 rounded-lg p-4 modulo-item" data-modulo-id="{{ $modulo->id }}">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h5 class="font-semibold text-slate-800 text-lg">{{ $modulo->titulo_modulo }}</h5>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Orden: {{ $modulo->orden }}</span>
                                @if($modulo->horas_modulo)
                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $modulo->horas_modulo }}h</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="deleteModulo({{ $modulo->id }})" class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pl-4 border-l-2 border-slate-200">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-xs font-semibold text-slate-500 uppercase">Clases</p>
                            <button onclick="toggleClaseForm({{ $modulo->id }})" class="text-green-600 hover:text-green-800 text-sm flex items-center gap-1">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                Agregar Clase
                            </button>
                        </div>

                        <div id="clase-form-{{ $modulo->id }}" class="hidden mb-3 bg-slate-50 p-3 rounded-lg">
                            <form onsubmit="createClase(event, {{ $modulo->id }})" class="flex gap-2">
                                <input type="text" name="titulo_clase" placeholder="Título de la clase" required
                                    class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <input type="number" name="orden" placeholder="Orden" value="{{ $modulo->clases->count() + 1 }}" min="1"
                                    class="w-20 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                                    Guardar
                                </button>
                                <button type="button" onclick="toggleClaseForm({{ $modulo->id }})" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 text-sm">
                                    Cancelar
                                </button>
                            </form>
                        </div>

                        <ul class="space-y-2 clases-list" id="clases-{{ $modulo->id }}">
                            @forelse($modulo->clases->sortBy('orden') as $clase)
                                <li class="flex items-center justify-between p-2 hover:bg-slate-50 rounded clase-item" data-clase-id="{{ $clase->id }}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-slate-400 w-6">{{ $clase->orden }}.</span>
                                        <span class="text-sm text-slate-700">{{ $clase->titulo_clase }}</span>
                                    </div>
                                    <button onclick="deleteClase({{ $clase->id }}, {{ $modulo->id }})" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic py-2">No hay clases en este módulo</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-500">
                    <i data-lucide="folder-open" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                    <p class="text-lg">No hay módulos registrados</p>
                    <p class="text-sm">Comienza agregando un módulo al curso</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal para crear módulo -->
<div id="moduloModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center p-6 border-b border-slate-200">
            <h3 class="text-xl font-semibold text-slate-800">Nuevo Módulo</h3>
            <button onclick="closeModuloModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="moduloForm" onsubmit="createModulo(event)" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Título del Módulo</label>
                <input type="text" name="titulo_modulo" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Horas del Módulo</label>
                    <input type="number" name="horas_modulo" min="0"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="number" name="orden" value="{{ $curso->modulos->count() + 1 }}" min="1" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModuloModal()" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Crear Módulo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cursoId = {{ $curso->id }};
    const csrfToken = '{{ csrf_token() }}';

    // Hacer funciones globales
    window.openModuloModal = function() {
        document.getElementById('moduloModal').classList.remove('hidden');
        document.getElementById('moduloForm').reset();
        setTimeout(() => lucide.createIcons(), 100);
    };

    window.closeModuloModal = function() {
        document.getElementById('moduloModal').classList.add('hidden');
    };

    window.createModulo = async function(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(`/api/cursos/${cursoId}/modulos`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                location.reload();
            } else {
                alert(data.message || 'Error al crear el módulo');
            }
        } catch (error) {
            alert('Error al crear el módulo');
            console.error(error);
        }
    };

    window.deleteModulo = async function(moduloId) {
        if (!confirm('¿Está seguro de eliminar este módulo? Se eliminarán también todas sus clases.')) {
            return;
        }
        
        try {
            const response = await fetch(`/api/modulos/${moduloId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            
            if (response.ok) {
                const element = document.querySelector(`[data-modulo-id="${moduloId}"]`);
                if (element) {
                    element.remove();
                }
            } else {
                alert('Error al eliminar el módulo');
            }
        } catch (error) {
            alert('Error al eliminar el módulo');
            console.error(error);
        }
    };

    window.toggleClaseForm = function(moduloId) {
        const form = document.getElementById(`clase-form-${moduloId}`);
        if (form) {
            form.classList.toggle('hidden');
        }
    };

    window.createClase = async function(event, moduloId) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(`/api/modulos/${moduloId}/clases`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                location.reload();
            } else {
                alert(data.message || 'Error al crear la clase');
            }
        } catch (error) {
            alert('Error al crear la clase');
            console.error(error);
        }
    };

    window.deleteClase = async function(claseId, moduloId) {
        if (!confirm('¿Está seguro de eliminar esta clase?')) {
            return;
        }
        
        try {
            const response = await fetch(`/api/clases/${claseId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            
            if (response.ok) {
                const element = document.querySelector(`[data-clase-id="${claseId}"]`);
                if (element) {
                    element.remove();
                }
            } else {
                alert('Error al eliminar la clase');
            }
        } catch (error) {
            alert('Error al eliminar la clase');
            console.error(error);
        }
    };

    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('moduloModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                window.closeModuloModal();
            }
        });
    }

    // Inicializar iconos de Lucide
    lucide.createIcons();
});
</script>
@endpush
