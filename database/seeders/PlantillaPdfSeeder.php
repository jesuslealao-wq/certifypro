<?php

namespace Database\Seeders;

use App\Models\PlantillaPdf;
use Illuminate\Database\Seeder;

class PlantillaPdfSeeder extends Seeder
{
    public function run(): void
    {
        PlantillaPdf::create([
            'nombre' => 'Certificado Profesional - 2 Paginas',
            'descripcion' => 'Plantilla profesional de certificado con 2 paginas A4 landscape. Pagina 1: Certificado principal. Pagina 2: Detalle academico.',
            'orientacion' => 'landscape',
            'tamano_papel' => 'a4',
            'activa' => true,
            'es_predeterminada' => true,
            'estilos_css' => <<<'CSS'
@page {
    margin: 0;
    padding: 0;
}
body {
    margin: 0;
    padding: 0;
    font-family: 'Times New Roman', serif;
    color: #1a1a2e;
}
.page {
    width: 297mm;
    height: 210mm;
    position: relative;
    overflow: hidden;
    background: #ffffff;
}
.page-break {
    page-break-after: always;
}

/* ===== PAGINA 1: CERTIFICADO ===== */
.cert-border {
    position: absolute;
    top: 8mm;
    left: 8mm;
    right: 8mm;
    bottom: 8mm;
    border: 3px solid #1a3a5c;
}
.cert-border-inner {
    position: absolute;
    top: 12mm;
    left: 12mm;
    right: 12mm;
    bottom: 12mm;
    border: 1px solid #b8860b;
}
.cert-corner {
    position: absolute;
    width: 30mm;
    height: 30mm;
    border-color: #b8860b;
}
.cert-corner-tl { top: 14mm; left: 14mm; border-top: 2px solid; border-left: 2px solid; }
.cert-corner-tr { top: 14mm; right: 14mm; border-top: 2px solid; border-right: 2px solid; }
.cert-corner-bl { bottom: 14mm; left: 14mm; border-bottom: 2px solid; border-left: 2px solid; }
.cert-corner-br { bottom: 14mm; right: 14mm; border-bottom: 2px solid; border-right: 2px solid; }

.cert-content {
    position: absolute;
    top: 20mm;
    left: 25mm;
    right: 25mm;
    bottom: 20mm;
    text-align: center;
}
.cert-institution {
    font-size: 14pt;
    font-weight: bold;
    color: #1a3a5c;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-top: 8mm;
}
.cert-subtitle {
    font-size: 10pt;
    color: #666;
    margin-top: 2mm;
    letter-spacing: 1px;
}
.cert-title {
    font-size: 28pt;
    font-weight: bold;
    color: #b8860b;
    margin-top: 12mm;
    letter-spacing: 5px;
    text-transform: uppercase;
}
.cert-otorga {
    font-size: 11pt;
    color: #555;
    margin-top: 10mm;
}
.cert-nombre {
    font-size: 26pt;
    font-weight: bold;
    color: #1a3a5c;
    margin-top: 6mm;
    padding-bottom: 3mm;
    border-bottom: 2px solid #b8860b;
    display: inline-block;
    min-width: 200mm;
}
.cert-cedula {
    font-size: 10pt;
    color: #777;
    margin-top: 3mm;
}
.cert-descripcion {
    font-size: 12pt;
    color: #333;
    margin-top: 8mm;
    line-height: 1.5;
}
.cert-curso {
    font-size: 16pt;
    font-weight: bold;
    color: #1a3a5c;
    margin-top: 3mm;
}
.cert-horas {
    font-size: 10pt;
    color: #666;
    margin-top: 2mm;
}
.cert-footer {
    position: absolute;
    bottom: 25mm;
    left: 25mm;
    right: 25mm;
    text-align: center;
}
.cert-fecha {
    font-size: 11pt;
    color: #555;
    margin-bottom: 5mm;
}
.cert-firmas {
    display: table;
    width: 100%;
    margin-top: 8mm;
}
.cert-firma-col {
    display: table-cell;
    width: 33.33%;
    text-align: center;
    padding: 0 10mm;
    vertical-align: bottom;
}
.cert-firma-linea {
    border-top: 1px solid #333;
    margin: 0 auto;
    width: 50mm;
    padding-top: 2mm;
}
.cert-firma-nombre {
    font-size: 9pt;
    font-weight: bold;
    color: #1a3a5c;
}
.cert-firma-cargo {
    font-size: 8pt;
    color: #777;
}
.cert-codigo {
    position: absolute;
    bottom: 10mm;
    right: 15mm;
    font-size: 7pt;
    color: #aaa;
}

/* ===== PAGINA 2: REVERSO / DETALLE ===== */
.rev-header {
    position: absolute;
    top: 10mm;
    left: 15mm;
    right: 15mm;
    padding-bottom: 5mm;
    border-bottom: 2px solid #1a3a5c;
}
.rev-header-title {
    font-size: 16pt;
    font-weight: bold;
    color: #1a3a5c;
}
.rev-header-sub {
    font-size: 9pt;
    color: #666;
    margin-top: 1mm;
}
.rev-body {
    position: absolute;
    top: 30mm;
    left: 15mm;
    right: 15mm;
    bottom: 25mm;
}
.rev-section {
    margin-bottom: 6mm;
}
.rev-section-title {
    font-size: 11pt;
    font-weight: bold;
    color: #1a3a5c;
    border-bottom: 1px solid #ddd;
    padding-bottom: 2mm;
    margin-bottom: 3mm;
}
.rev-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9pt;
}
.rev-table th {
    background: #f0f4f8;
    color: #1a3a5c;
    font-weight: bold;
    text-align: left;
    padding: 2mm 3mm;
    border: 0.5pt solid #ddd;
}
.rev-table td {
    padding: 2mm 3mm;
    border: 0.5pt solid #ddd;
    color: #333;
}
.rev-info-grid {
    display: table;
    width: 100%;
}
.rev-info-row {
    display: table-row;
}
.rev-info-label {
    display: table-cell;
    font-size: 9pt;
    font-weight: bold;
    color: #1a3a5c;
    padding: 1.5mm 3mm;
    width: 45mm;
    vertical-align: top;
}
.rev-info-value {
    display: table-cell;
    font-size: 9pt;
    color: #333;
    padding: 1.5mm 3mm;
    vertical-align: top;
}
.rev-footer {
    position: absolute;
    bottom: 10mm;
    left: 15mm;
    right: 15mm;
    font-size: 7pt;
    color: #999;
    text-align: center;
    border-top: 1px solid #ddd;
    padding-top: 3mm;
}
CSS,
            'contenido_html' => <<<'HTML'
<!-- ==================== PAGINA 1: CERTIFICADO ==================== -->
<div class="page page-break">
    <div class="cert-border"></div>
    <div class="cert-border-inner"></div>
    <div class="cert-corner cert-corner-tl"></div>
    <div class="cert-corner cert-corner-tr"></div>
    <div class="cert-corner cert-corner-bl"></div>
    <div class="cert-corner cert-corner-br"></div>

    <div class="cert-content">
        <div class="cert-institution">Instituto de Formacion Profesional</div>
        <div class="cert-subtitle">Comprometidos con la excelencia academica</div>

        <div class="cert-title">Certificado</div>

        <div class="cert-otorga">Se otorga el presente certificado a:</div>

        <div class="cert-nombre">{{ $alumno_nombre }}</div>
        <div class="cert-cedula">{{ $alumno_identificacion }}</div>

        <div class="cert-descripcion">
            Por haber aprobado satisfactoriamente el programa de formacion:
        </div>
        <div class="cert-curso">{{ $curso_nombre }}</div>
        <div class="cert-horas">Duracion: {{ $curso_horas }} horas academicas &bull; Modalidad: {{ $cohorte_modalidad }}</div>
    </div>

    <div class="cert-footer">
        <div class="cert-fecha">Emitido el {{ $certificado_fecha_emision }}</div>
        <div class="cert-firmas">
            <div class="cert-firma-col">
                <div class="cert-firma-linea">
                    <div class="cert-firma-nombre">Firma 1</div>
                    <div class="cert-firma-cargo">Director Academico</div>
                </div>
            </div>
            <div class="cert-firma-col">
                <div class="cert-firma-linea">
                    <div class="cert-firma-nombre">Firma 2</div>
                    <div class="cert-firma-cargo">Coordinador de Programa</div>
                </div>
            </div>
            <div class="cert-firma-col">
                <div class="cert-firma-linea">
                    <div class="cert-firma-nombre">Firma 3</div>
                    <div class="cert-firma-cargo">Rector</div>
                </div>
            </div>
        </div>
    </div>

    <div class="cert-codigo">Cod: {{ $certificado_codigo }} | Libro: {{ $certificado_libro }} Folio: {{ $certificado_folio }}</div>
</div>

<!-- ==================== PAGINA 2: REVERSO ==================== -->
<div class="page">
    <div class="rev-header">
        <div class="rev-header-title">Detalle Academico del Certificado</div>
        <div class="rev-header-sub">Documento complementario al certificado {{ $certificado_codigo }}</div>
    </div>

    <div class="rev-body">
        <!-- Datos del participante -->
        <div class="rev-section">
            <div class="rev-section-title">Datos del Participante</div>
            <div class="rev-info-grid">
                <div class="rev-info-row">
                    <div class="rev-info-label">Nombre Completo:</div>
                    <div class="rev-info-value">{{ $alumno_nombre }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Identificacion:</div>
                    <div class="rev-info-value">{{ $alumno_identificacion }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Correo Electronico:</div>
                    <div class="rev-info-value">{{ $alumno_email }}</div>
                </div>
            </div>
        </div>

        <!-- Datos del programa -->
        <div class="rev-section">
            <div class="rev-section-title">Datos del Programa</div>
            <div class="rev-info-grid">
                <div class="rev-info-row">
                    <div class="rev-info-label">Programa:</div>
                    <div class="rev-info-value">{{ $curso_nombre }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Horas Academicas:</div>
                    <div class="rev-info-value">{{ $curso_horas }} horas</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Modalidad:</div>
                    <div class="rev-info-value">{{ $cohorte_modalidad }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Cohorte:</div>
                    <div class="rev-info-value">{{ $cohorte_codigo }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Periodo:</div>
                    <div class="rev-info-value">{{ $cohorte_fecha_inicio }} al {{ $cohorte_fecha_fin }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Descripcion:</div>
                    <div class="rev-info-value">{{ $curso_descripcion }}</div>
                </div>
            </div>
        </div>

        <!-- Datos del certificado -->
        <div class="rev-section">
            <div class="rev-section-title">Datos del Certificado</div>
            <div class="rev-info-grid">
                <div class="rev-info-row">
                    <div class="rev-info-label">Fecha de Emision:</div>
                    <div class="rev-info-value">{{ $certificado_fecha_emision }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Codigo Verificacion:</div>
                    <div class="rev-info-value">{{ $certificado_codigo }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Registro:</div>
                    <div class="rev-info-value">Libro {{ $certificado_libro }}, Folio {{ $certificado_folio }}</div>
                </div>
                <div class="rev-info-row">
                    <div class="rev-info-label">Codigo Manual:</div>
                    <div class="rev-info-value">{{ $certificado_registro }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="rev-footer">
        Este documento es valido con el certificado principal. Verifique su autenticidad con el codigo: {{ $certificado_codigo }}
    </div>
</div>
HTML,
        ]);
    }
}
