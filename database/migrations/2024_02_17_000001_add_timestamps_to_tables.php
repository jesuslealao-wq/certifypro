<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add timestamps to estatus
        if (!Schema::hasColumn('estatus', 'created_at')) {
            Schema::table('estatus', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to autoridades
        if (!Schema::hasColumn('autoridades', 'created_at')) {
            Schema::table('autoridades', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to cursos
        if (!Schema::hasColumn('cursos', 'created_at')) {
            Schema::table('cursos', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to modulos
        if (!Schema::hasColumn('modulos', 'created_at')) {
            Schema::table('modulos', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to clases
        if (!Schema::hasColumn('clases', 'created_at')) {
            Schema::table('clases', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to alumnos
        if (!Schema::hasColumn('alumnos', 'created_at')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to cohortes
        if (!Schema::hasColumn('cohortes', 'created_at')) {
            Schema::table('cohortes', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add timestamps to certificados
        if (!Schema::hasColumn('certificados', 'created_at')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('estatus', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('autoridades', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('modulos', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('clases', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('cohortes', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
