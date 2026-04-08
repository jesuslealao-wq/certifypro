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
        Schema::create('estatus', function (Blueprint $table) {
            $table->id();
            $table->string('entidad'); // curso, cohorte, inscripcion, certificado
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('orden_visual')->default(0);
    
            $table->unique(['entidad', 'nombre']);
            $table->index(['entidad', 'orden_visual']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatus');
    }
};
