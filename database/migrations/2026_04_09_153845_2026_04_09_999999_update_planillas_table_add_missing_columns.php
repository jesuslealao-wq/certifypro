<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('planillas', function (Blueprint $table) {
            // Nota: esta migración existe porque `planillas` ya fue creada y migrada
            // con una estructura mínima. En SQLite solo podemos "add column", por eso
            // mantenemos las columnas antiguas (fondo_pagina1/fondo_pagina2) y agregamos
            // las nuevas que usa el módulo actual.

            if (!Schema::hasColumn('planillas', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }

            if (!Schema::hasColumn('planillas', 'estructura_html')) {
                $table->longText('estructura_html')->nullable()->after('descripcion');
            }

            if (!Schema::hasColumn('planillas', 'estilos_css')) {
                $table->longText('estilos_css')->nullable()->after('estructura_html');
            }

            if (!Schema::hasColumn('planillas', 'fondo_pagina_1')) {
                $table->string('fondo_pagina_1')->nullable()->after('estilos_css');
            }

            if (!Schema::hasColumn('planillas', 'fondo_pagina_2')) {
                $table->string('fondo_pagina_2')->nullable()->after('fondo_pagina_1');
            }

            if (!Schema::hasColumn('planillas', 'activa')) {
                $table->boolean('activa')->default(true)->after('fondo_pagina_2');
            }

            if (!Schema::hasColumn('planillas', 'es_predeterminada')) {
                $table->boolean('es_predeterminada')->default(false)->after('activa');
            }

            if (!Schema::hasColumn('planillas', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Migrar datos desde columnas antiguas si existen (solo si las nuevas están vacías).
        if (Schema::hasColumn('planillas', 'fondo_pagina1') && Schema::hasColumn('planillas', 'fondo_pagina_1')) {
            DB::statement('UPDATE planillas SET fondo_pagina_1 = COALESCE(fondo_pagina_1, fondo_pagina1)');
        }
        if (Schema::hasColumn('planillas', 'fondo_pagina2') && Schema::hasColumn('planillas', 'fondo_pagina_2')) {
            DB::statement('UPDATE planillas SET fondo_pagina_2 = COALESCE(fondo_pagina_2, fondo_pagina2)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planillas', function (Blueprint $table) {
            // En SQLite el rollback de drops es limitado; hacemos drops donde sea posible.
            if (Schema::hasColumn('planillas', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
            foreach (['es_predeterminada', 'activa', 'fondo_pagina_2', 'fondo_pagina_1', 'estilos_css', 'estructura_html', 'descripcion'] as $col) {
                if (Schema::hasColumn('planillas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
