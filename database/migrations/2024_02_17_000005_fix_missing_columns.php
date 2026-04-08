<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix modulos - add horas_modulo
        if (!Schema::hasColumn('modulos', 'horas_modulo')) {
            Schema::table('modulos', function (Blueprint $table) {
                $table->integer('horas_modulo')->nullable()->after('titulo_modulo');
            });
        }

        // Fix cohortes - rename codigo_promocion to codigo_cohorte
        if (Schema::hasColumn('cohortes', 'codigo_promocion') && !Schema::hasColumn('cohortes', 'codigo_cohorte')) {
            Schema::table('cohortes', function (Blueprint $table) {
                $table->renameColumn('codigo_promocion', 'codigo_cohorte');
            });
        }

        // Fix cohortes - add modalidad
        if (!Schema::hasColumn('cohortes', 'modalidad')) {
            Schema::table('cohortes', function (Blueprint $table) {
                $table->string('modalidad')->nullable()->after('codigo_cohorte');
            });
        }

        // Fix certificados - add estado (string)
        if (!Schema::hasColumn('certificados', 'estado')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->string('estado')->default('valido')->after('estado_id');
            });
        }

        // Fix certificados - add creado_por_usuario_id
        if (!Schema::hasColumn('certificados', 'creado_por_usuario_id')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->unsignedBigInteger('creado_por_usuario_id')->nullable()->after('estado');
            });
        }

        // Fix certificados - add temario_snapshot
        if (!Schema::hasColumn('certificados', 'temario_snapshot')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->json('temario_snapshot')->nullable()->after('firma_3_id');
            });
        }

        // Fix certificados - add pdf_path
        if (!Schema::hasColumn('certificados', 'pdf_path')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->string('pdf_path')->nullable()->after('temario_snapshot');
            });
        }

        // Fix certificados - add qr_path
        if (!Schema::hasColumn('certificados', 'qr_path')) {
            Schema::table('certificados', function (Blueprint $table) {
                $table->string('qr_path')->nullable()->after('pdf_path');
            });
        }
    }

    public function down(): void
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->dropColumn('horas_modulo');
        });

        Schema::table('cohortes', function (Blueprint $table) {
            $table->renameColumn('codigo_cohorte', 'codigo_promocion');
            $table->dropColumn('modalidad');
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->dropColumn([
                'estado',
                'creado_por_usuario_id',
                'temario_snapshot',
                'pdf_path',
                'qr_path'
            ]);
        });
    }
};
