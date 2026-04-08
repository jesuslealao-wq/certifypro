<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autoridades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('cargo');
            $table->string('especialidad')->nullable();
            $table->string('firma_path')->nullable();
            $table->string('sello_path')->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autoridades');
    }
};
