@extends('layouts.app')

@section('page-title', 'Editor de Plantilla')
@section('page-description', $plantillas_pdf->nombre)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
<style>
    .CodeMirror { height: 100%; font-size: 13px; border-radius: 0 0 8px 8px; }
    .editor-tabs button.active { background: #282a36; color: #f8f8f2; }
    .editor-tabs button { background: #44475a; color: #6272a4; }
    .a4-page {
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .a4-landscape { width: 297mm; height: 210mm; }
    .a4-portrait { width: 210mm; height: 297mm; }
    #preview-wrapper {
        transform-origin: top left;
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<form id="plantilla-form" action="{{ route('plantillas-pdf.update', $plantillas_pdf) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Barra superior --}}
    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <a href="{{ route('plantillas-pdf.index') }}" class="text-slate-400 hover:text-slate-600 shrink-0">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <input type="text" name="nombre" value="{{ $plantillas_pdf->nombre }}" class="text-lg font-semibold bg-transparent border-b border-transparent hover:border-slate-300 focus:border-blue-500 focus:outline-none px-1 py-0.5 flex-1 min-w-0">
            <input type="hidden" name="descripcion" value="{{ $plantillas_pdf->descripcion }}">
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <select name="orientacion" id="sel-orientacion" class="text-sm border border-slate-300 rounded px-2 py-1">
                <option value="landscape" {{ $plantillas_pdf->orientacion == 'landscape' ? 'selected' : '' }}>Landscape</option>
                <option value="portrait" {{ $plantillas_pdf->orientacion == 'portrait' ? 'selected' : '' }}>Portrait</option>
            </select>
            <select name="tamano_papel" class="text-sm border border-slate-300 rounded px-2 py-1">
                <option value="a4" {{ $plantillas_pdf->tamano_papel == 'a4' ? 'selected' : '' }}>A4</option>
                <option value="letter" {{ $plantillas_pdf->tamano_papel == 'letter' ? 'selected' : '' }}>Letter</option>
            </select>
            <a href="{{ route('plantillas-pdf.preview', $plantillas_pdf) }}" target="_blank" class="bg-purple-600 text-white px-3 py-1.5 rounded text-sm hover:bg-purple-700 transition">Preview</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700 transition font-semibold">Guardar</button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex gap-4" style="height: calc(100vh - 200px);">
        {{-- Panel izquierdo: Editor de codigo --}}
        <div class="w-1/2 flex flex-col bg-white rounded-lg shadow overflow-hidden">
            {{-- Tabs --}}
            <div class="editor-tabs flex border-b border-slate-200">
                <button type="button" onclick="switchEditorTab('html')" id="tab-html" class="px-4 py-2 text-sm font-semibold active">HTML</button>
                <button type="button" onclick="switchEditorTab('css')" id="tab-css" class="px-4 py-2 text-sm font-semibold">CSS</button>
                <button type="button" onclick="switchEditorTab('variables')" id="tab-variables" class="px-4 py-2 text-sm font-semibold">Variables</button>
                <button type="button" onclick="switchEditorTab('fondos')" id="tab-fondos" class="px-4 py-2 text-sm font-semibold">Fondos</button>
            </div>

            {{-- Editor HTML --}}
            <div id="panel-html" class="flex-1 relative">
                <textarea name="contenido_html" id="editor-html" class="hidden">{{ $plantillas_pdf->contenido_html }}</textarea>
            </div>

            {{-- Editor CSS --}}
            <div id="panel-css" class="flex-1 relative hidden">
                <textarea name="estilos_css" id="editor-css" class="hidden">{{ $plantillas_pdf->estilos_css }}</textarea>
            </div>

            {{-- Panel Fondos --}}
            <div id="panel-fondos" class="flex-1 relative hidden overflow-y-auto p-4">
                <p class="text-sm text-slate-600 mb-4">Sube una imagen de fondo para cada pagina. Formatos: JPG, PNG, WebP. Max 5MB.</p>

                @foreach([1, 2] as $numPag)
                <div class="mb-5 bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <h4 class="text-sm font-semibold text-slate-700 mb-3">Pagina {{ $numPag }}</h4>
                    @php $campoFondo = 'fondo_pagina_' . $numPag; @endphp
                    <div id="fondo-preview-{{ $numPag }}" class="mb-3 {{ $plantillas_pdf->$campoFondo ? '' : 'hidden' }}">
                        <img id="fondo-img-{{ $numPag }}" src="{{ $plantillas_pdf->$campoFondo ? asset('storage/' . $plantillas_pdf->$campoFondo) : '' }}" class="w-full max-h-32 object-contain rounded border bg-white">
                        <div class="flex items-center justify-between mt-2">
                            <span id="fondo-info-{{ $numPag }}" class="text-xs text-slate-500"></span>
                            <button type="button" onclick="removeFondo({{ $numPag }})" class="text-xs text-red-500 hover:text-red-700">Eliminar fondo</button>
                        </div>
                    </div>
                    <div id="fondo-upload-{{ $numPag }}" class="{{ $plantillas_pdf->$campoFondo ? 'hidden' : '' }}">
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:bg-slate-100 transition">
                            <i data-lucide="upload" class="w-6 h-6 text-slate-400 mb-1"></i>
                            <span class="text-xs text-slate-500">Click para subir imagen</span>
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handleFondoUpload(this, {{ $numPag }})">
                        </label>
                    </div>
                </div>
                @endforeach

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
                    <strong>Dimensiones recomendadas:</strong><br>
                    <span id="dims-recomendadas">Landscape A4: 3508 x 2480 px (300 DPI) o 1754 x 1240 px (150 DPI)</span>
                </div>
            </div>

            {{-- Panel Variables --}}
            <div id="panel-variables" class="flex-1 relative hidden overflow-y-auto p-4">
                <p class="text-sm text-slate-600 mb-3">Usa estas variables en el HTML. Se reemplazaran con datos reales al generar el PDF.</p>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="pb-2 text-slate-500 font-medium">Variable</th>
                            <th class="pb-2 text-slate-500 font-medium">Descripcion</th>
                            <th class="pb-2 text-slate-500 font-medium">Ejemplo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($variables as $key => $desc)
                            @php $varTag = '{{ $' . $key . ' }}'; @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50 cursor-pointer" onclick="insertVariable('{{ $key }}')">
                                <td class="py-2 font-mono text-xs"><code class="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">{{ $varTag }}</code></td>
                                <td class="py-2 text-slate-600">{{ $desc }}</td>
                                <td class="py-2 text-slate-400 text-xs">{{ $datosPrueba[$key] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel derecho: Preview A4 --}}
        <div class="w-1/2 bg-slate-200 rounded-lg overflow-auto p-6" id="preview-scroll">
            <div id="preview-wrapper">
                {{-- Las paginas A4 se renderizaran aqui via JS --}}
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script>
// Datos de prueba para el preview
const datosPrueba = @json($datosPrueba);
const uploadUrl = '{{ route('plantillas-pdf.upload-fondo', $plantillas_pdf) }}';
const removeUrl = '{{ route('plantillas-pdf.remove-fondo', $plantillas_pdf) }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let fondoUrls = {
    1: '{{ $plantillas_pdf->fondo_pagina_1 ? asset('storage/' . $plantillas_pdf->fondo_pagina_1) : '' }}',
    2: '{{ $plantillas_pdf->fondo_pagina_2 ? asset('storage/' . $plantillas_pdf->fondo_pagina_2) : '' }}',
};

// Inicializar CodeMirror para HTML
const cmHtml = CodeMirror.fromTextArea(document.getElementById('editor-html'), {
    mode: 'htmlmixed',
    theme: 'dracula',
    lineNumbers: true,
    lineWrapping: true,
    tabSize: 2,
    indentWithTabs: false,
    autoCloseTags: true,
    matchBrackets: true,
});

// Inicializar CodeMirror para CSS
const cmCss = CodeMirror.fromTextArea(document.getElementById('editor-css'), {
    mode: 'css',
    theme: 'dracula',
    lineNumbers: true,
    lineWrapping: true,
    tabSize: 2,
    indentWithTabs: false,
    matchBrackets: true,
});

// Tabs
function switchEditorTab(tab) {
    document.querySelectorAll('[id^="panel-"]').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.editor-tabs button').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('active');
    if (tab === 'html') cmHtml.refresh();
    if (tab === 'css') cmCss.refresh();
}

// Insertar variable en el editor HTML
function insertVariable(key) {
    const text = '{' + '{ $' + key + ' }' + '}';
    cmHtml.replaceSelection(text);
    switchEditorTab('html');
    cmHtml.focus();
}

// Preview en vivo
let previewTimeout;
function updatePreview() {
    clearTimeout(previewTimeout);
    previewTimeout = setTimeout(renderPreview, 400);
}

function renderPreview() {
    let html = cmHtml.getValue();
    let css = cmCss.getValue();

    // Reemplazar variables con datos de prueba
    for (const [key, value] of Object.entries(datosPrueba)) {
        html = html.replaceAll('{' + '{ $' + key + ' }' + '}', value);
        html = html.replaceAll('{' + '{$' + key + '}' + '}', value);
    }

    const orientacion = document.getElementById('sel-orientacion').value;
    const isLandscape = orientacion === 'landscape';
    const pageW = isLandscape ? '297mm' : '210mm';
    const pageH = isLandscape ? '210mm' : '297mm';

    const wrapper = document.getElementById('preview-wrapper');
    const container = document.getElementById('preview-scroll');

    // Calcular escala para que quepa en el panel
    const containerW = container.clientWidth - 48; // padding
    const pageWpx = isLandscape ? 1123 : 794; // 297mm o 210mm en px a 96dpi
    const scale = Math.min(containerW / pageWpx, 1);

    wrapper.style.transform = 'scale(' + scale + ')';
    wrapper.style.width = pageWpx + 'px';

    // Construir el contenido completo
    const fullHtml = `
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Times New Roman', serif; }
            .page-break { page-break-after: always; }
            ${css}
        </style>
        ${html}
    `;

    // Separar paginas por .page-break o por divs de nivel superior
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = fullHtml;

    // Buscar elementos con clase page-break
    const styleTag = tempDiv.querySelector('style');
    const styleContent = styleTag ? styleTag.outerHTML : '';
    if (styleTag) styleTag.remove();

    const children = Array.from(tempDiv.children);
    let pages = [];

    if (children.length === 0) {
        pages.push(tempDiv.innerHTML);
    } else {
        let currentPage = '';
        children.forEach(child => {
            currentPage += child.outerHTML;
            if (child.classList && child.classList.contains('page-break')) {
                pages.push(currentPage);
                currentPage = '';
            }
        });
        if (currentPage.trim()) {
            pages.push(currentPage);
        }
    }

    // Si solo hay una "pagina" pero sin separador, mostrarla como una sola
    if (pages.length === 0) pages.push('');

    // Renderizar paginas con fondos
    let pagesHtml = '';
    pages.forEach((pageContent, i) => {
        const pageNum = i + 1;
        const bgUrl = fondoUrls[pageNum] || '';
        const bgStyle = bgUrl ? `background-image:url('${bgUrl}'); background-size:cover; background-position:center; background-repeat:no-repeat;` : '';
        pagesHtml += `
            <div class="a4-page" style="width:${pageW}; height:${pageH}; position:relative; overflow:hidden; ${bgStyle}">
                ${styleContent}
                ${pageContent}
            </div>
        `;
    });

    wrapper.innerHTML = pagesHtml;
}

// Escuchar cambios
cmHtml.on('change', updatePreview);
cmCss.on('change', updatePreview);
document.getElementById('sel-orientacion').addEventListener('change', renderPreview);

// Sincronizar CodeMirror con textarea antes de submit
document.getElementById('plantilla-form').addEventListener('submit', function() {
    cmHtml.save();
    cmCss.save();
});

// === FONDOS ===
function handleFondoUpload(input, pagina) {
    const file = input.files[0];
    if (!file) return;

    // Validar tipo
    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        alert('Solo se permiten archivos JPG, PNG o WebP.');
        input.value = '';
        return;
    }

    // Validar tamano
    if (file.size > 5 * 1024 * 1024) {
        alert('La imagen no debe superar 5MB.');
        input.value = '';
        return;
    }

    // Verificar dimensiones
    const img = new Image();
    img.onload = function() {
        const w = img.width;
        const h = img.height;
        const orientacion = document.getElementById('sel-orientacion').value;
        const isLandscape = orientacion === 'landscape';

        // Dimensiones esperadas para A4
        const expectedRatio = isLandscape ? (297 / 210) : (210 / 297);
        const actualRatio = w / h;
        const ratioDiff = Math.abs(actualRatio - expectedRatio) / expectedRatio;

        let proceed = true;
        if (ratioDiff > 0.08) {
            proceed = confirm(
                'La imagen tiene dimensiones ' + w + ' x ' + h + ' px (ratio ' + actualRatio.toFixed(2) + ').\n' +
                'Para ' + (isLandscape ? 'Landscape' : 'Portrait') + ' A4, el ratio ideal es ~' + expectedRatio.toFixed(2) + '.\n\n' +
                'La imagen podria verse distorsionada. ¿Deseas continuar?'
            );
        }

        if (w < 800 || h < 500) {
            proceed = confirm(
                'La imagen tiene baja resolucion (' + w + ' x ' + h + ' px).\n' +
                'Se recomienda minimo 1754 x 1240 px para buena calidad.\n\n' +
                '¿Deseas continuar de todos modos?'
            );
        }

        if (proceed) {
            subirFondo(file, pagina, w, h);
        } else {
            input.value = '';
        }
        URL.revokeObjectURL(img.src);
    };
    img.src = URL.createObjectURL(file);
}

function subirFondo(file, pagina, w, h) {
    const formData = new FormData();
    formData.append('imagen', file);
    formData.append('pagina', pagina);
    formData.append('_token', csrfToken);

    fetch(uploadUrl, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fondoUrls[pagina] = data.url;
                document.getElementById('fondo-img-' + pagina).src = data.url;
                document.getElementById('fondo-info-' + pagina).textContent = w + ' x ' + h + ' px';
                document.getElementById('fondo-preview-' + pagina).classList.remove('hidden');
                document.getElementById('fondo-upload-' + pagina).classList.add('hidden');
                renderPreview();
            } else {
                alert('Error al subir la imagen.');
            }
        })
        .catch(() => alert('Error de conexion al subir la imagen.'));
}

function removeFondo(pagina) {
    if (!confirm('¿Eliminar el fondo de la pagina ' + pagina + '?')) return;

    const formData = new FormData();
    formData.append('pagina', pagina);
    formData.append('_token', csrfToken);

    fetch(removeUrl, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fondoUrls[pagina] = '';
                document.getElementById('fondo-preview-' + pagina).classList.add('hidden');
                document.getElementById('fondo-upload-' + pagina).classList.remove('hidden');
                renderPreview();
            }
        });
}

// Render inicial
setTimeout(renderPreview, 200);
</script>
@endpush
