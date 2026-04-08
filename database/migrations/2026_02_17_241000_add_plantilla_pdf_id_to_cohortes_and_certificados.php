<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migracion: Agregar la columna plantilla_pdf_id a las tablas cohortes y certificados.
 *
 * Establece la relacion entre cohortes/certificados y sus plantillas PDF asignadas.
 * Ambas columnas son nullable porque la asignacion de plantilla es opcional.
 *
 * La restriccion nullOnDelete() garantiza que si se elimina una plantilla PDF,
 * las cohortes y certificados que la tenian asignada simplemente pierden la referencia
 * (se pone null) en lugar de fallar con un error de FK o eliminarse en cascada.
 *
 * Prioridad de plantilla al generar PDF:
 *   1. certificados.plantilla_pdf_id (plantilla individual del certificado)
 *   2. cohortes.plantilla_pdf_id (plantilla asignada a la cohorte)
 *   3. Plantilla marcada como es_predeterminada = true en la tabla plantillas_pdf
 */
return new class extends Migration
{
    /**
     * Agrega plantilla_pdf_id como FK nullable a cohortes y certificados.
     *
     * @return void
     */
    public function up(): void
    {
        // Agregar a cohortes: plantilla por defecto para todos los certificados de la cohorte
        Schema::table('cohortes', function (Blueprint $table) {
            $table->foreignId('plantilla_pdf_id')->nullable()->after('firma_default_3_id')
                ->constrained('plantillas_pdf')->nullOnDelete();
        });

        // Agregar a certificados: plantilla individual que sobreescribe la de la cohorte
        Schema::table('certificados', function (Blueprint $table) {
            $table->foreignId('plantilla_pdf_id')->nullable()->after('qr_path')
                ->constrained('plantillas_pdf')->nullOnDelete();
        });
    }

    /**
     * Elimina la columna plantilla_pdf_id de ambas tablas.
     * El orden es inverso al de creacion para evitar conflictos de FK.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plantilla_pdf_id');
        });

        Schema::table('cohortes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plantilla_pdf_id');
        });
    }
};
