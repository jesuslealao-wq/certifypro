<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique()->comment('Identificador único de la configuración');
            $table->text('valor')->nullable()->comment('Valor de la configuración');
            $table->string('tipo')->default('string')->comment('Tipo de dato: string, integer, boolean, json');
            $table->string('grupo')->nullable()->comment('Grupo al que pertenece: general, cursos, certificados, etc');
            $table->string('descripcion')->nullable()->comment('Descripción de la configuración');
            $table->timestamps();
        });

        // Insertar configuraciones por defecto
        DB::table('configuraciones')->insert([
            [
                'clave' => 'estado_default_curso',
                'valor' => '1',
                'tipo' => 'integer',
                'grupo' => 'cursos',
                'descripcion' => 'Estado por defecto al crear un curso',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'estado_default_cohorte',
                'valor' => '1',
                'tipo' => 'integer',
                'grupo' => 'cohortes',
                'descripcion' => 'Estado por defecto al crear una cohorte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'estado_default_certificado',
                'valor' => '1',
                'tipo' => 'integer',
                'grupo' => 'certificados',
                'descripcion' => 'Estado por defecto al crear un certificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'certificado_estado_valido',
                'valor' => 'valido',
                'tipo' => 'string',
                'grupo' => 'certificados',
                'descripcion' => 'Estado por defecto de validez del certificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
