<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migracion: Agregar campos de imagen de fondo a la tabla plantillas_pdf.
 *
 * Permite que cada plantilla tenga una imagen de fondo diferente para la pagina 1
 * y la pagina 2 del certificado. Las imagenes se almacenan en disco
 * (storage/app/public/plantillas-pdf/fondos/) y estos campos guardan la ruta
 * relativa al archivo (ej: 'plantillas-pdf/fondos/abc123.jpg').
 *
 * Al generar el PDF, el servicio CertificadoPdfService lee estas imagenes,
 * las convierte a base64 y las inyecta como background-image en el CSS,
 * porque DomPDF no puede resolver rutas locales de archivos directamente.
 */
return new class extends Migration
{
    /**
     * Agrega los campos fondo_pagina_1 y fondo_pagina_2 despues de estilos_css.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('plantillas_pdf', function (Blueprint $table) {
            $table->string('fondo_pagina_1')->nullable()->after('estilos_css'); // Ruta de la imagen de fondo para la pagina 1
            $table->string('fondo_pagina_2')->nullable()->after('fondo_pagina_1'); // Ruta de la imagen de fondo para la pagina 2
        });
    }

    /**
     * Elimina los campos de fondo de la tabla plantillas_pdf.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('plantillas_pdf', function (Blueprint $table) {
            $table->dropColumn(['fondo_pagina_1', 'fondo_pagina_2']);
        });
    }
};
