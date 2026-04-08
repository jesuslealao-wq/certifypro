<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->string('titulo_clase');
            $table->unsignedInteger('orden')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clases');
    }
};
