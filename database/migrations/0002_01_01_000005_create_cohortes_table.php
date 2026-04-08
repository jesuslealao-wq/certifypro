<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohortes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('curso_id')->constrained('cursos')->restrictOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('autoridades')->nullOnDelete();

            $table->foreignId('estatus_id')->constrained('estatus')->restrictOnDelete();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('codigo_promocion')->nullable();

            $table->foreignId('firma_default_1_id')->nullable()->constrained('autoridades')->nullOnDelete();
            $table->foreignId('firma_default_2_id')->nullable()->constrained('autoridades')->nullOnDelete();
            $table->foreignId('firma_default_3_id')->nullable()->constrained('autoridades')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohortes');
    }
};
