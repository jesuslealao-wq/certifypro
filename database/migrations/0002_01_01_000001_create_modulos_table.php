<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->string('titulo_modulo');
            $table->unsignedInteger('orden')->default(1);
        });
    }

public function down(): void
{
    Schema::dropIfExists('modulos');
}
};
