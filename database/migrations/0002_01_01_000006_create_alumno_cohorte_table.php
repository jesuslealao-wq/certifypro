<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno_cohorte', function (Blueprint $table) {
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('cohorte_id')->constrained('cohortes')->cascadeOnDelete();
            $table->date('fecha_inscripcion')->nullable();
            $table->timestamps();

            $table->primary(['alumno_id', 'cohorte_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_cohorte');
    }
};
