<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('alumno_cohorte')) {
            return;
        }

        if (Schema::hasColumn('alumno_cohorte', 'fecha_inscripcion')) {
            return;
        }

        Schema::table('alumno_cohorte', function (Blueprint $table) {
            $table->date('fecha_inscripcion')->nullable()->after('cohorte_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('alumno_cohorte')) {
            return;
        }

        if (!Schema::hasColumn('alumno_cohorte', 'fecha_inscripcion')) {
            return;
        }

        Schema::table('alumno_cohorte', function (Blueprint $table) {
            $table->dropColumn('fecha_inscripcion');
        });
    }
};
