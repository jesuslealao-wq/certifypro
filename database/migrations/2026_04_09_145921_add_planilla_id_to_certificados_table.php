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
        Schema::table('certificados', function (Blueprint $table) {
            $table->foreignId('planilla_id')
                ->nullable()
                ->after('plantilla_pdf_id')
                ->constrained('planillas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('planilla_id');
        });
    }
};
