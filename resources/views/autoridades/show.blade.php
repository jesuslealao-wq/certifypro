@extends('layouts.app')

@section('page-title', 'Detalle de Autoridad')
@section('page-description', $autoridad->nombre_completo)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $autoridad->nombre_completo }}</h3>
                <p class="text-slate-500 mt-1">{{ $autoridad->cargo }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('autoridades.edit', $autoridad) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
                <a href="{{ route('autoridades.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition">
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-slate-500">Especialidad</p>
                <p class="text-lg font-semibold text-slate-800">{{ $autoridad->especialidad ?? 'No especificada' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Estado</p>
                <span class="px-3 py-1 {{ $autoridad->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-sm font-semibold">
                    {{ $autoridad->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>

        @if($autoridad->sello_path || $autoridad->firma_path)
            <div class="border-t pt-6">
                <h4 class="text-lg font-semibold text-slate-800 mb-4">Sello y Firma</h4>
                
                @if($autoridad->sello_path && $autoridad->firma_path)
                    <!-- Vista Superpuesta -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-sm font-medium text-slate-700">Vista Superpuesta</p>
                            <div class="flex gap-2">
                                <button onclick="toggleLayer('sello')" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                    Toggle Sello
                                </button>
                                <button onclick="toggleLayer('firma')" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">
                                    Toggle Firma
                                </button>
                            </div>
                        </div>
                        
                        <div class="border border-slate-300 rounded-lg p-6 bg-white" style="position: relative; height: 300px; overflow: hidden;">
                            <div id="layer-sello" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1; transition: all 0.3s ease;">
                                <img src="{{ asset('storage/' . $autoridad->sello_path) }}" 
                                    id="overlay-sello-img"
                                    alt="Sello" 
                                    style="max-height: 200px; opacity: 0.9; transition: transform 0.3s ease;">
                            </div>
                            <div id="layer-firma" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2; transition: all 0.3s ease;">
                                <img src="{{ asset('storage/' . $autoridad->firma_path) }}" 
                                    id="overlay-firma-img"
                                    alt="Firma" 
                                    style="max-height: 150px; opacity: 0.9; transition: transform 0.3s ease;">
                            </div>
                        </div>
                        
                        <!-- Controles de Superposición -->
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs font-semibold text-blue-800 mb-2">Sello</p>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-slate-700 w-16">Zoom:</label>
                                        <input type="range" id="overlay-sello-zoom" min="50" max="200" value="100" 
                                            oninput="updateOverlayTransform('sello')"
                                            class="flex-1">
                                        <span id="overlay-sello-zoom-value" class="text-xs text-slate-600 w-12">100%</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-slate-700 w-16">Rotación:</label>
                                        <input type="range" id="overlay-sello-rotation" min="0" max="360" value="0" 
                                            oninput="updateOverlayTransform('sello')"
                                            class="flex-1">
                                        <span id="overlay-sello-rotation-value" class="text-xs text-slate-600 w-12">0°</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-slate-700 w-16">Opacidad:</label>
                                        <input type="range" id="overlay-sello-opacity" min="0" max="100" value="90" 
                                            oninput="updateOverlayTransform('sello')"
                                            class="flex-1">
                                        <span id="overlay-sello-opacity-value" class="text-xs text-slate-600 w-12">90%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-xs font-semibold text-green-800 mb-2">Firma</p>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-slate-700 w-16">Zoom:</label>
                                        <input type="range" id="overlay-firma-zoom" min="50" max="200" value="100" 
                                            oninput="updateOverlayTransform('firma')"
                                            class="flex-1">
                                        <span id="overlay-firma-zoom-value" class="text-xs text-slate-600 w-12">100%</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-slate-700 w-16">Rotación:</label>
                                        <input type="range" id="overlay-firma-rotation" min="0" max="360" value="0" 
                                            oninput="updateOverlayTransform('firma')"
                                            class="flex-1">
                                        <span id="overlay-firma-rotation-value" class="text-xs text-slate-600 w-12">0°</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-slate-700 w-16">Opacidad:</label>
                                        <input type="range" id="overlay-firma-opacity" min="0" max="100" value="90" 
                                            oninput="updateOverlayTransform('firma')"
                                            class="flex-1">
                                        <span id="overlay-firma-opacity-value" class="text-xs text-slate-600 w-12">90%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Vista Individual -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-3">Sello</p>
                        @if($autoridad->sello_path)
                            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50 inline-block">
                                <img src="{{ asset('storage/' . $autoridad->sello_path) }}" 
                                    alt="Sello de {{ $autoridad->nombre_completo }}" 
                                    class="max-h-40 max-w-full">
                            </div>
                        @else
                            <p class="text-slate-400 text-sm">No hay sello cargado</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-3">Firma</p>
                        @if($autoridad->firma_path)
                            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50 inline-block">
                                <img src="{{ asset('storage/' . $autoridad->firma_path) }}" 
                                    alt="Firma de {{ $autoridad->nombre_completo }}" 
                                    class="max-h-40 max-w-full">
                            </div>
                        @else
                            <p class="text-slate-400 text-sm">No hay firma cargada</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateOverlayTransform(type) {
    const img = document.getElementById('overlay-' + type + '-img');
    const zoom = document.getElementById('overlay-' + type + '-zoom').value;
    const rotation = document.getElementById('overlay-' + type + '-rotation').value;
    const opacity = document.getElementById('overlay-' + type + '-opacity').value;
    
    // Update display values
    document.getElementById('overlay-' + type + '-zoom-value').textContent = zoom + '%';
    document.getElementById('overlay-' + type + '-rotation-value').textContent = rotation + '°';
    document.getElementById('overlay-' + type + '-opacity-value').textContent = opacity + '%';
    
    // Apply transform
    img.style.transform = `scale(${zoom / 100}) rotate(${rotation}deg)`;
    img.style.opacity = opacity / 100;
}

function toggleLayer(type) {
    const layer = document.getElementById('layer-' + type);
    if (layer.style.display === 'none') {
        layer.style.display = 'block';
    } else {
        layer.style.display = 'none';
    }
}
</script>
@endpush
