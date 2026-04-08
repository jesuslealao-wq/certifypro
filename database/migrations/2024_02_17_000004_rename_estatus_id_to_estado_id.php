<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar estatus_id a estado_id en cursos
        if (Schema::hasColumn('cursos', 'estatus_id')) {
            Schema::table('cursos', function (Blueprint $table) {
                $table->renameColumn('estatus_id', 'estado_id');
            });
        }

        // Renombrar estatus_id a estado_id en cohortes
        if (Schema::hasColumn('cohortes', 'estatus_id')) {
            Schema::table('cohortes', function (Blueprint $table) {
                $table->renameColumn('estatus_id', 'estado_id');
            });
        }

        // Renombrar estatus_id a estado_id en certificados
        if (Schema::hasColumn('certificados', 'estatus_id')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->renameColumn('estatus_id', 'estado_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->renameColumn('estado_id', 'estatus_id');
        });

        Schema::table('cohortes', function (Blueprint $table) {
            $table->renameColumn('estado_id', 'estatus_id');
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->renameColumn('estado_id', 'estatus_id');
        });
    }
};
