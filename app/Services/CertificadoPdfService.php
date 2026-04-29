<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\Planilla;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Servicio central para la generacion de archivos PDF de certificados.
 *
 * Este servicio encapsula toda la logica necesaria para:
 * - Determinar que plantilla PDF debe usarse para un certificado dado.
 * - Extraer los datos reales del certificado y sus relaciones (alumno, curso, cohorte, firmas).
 * - Reemplazar las variables del HTML de la plantilla con los datos extraidos.
 * - Embeber las imagenes de fondo como base64 dentro del CSS para que DomPDF las renderice.
 * - Generar un objeto PDF listo para ser mostrado en el navegador (stream) o descargado.
 *
 * El servicio soporta tanto la generacion de un PDF individual (un solo certificado)
 * como la generacion masiva (multiples certificados combinados en un solo archivo PDF
 * con saltos de pagina entre cada uno).
 *
 * Dependencia principal: barryvdh/laravel-dompdf (Facade Pdf).
 */
class CertificadoPdfService
{
    /**
     * Determina que plantilla PDF debe usarse para generar el certificado.
     *
     * La busqueda sigue un orden de prioridad estricto:
     *   1. Plantilla asignada directamente al certificado (certificado.plantilla_pdf_id).
     *   2. Plantilla asignada a la cohorte del certificado (cohorte.plantilla_pdf_id).
     *   3. Plantilla marcada como predeterminada y activa en el sistema.
     *
     * Si ninguna plantilla se encuentra en ninguno de los tres niveles,
     * el metodo retorna null y el controlador debe manejar ese caso mostrando un error.
     *
     * @param  Certificado  $certificado  El certificado para el cual se busca la plantilla.
     * @return PlantillaPdf|null  La plantilla encontrada, o null si no hay ninguna disponible.
     */
    public function obtenerPlantilla(Certificado $certificado): ?Planilla
    {
        // Prioridad: planilla individual del certificado > predeterminada activa
        if ($certificado->planilla_id) {
            return Planilla::find($certificado->planilla_id);
        }

        return Planilla::where('es_predeterminada', true)->where('activa', true)->first();
    }

    /**
     * Extrae todos los datos del certificado y sus relaciones para sustituir las variables
     * en la plantilla HTML.
     *
     * Este metodo carga las relaciones necesarias (alumno, cohorte con curso, firmas 1/2/3)
     * y construye un array asociativo donde cada clave corresponde a una variable de plantilla
     * (por ejemplo, 'alumno_nombre' se usa como {{ $alumno_nombre }} en el HTML).
     *
     * Si algun dato esta vacio o es null (por ejemplo, el alumno no tiene email,
     * o no se asigno una firma), se usa un valor por defecto definido en el array $defaults.
     * Esto garantiza que el PDF nunca muestre campos en blanco inesperados.
     *
     * El nombre del alumno se convierte a mayusculas con mb_strtoupper para respetar
     * caracteres acentuados y especiales del espanol.
     *
     * @param  Certificado  $certificado  El certificado del cual se extraen los datos.
     * @return array  Array asociativo con clave = nombre_variable y valor = dato real o default.
     */
    public function extraerDatos(Certificado $certificado): array
    {
        $certificado->load('alumno', 'cohorte.curso', 'firma1', 'firma2', 'firma3');

        $defaults = [
            'alumno_nombre' => 'NOMBRE DEL ALUMNO',
            'alumno_identificacion' => '00000000',
            'alumno_email' => 'sin-email@ejemplo.com',
            'curso_nombre' => 'Nombre del Curso',
            'curso_horas' => '0',
            'curso_descripcion' => '',
            'cohorte_codigo' => 'COH-0000',
            'cohorte_modalidad' => 'Presencial',
            'cohorte_fecha_inicio' => date('d/m/Y'),
            'cohorte_fecha_fin' => date('d/m/Y'),
            'certificado_fecha_emision' => date('d/m/Y'),
            'certificado_codigo' => 'SIN-CODIGO',
            'certificado_libro' => '0',
            'certificado_folio' => '0',
            'certificado_registro' => '',
            'firma_1_nombre' => '',
            'firma_1_cargo' => '',
            'firma_2_nombre' => '',
            'firma_2_cargo' => '',
            'firma_3_nombre' => '',
            'firma_3_cargo' => '',
        ];

        $datos = [
            'alumno_nombre' => mb_strtoupper($certificado->alumno->nombre_completo ?? ''),
            'alumno_identificacion' => $certificado->alumno->identificacion_nacional ?? '',
            'alumno_email' => $certificado->alumno->email ?? '',
            'curso_nombre' => $certificado->cohorte->curso->nombre_curso ?? '',
            'curso_horas' => $certificado->cohorte->curso->horas_academicas ?? '',
            'curso_descripcion' => $certificado->cohorte->curso->descripcion ?? '',
            'cohorte_codigo' => $certificado->cohorte->codigo_cohorte ?? '',
            'cohorte_modalidad' => ucfirst($certificado->cohorte->modalidad ?? ''),
            'cohorte_fecha_inicio' => $certificado->cohorte->fecha_inicio ? $certificado->cohorte->fecha_inicio->format('d/m/Y') : '',
            'cohorte_fecha_fin' => $certificado->cohorte->fecha_fin ? $certificado->cohorte->fecha_fin->format('d/m/Y') : '',
            'certificado_fecha_emision' => $certificado->fecha_emision ? $certificado->fecha_emision->format('d/m/Y') : '',
            'certificado_codigo' => $certificado->codigo_verificacion_app ?? '',
            'certificado_libro' => $certificado->libro ?? '',
            'certificado_folio' => $certificado->folio ?? '',
            'certificado_registro' => $certificado->codigo_registro_manual ?? '',
            'firma_1_nombre' => $certificado->firma1->nombre_completo ?? '',
            'firma_1_cargo' => $certificado->firma1->cargo ?? '',
            'firma_2_nombre' => $certificado->firma2->nombre_completo ?? '',
            'firma_2_cargo' => $certificado->firma2->cargo ?? '',
            'firma_3_nombre' => $certificado->firma3->nombre_completo ?? '',
            'firma_3_cargo' => $certificado->firma3->cargo ?? '',
        ];

        // Usar defaults cuando el valor real esta vacio
        foreach ($datos as $key => $value) {
            if ($value === '' || $value === null) {
                $datos[$key] = $defaults[$key];
            }
        }

        return $datos;
    }

    /**
     * Construye el HTML completo listo para ser convertido a PDF por DomPDF.
     *
     * El proceso tiene tres etapas:
     *
     * 1. SUSTITUCION DE VARIABLES: Recorre el array de datos y reemplaza cada
     *    ocurrencia de {{ $nombre_variable }} en el HTML de la plantilla con su valor real.
     *    Soporta dos formatos: con espacios '{{ $var }}' y sin espacios '{{$var}}'.
     *    Despues de la sustitucion, cualquier variable no reconocida que haya quedado
     *    sin reemplazar se elimina con una expresion regular para evitar que aparezcan
     *    etiquetas crudas en el PDF final.
     *
     * 2. FONDOS DE PAGINA: Si la plantilla tiene imagenes de fondo asignadas
     *    (fondo_pagina_1 y/o fondo_pagina_2), se leen los archivos desde el disco,
     *    se convierten a base64 y se inyectan como background-image en el CSS.
     *    Esto es necesario porque DomPDF no puede acceder a archivos locales por ruta,
     *    pero si puede procesar imagenes embebidas en base64.
     *
     * 3. ENSAMBLAJE FINAL: Se construye un documento HTML5 completo con doctype,
     *    head (charset utf-8, estilos CSS del usuario, estilos de fondos, resets basicos)
     *    y body (contenido HTML ya procesado).
     *
     * @param  PlantillaPdf  $plantilla  La plantilla que contiene el HTML y CSS base.
     * @param  array         $datos      Array asociativo de variables ya extraidas del certificado.
     * @return string  El HTML completo listo para ser pasado a DomPDF::loadHTML().
     */
    public function renderizarHtml(Planilla $planilla, array $datos): string
    {
        $html = $planilla->estructura_html ?? '';
        $css = $planilla->estilos_css ?? '';

        // Reemplazar variables
        foreach ($datos as $key => $value) {
            $html = str_replace('{{ $' . $key . ' }}', $value, $html);
            $html = str_replace('{{$' . $key . '}}', $value, $html);
        }

        // Limpiar cualquier variable no sustituida que haya quedado
        $html = preg_replace('/\{\{\s*\$[a-zA-Z_]+\s*\}\}/', '', $html);

        // Construir fondos CSS por pagina
        $fondoCss = '';

        // DomPDF puede fallar con pseudo-selectores (:first-of-type/:nth-of-type).
        // Aplicamos fondos por estilo inline en las 2 páginas para máxima compatibilidad.
        $pageBaseStyle = "background-repeat:no-repeat;background-position:center;background-size:cover;background-color:transparent;";

        if ($planilla->fondo_pagina_1) {
            $fondoPath = $this->resolverRutaFondo($planilla->fondo_pagina_1);
            if ($fondoPath && file_exists($fondoPath)) {
                $base64 = base64_encode(file_get_contents($fondoPath));
                $mime = mime_content_type($fondoPath);
                $dataUri = "data:{$mime};base64,{$base64}";
                $html = preg_replace(
                    '/<div class="page page-break">/i',
                    '<div class="page page-break" style="background-image:url(\'' . $dataUri . '\');' . $pageBaseStyle . '">',
                    $html,
                    1
                ) ?? $html;
            }
        }

        if ($planilla->fondo_pagina_2) {
            $fondoPath = $this->resolverRutaFondo($planilla->fondo_pagina_2);
            if ($fondoPath && file_exists($fondoPath)) {
                $base64 = base64_encode(file_get_contents($fondoPath));
                $mime = mime_content_type($fondoPath);
                $dataUri = "data:{$mime};base64,{$base64}";
                $html = preg_replace(
                    '/<div class="page">/i',
                    '<div class="page" style="background-image:url(\'' . $dataUri . '\');' . $pageBaseStyle . '">',
                    $html,
                    1
                ) ?? $html;
            }
        }

        // Fallback CSS (por si el HTML no coincide exactamente con los patrones)
        $fondoCss .= ".page { background-color: transparent !important; }\n";
        if ($planilla->fondo_pagina_1) {
            $fondoPath = $this->resolverRutaFondo($planilla->fondo_pagina_1);
            if ($fondoPath && file_exists($fondoPath)) {
                $base64 = base64_encode(file_get_contents($fondoPath));
                $mime = mime_content_type($fondoPath);
                $fondoCss .= ".page:first-of-type, .page-break:first-of-type { background-image: url('data:{$mime};base64,{$base64}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-color: transparent; }\n";
            }
        }
        if ($planilla->fondo_pagina_2) {
            $fondoPath = $this->resolverRutaFondo($planilla->fondo_pagina_2);
            if ($fondoPath && file_exists($fondoPath)) {
                $base64 = base64_encode(file_get_contents($fondoPath));
                $mime = mime_content_type($fondoPath);
                $fondoCss .= ".page:nth-of-type(2), .page:last-of-type { background-image: url('data:{$mime};base64,{$base64}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-color: transparent; }\n";
            }
        }

        $fullHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>';
        $fullHtml .= "* { margin: 0; padding: 0; box-sizing: border-box; }\n";
        $fullHtml .= "body { font-family: 'Times New Roman', serif; }\n";
        $fullHtml .= ".page-break { page-break-after: always; }\n";
        $fullHtml .= $css . "\n" . $fondoCss;
        $fullHtml .= '</style></head><body>' . $html . '</body></html>';

        return $fullHtml;
    }

    private function resolverRutaFondo(string $filename): ?string
    {
        $file = basename(str_replace('\\', '/', $filename));
        $candidatos = [
            base_path('planilla' . DIRECTORY_SEPARATOR . $file),
            public_path('planilla' . DIRECTORY_SEPARATOR . $file),
        ];

        foreach ($candidatos as $path) {
            if (is_file($path)) return $path;
        }

        return null;
    }

    /**
     * Genera un archivo PDF para un unico certificado.
     *
     * Orquesta el flujo completo: obtener plantilla, extraer datos, renderizar HTML
     * y crear el objeto PDF con DomPDF. Configura la orientacion (portrait/landscape)
     * y el tamano de papel (a4/letter/legal) segun lo definido en la plantilla.
     *
     * Opciones de DomPDF habilitadas:
     * - isHtml5ParserEnabled: Usa el parser HTML5 para mejor compatibilidad.
     * - isRemoteEnabled: Permite cargar recursos remotos (fuentes, imagenes externas).
     * - defaultFont: Establece Times New Roman como fuente por defecto.
     *
     * @param  Certificado  $certificado  El certificado a convertir en PDF.
     * @return \Barryvdh\DomPDF\PDF|null  El objeto PDF listo para stream() o download(), o null si no hay plantilla.
     */
    public function generarPdf(Certificado $certificado): ?\Barryvdh\DomPDF\PDF
    {
        $plantilla = $this->obtenerPlantilla($certificado);

        if (!$plantilla) {
            return null;
        }

        $datos = $this->extraerDatos($certificado);
        $htmlCompleto = $this->renderizarHtml($plantilla, $datos);

        // Planillas usan la estructura base A4 horizontal
        $orientacion = 'landscape';
        $tamano = 'a4';

        $pdf = Pdf::loadHTML($htmlCompleto)
            ->setPaper($tamano, $orientacion)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'Times New Roman');

        return $pdf;
    }

    /**
     * Genera un unico archivo PDF que contiene multiples certificados.
     *
     * Cada certificado se renderiza individualmente (puede tener su propia plantilla)
     * y se concatena en un solo documento HTML con saltos de pagina entre cada uno
     * usando la propiedad CSS 'page-break-before: always'.
     *
     * La plantilla del primer certificado procesado se usa como referencia para
     * definir la orientacion, tamano de papel y los estilos CSS/fondos del documento.
     * Esto significa que si los certificados de una cohorte usan la misma plantilla
     * (caso mas comun), el PDF resultante sera visualmente consistente.
     *
     * Si un certificado individual no tiene plantilla asignada, se omite silenciosamente
     * y no se incluye en el PDF final. Si ningun certificado tiene plantilla, retorna null.
     *
     * @param  array  $certificados  Array de instancias de Certificado (no Collection, usar ->all()).
     * @return \Barryvdh\DomPDF\PDF|null  El objeto PDF combinado, o null si no hay plantillas disponibles.
     */
    public function generarPdfMultiple(array $certificados): ?\Barryvdh\DomPDF\PDF
    {
        if (empty($certificados)) {
            return null;
        }

        $htmlPartes = [];
        $plantillaRef = null;

        foreach ($certificados as $i => $certificado) {
            $plantilla = $this->obtenerPlantilla($certificado);
            if (!$plantilla) continue;

            if (!$plantillaRef) $plantillaRef = $plantilla;

            $datos = $this->extraerDatos($certificado);
            $html = $plantilla->estructura_html ?? '';
            $css = $plantilla->estilos_css ?? '';

            foreach ($datos as $key => $value) {
                $html = str_replace('{{ $' . $key . ' }}', $value, $html);
                $html = str_replace('{{$' . $key . '}}', $value, $html);
            }

            // Limpiar variables no sustituidas
            $html = preg_replace('/\{\{\s*\$[a-zA-Z_]+\s*\}\}/', '', $html);

            // Envolver cada certificado completo para que inicie en nueva pagina (excepto el primero)
            if ($i > 0) {
                $htmlPartes[] = '<div style="page-break-before: always;"></div>';
            }
            $htmlPartes[] = $html;
        }

        if (!$plantillaRef || empty($htmlPartes)) {
            return null;
        }

        $css = $plantillaRef->estilos_css ?? '';

        // Fondos para la plantilla de referencia
        $fondoCss = '';
        if ($plantillaRef->fondo_pagina_1) {
            $fondoPath = $this->resolverRutaFondo($plantillaRef->fondo_pagina_1);
            if ($fondoPath && file_exists($fondoPath)) {
                $base64 = base64_encode(file_get_contents($fondoPath));
                $mime = mime_content_type($fondoPath);
                $fondoCss .= ".page:first-of-type, .page-break:first-of-type { background-image: url('data:{$mime};base64,{$base64}'); background-size: cover; background-position: center; }\n";
            }
        }
        if ($plantillaRef->fondo_pagina_2) {
            $fondoPath = $this->resolverRutaFondo($plantillaRef->fondo_pagina_2);
            if ($fondoPath && file_exists($fondoPath)) {
                $base64 = base64_encode(file_get_contents($fondoPath));
                $mime = mime_content_type($fondoPath);
                $fondoCss .= ".page:nth-of-type(2) { background-image: url('data:{$mime};base64,{$base64}'); background-size: cover; background-position: center; }\n";
            }
        }

        $fullHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>';
        $fullHtml .= "* { margin: 0; padding: 0; box-sizing: border-box; }\n";
        $fullHtml .= "body { font-family: 'Times New Roman', serif; }\n";
        $fullHtml .= ".page-break { page-break-after: always; }\n";
        $fullHtml .= $css . "\n" . $fondoCss;
        $fullHtml .= '</style></head><body>' . implode("\n", $htmlPartes) . '</body></html>';

        $orientacion = 'landscape';
        $tamano = 'a4';

        $pdf = Pdf::loadHTML($fullHtml)
            ->setPaper($tamano, $orientacion)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'Times New Roman');

        return $pdf;
    }
}
