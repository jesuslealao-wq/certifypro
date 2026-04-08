<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migracion: Crear la tabla plantillas_pdf.
 *
 * Esta tabla almacena las plantillas de diseno HTML/CSS que se usan para
 * generar los certificados en formato PDF. Cada plantilla contiene el HTML
 * del cuerpo del certificado con variables dinamicas (ej: {{ $alumno_nombre }})
 * y los estilos CSS asociados.
 *
 * Las plantillas se editan mediante un editor visual con CodeMirror
 * y se renderizan a PDF con la libreria DomPDF.
 */
return new class extends Migration
{
    /**
     * Crea la tabla plantillas_pdf con todos los campos necesarios.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('plantillas_pdf', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental
            $table->string('nombre'); // Nombre identificador de la plantilla (ej: "Certificado Formal A4")
            $table->text('descripcion')->nullable(); // Descripcion opcional del proposito de la plantilla
            $table->longText('contenido_html'); // HTML completo del certificado con variables {{ $variable }}
            $table->longText('estilos_css')->nullable(); // CSS personalizado para el diseno del certificado
            $table->string('orientacion')->default('landscape'); // Orientacion del papel: 'portrait' o 'landscape'
            $table->string('tamano_papel')->default('a4'); // Tamano del papel: 'a4', 'letter' o 'legal'
            $table->boolean('activa')->default(true); // Si la plantilla esta disponible para ser asignada
            $table->boolean('es_predeterminada')->default(false); // Si es la plantilla por defecto (solo una a la vez)
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at para eliminacion suave (papelera)
        });
    }

    /**
     * Elimina la tabla plantillas_pdf.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_pdf');
    }
};
