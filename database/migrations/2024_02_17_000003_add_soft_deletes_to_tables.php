<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes to estatus
        if (!Schema::hasColumn('estatus', 'deleted_at')) {
            Schema::table('estatus', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to autoridades
        if (!Schema::hasColumn('autoridades', 'deleted_at')) {
            Schema::table('autoridades', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to cursos
        if (!Schema::hasColumn('cursos', 'deleted_at')) {
            Schema::table('cursos', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to modulos
        if (!Schema::hasColumn('modulos', 'deleted_at')) {
            Schema::table('modulos', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to clases
        if (!Schema::hasColumn('clases', 'deleted_at')) {
            Schema::table('clases', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to alumnos
        if (!Schema::hasColumn('alumnos', 'deleted_at')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to cohortes
        if (!Schema::hasColumn('cohortes', 'deleted_at')) {
            Schema::table('cohortes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to certificados
        if (!Schema::hasColumn('certificados', 'deleted_at')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to configuraciones
        if (!Schema::hasColumn('configuraciones', 'deleted_at')) {
            Schema::table('configuraciones', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('estatus', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('autoridades', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('modulos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('clases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cohortes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
