@extends('layouts.app')

@section('page-title', 'Nueva Plantilla PDF')
@section('page-description', 'Crear una nueva plantilla para certificados')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('plantillas-pdf.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg" required placeholder="Ej: Certificado Diplomado 2024">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Descripcion</label>
                <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg" placeholder="Descripcion opcional...">{{ old('descripcion') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Orientacion *</label>
                    <select name="orientacion" class="w-full px-4 py-2 border border-slate-300 rounded-lg">
                        <option value="landscape" selected>Horizontal (Landscape)</option>
                        <option value="portrait">Vertical (Portrait)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tamano *</label>
                    <select name="tamano_papel" class="w-full px-4 py-2 border border-slate-300 rounded-lg">
                        <option value="a4" selected>A4</option>
                        <option value="letter">Letter</option>
                        <option value="legal">Legal</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="contenido_html" value="<div class='pagina'><h1>Mi Certificado</h1><p>Contenido de la pagina 1</p></div><div class='pagina'><h1>Pagina 2</h1><p>Contenido de la pagina 2</p></div>">
            <input type="hidden" name="estilos_css" value="">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">Variables disponibles del certificado</label>
                <div class="bg-slate-50 rounded-lg p-3 grid grid-cols-2 gap-1 text-xs font-mono">
                    @foreach($variables as $key => $desc)
                        @php $varTag = '{{ $' . $key . ' }}'; @endphp
                        <div class="flex items-center gap-1 py-0.5">
                            <code class="bg-blue-100 text-blue-800 px-1 rounded">{{ $varTag }}</code>
                            <span class="text-slate-500 truncate">{{ $desc }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Crear y Abrir Editor</button>
                <a href="{{ route('plantillas-pdf.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
