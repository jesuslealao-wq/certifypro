<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('cohorte_id')->constrained('cohortes')->cascadeOnDelete();

            $table->string('libro');
            $table->string('folio');
            $table->string('codigo_registro_manual')->nullable();

            $table->string('codigo_verificacion_app')->unique();
            $table->uuid('uuid_seguridad')->nullable()->unique();

            $table->date('fecha_emision');
            $table->foreignId('estatus_id')->constrained('estatus')->restrictOnDelete();

            $table->foreignId('firma_1_id')->nullable()->constrained('autoridades')->nullOnDelete();
            $table->foreignId('firma_2_id')->nullable()->constrained('autoridades')->nullOnDelete();
            $table->foreignId('firma_3_id')->nullable()->constrained('autoridades')->nullOnDelete();

            $table->unique(['alumno_id', 'cohorte_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
