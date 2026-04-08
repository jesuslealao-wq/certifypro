<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    use SoftDeletes;

    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'grupo',
        'descripcion',
    ];

    /**
     * Obtener el valor de una configuración
     */
    public static function obtener(string $clave, $default = null)
    {
        return Cache::remember("config_{$clave}", 3600, function () use ($clave, $default) {
            $config = self::where('clave', $clave)->first();
            
            if (!$config) {
                return $default;
            }

            return self::convertirValor($config->valor, $config->tipo);
        });
    }

    /**
     * Establecer el valor de una configuración
     */
    public static function establecer(string $clave, $valor): bool
    {
        $config = self::where('clave', $clave)->first();
        
        if (!$config) {
            return false;
        }

        $config->valor = is_array($valor) ? json_encode($valor) : $valor;
        $config->save();

        Cache::forget("config_{$clave}");
        
        return true;
    }

    /**
     * Convertir el valor según el tipo
     */
    protected static function convertirValor($valor, string $tipo)
    {
        return match($tipo) {
            'integer' => (int) $valor,
            'boolean' => filter_var($valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($valor, true),
            default => $valor,
        };
    }

    /**
     * Obtener todas las configuraciones de un grupo
     */
    public static function porGrupo(string $grupo): array
    {
        $configs = self::where('grupo', $grupo)->get();
        
        $resultado = [];
        foreach ($configs as $config) {
            $resultado[$config->clave] = self::convertirValor($config->valor, $config->tipo);
        }
        
        return $resultado;
    }
}
