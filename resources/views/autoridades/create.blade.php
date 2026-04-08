@extends('layouts.app')

@section('page-title', 'Crear Autoridad')
@section('page-description', 'Registrar una nueva autoridad o instructor')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('autoridades.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nombre Completo</label>
                <input type="text" name="nombre_completo" value="{{ old('nombre_completo') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('nombre_completo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Cargo</label>
                <input type="text" name="cargo" value="{{ old('cargo') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required>
                @error('cargo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Especialidad</label>
                <input type="text" name="especialidad" value="{{ old('especialidad') }}" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('especialidad')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sello (JPG)</label>
                    <input type="file" name="sello" id="sello-input" accept=".jpg,.jpeg" 
                        onchange="loadImageEditor(event, 'sello')"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-slate-500 mt-1">Formato: JPG, Máx: 2MB</p>
                    @error('sello')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    <div id="sello-editor" class="mt-3 hidden">
                        <div class="border border-slate-300 rounded-lg p-4 bg-slate-50">
                            <div class="flex justify-center items-center bg-white border border-slate-200 rounded" style="height: 200px; overflow: hidden;">
                                <img id="sello-preview-img" src="" alt="Preview Sello" style="transition: transform 0.3s ease;">
                            </div>
                            
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-medium text-slate-700 w-16">Zoom:</label>
                                    <input type="range" id="sello-zoom" min="50" max="200" value="100" 
                                        oninput="updateImageTransform('sello')"
                                        class="flex-1">
                                    <span id="sello-zoom-value" class="text-xs text-slate-600 w-12">100%</span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-medium text-slate-700 w-16">Rotación:</label>
                                    <input type="range" id="sello-rotation" min="0" max="360" value="0" 
                                        oninput="updateImageTransform('sello')"
                                        class="flex-1">
                                    <span id="sello-rotation-value" class="text-xs text-slate-600 w-12">0°</span>
                                </div>
                                
                                <div class="flex gap-2 mt-2">
                                    <button type="button" onclick="resetImageTransform('sello')" 
                                        class="text-xs bg-slate-200 text-slate-700 px-3 py-1 rounded hover:bg-slate-300">
                                        Resetear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Firma (JPG)</label>
                    <input type="file" name="firma" id="firma-input" accept=".jpg,.jpeg" 
                        onchange="loadImageEditor(event, 'firma')"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-slate-500 mt-1">Formato: JPG, Máx: 2MB</p>
                    @error('firma')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    <div id="firma-editor" class="mt-3 hidden">
                        <div class="border border-slate-300 rounded-lg p-4 bg-slate-50">
                            <div class="flex justify-center items-center bg-white border border-slate-200 rounded" style="height: 200px; overflow: hidden;">
                                <img id="firma-preview-img" src="" alt="Preview Firma" style="transition: transform 0.3s ease;">
                            </div>
                            
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-medium text-slate-700 w-16">Zoom:</label>
                                    <input type="range" id="firma-zoom" min="50" max="200" value="100" 
                                        oninput="updateImageTransform('firma')"
                                        class="flex-1">
                                    <span id="firma-zoom-value" class="text-xs text-slate-600 w-12">100%</span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-medium text-slate-700 w-16">Rotación:</label>
                                    <input type="range" id="firma-rotation" min="0" max="360" value="0" 
                                        oninput="updateImageTransform('firma')"
                                        class="flex-1">
                                    <span id="firma-rotation-value" class="text-xs text-slate-600 w-12">0°</span>
                                </div>
                                
                                <div class="flex gap-2 mt-2">
                                    <button type="button" onclick="resetImageTransform('firma')" 
                                        class="text-xs bg-slate-200 text-slate-700 px-3 py-1 rounded hover:bg-slate-300">
                                        Resetear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} 
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-slate-700">Activo</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Crear Autoridad
                </button>
                <a href="{{ route('autoridades.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadImageEditor(event, type) {
    const file = event.target.files[0];
    const editor = document.getElementById(type + '-editor');
    const img = document.getElementById(type + '-preview-img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            editor.classList.remove('hidden');
            resetImageTransform(type);
        }
        reader.readAsDataURL(file);
    } else {
        editor.classList.add('hidden');
    }
}

function updateImageTransform(type) {
    const img = document.getElementById(type + '-preview-img');
    const zoom = document.getElementById(type + '-zoom').value;
    const rotation = document.getElementById(type + '-rotation').value;
    
    // Update display values
    document.getElementById(type + '-zoom-value').textContent = zoom + '%';
    document.getElementById(type + '-rotation-value').textContent = rotation + '°';
    
    // Apply transform
    img.style.transform = `scale(${zoom / 100}) rotate(${rotation}deg)`;
}

function resetImageTransform(type) {
    document.getElementById(type + '-zoom').value = 100;
    document.getElementById(type + '-rotation').value = 0;
    updateImageTransform(type);
}
</script>
@endpush
