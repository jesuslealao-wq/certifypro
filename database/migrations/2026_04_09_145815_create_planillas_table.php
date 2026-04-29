<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Se guarda una copia de la estructura base (HTML/CSS) para mantener consistencia
            // y permitir que la planilla siga funcionando aunque cambie el archivo base.
            $table->longText('estructura_html');
            $table->longText('estilos_css')->nullable();

            // Fondos por página: se seleccionan de los archivos existentes en la carpeta planilla/
            // Se almacena el nombre del archivo (o ruta relativa) para leerlo desde el disco al generar PDF.
            $table->string('fondo_pagina_1')->nullable();
            $table->string('fondo_pagina_2')->nullable();

            $table->boolean('activa')->default(true);
            $table->boolean('es_predeterminada')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planillas');
    }
};
