# Sistema de Papelera (Trash/Recycle Bin)

## Descripción General

El sistema de papelera permite recuperar elementos eliminados mediante SoftDelete en todos los módulos del sistema. Los elementos eliminados no se borran físicamente de la base de datos, sino que se marcan con una fecha de eliminación (`deleted_at`).

---

## Módulos con Papelera

Todos los módulos principales tienen papelera implementada:

- ✅ **Cursos** - `/cursos/papelera/index`
- ✅ **Módulos** - `/modulos/papelera/index`
- ✅ **Clases** - `/clases/papelera/index`
- ✅ **Estatus** - `/estatus/papelera/index`
- ✅ **Autoridades** - `/autoridades/papelera/index`
- ✅ **Alumnos** - `/alumnos/papelera/index`
- ✅ **Cohortes** - `/cohortes/papelera/index`
- ✅ **Certificados** - `/certificados/papelera/index`

---

## Características

### 1. **Eliminación Suave (SoftDelete)**
Cuando eliminas un elemento:
- ❌ NO se borra de la base de datos
- ✅ Se marca con `deleted_at = fecha actual`
- ✅ Desaparece de listados normales
- ✅ Puede ser restaurado en cualquier momento

### 2. **Vista de Papelera**
Cada módulo tiene su propia papelera que muestra:
- Lista de elementos eliminados
- Fecha y hora de eliminación
- Tiempo transcurrido desde la eliminación
- Botones de acción (Restaurar / Eliminar Definitivamente)

### 3. **Restauración**
- Botón "Restaurar" en cada elemento
- Limpia el campo `deleted_at`
- El elemento vuelve a aparecer en listados normales
- Mensaje de confirmación

### 4. **Eliminación Permanente**
- Botón "Eliminar Definitivamente"
- Confirmación obligatoria
- Borra físicamente el registro de la base de datos
- **Esta acción NO se puede deshacer**

---

## Rutas Implementadas

Para cada módulo existen 3 rutas adicionales:

```php
// Ver papelera
GET /modulo/papelera/index

// Restaurar elemento
POST /modulo/{id}/restore

// Eliminar permanentemente
DELETE /modulo/{id}/force-delete
```

### Ejemplo con Cursos:

```php
Route::get('cursos/papelera/index', [CursoController::class, 'papelera'])
    ->name('cursos.papelera');

Route::post('cursos/{id}/restore', [CursoController::class, 'restore'])
    ->name('cursos.restore');

Route::delete('cursos/{id}/force-delete', [CursoController::class, 'forceDelete'])
    ->name('cursos.forceDelete');
```

---

## Implementación Técnica

### Trait `HasTrash`

Se creó un trait reutilizable que proporciona los métodos necesarios:

```php
namespace App\Traits;

trait HasTrash
{
    // Mostrar elementos en papelera
    public function papelera()
    
    // Restaurar elemento
    public function restore($id)
    
    // Eliminar permanentemente
    public function forceDelete($id)
}
```

### Uso en Controladores

Cada controlador implementa el trait y define 3 métodos:

```php
use App\Traits\HasTrash;

class CursoController extends Controller
{
    use HasTrash;
    
    protected function getModelClass(): string
    {
        return Curso::class;
    }
    
    protected function getViewName(): string
    {
        return 'cursos';
    }
    
    protected function getRouteName(): string
    {
        return 'cursos';
    }
}
```

---

## Acceso a la Papelera

### Desde la Interfaz

En cada vista index hay un botón "Papelera" en la esquina superior derecha:

```blade
<a href="{{ route('cursos.papelera') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg">
    <i data-lucide="trash-2"></i>
    Papelera
</a>
```

### Navegación

```
Lista Principal → Botón "Papelera" → Vista de Papelera
                                    ↓
                            [Restaurar] o [Eliminar Definitivamente]
                                    ↓
                            Volver a Lista Principal
```

---

## Vistas de Papelera

Cada vista de papelera incluye:

1. **Encabezado**
   - Título "Papelera de [Módulo]"
   - Descripción
   - Botón "Volver"

2. **Tabla de Elementos**
   - Información relevante del elemento
   - Fecha de eliminación
   - Tiempo transcurrido
   - Acciones

3. **Estado Vacío**
   - Mensaje cuando no hay elementos eliminados
   - Icono visual

4. **Paginación**
   - 15 elementos por página

---

## Consultas en Base de Datos

### Ver solo elementos eliminados
```php
Curso::onlyTrashed()->get();
```

### Ver todos (incluidos eliminados)
```php
Curso::withTrashed()->get();
```

### Verificar si está eliminado
```php
if ($curso->trashed()) {
    // Está en papelera
}
```

### Restaurar
```php
$curso = Curso::withTrashed()->find($id);
$curso->restore();
```

### Eliminar permanentemente
```php
$curso = Curso::withTrashed()->find($id);
$curso->forceDelete();
```

---

## Consideraciones Importantes

### ⚠️ Relaciones

Cuando eliminas un elemento padre:
- Los hijos NO se eliminan automáticamente
- Las relaciones pueden quedar "huérfanas"
- Las vistas manejan relaciones null correctamente

Ejemplo:
```blade
@if($clase->modulo)
    {{ $clase->modulo->titulo_modulo }}
@else
    <span class="text-slate-400">Módulo eliminado</span>
@endif
```

### 🔒 Seguridad

- La eliminación permanente requiere confirmación
- Los elementos eliminados NO aparecen en búsquedas normales
- Solo usuarios autorizados deberían acceder a la papelera

### 📊 Performance

- Índice en `deleted_at` mejora consultas
- Considera limpieza periódica de elementos antiguos
- Monitorea el tamaño de la papelera

---

## Limpieza Automática (Opcional)

Para implementar limpieza automática de elementos antiguos:

### Crear Comando Artisan

```bash
php artisan make:command LimpiarPapelera
```

```php
// app/Console/Commands/LimpiarPapelera.php
public function handle()
{
    $dias = 30; // Eliminar después de 30 días
    
    $modelos = [
        Curso::class,
        Modulo::class,
        Clase::class,
        // ... otros modelos
    ];
    
    foreach ($modelos as $modelo) {
        $eliminados = $modelo::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays($dias))
            ->forceDelete();
            
        $this->info("Eliminados {$eliminados} registros de " . class_basename($modelo));
    }
}
```

### Programar en Cron

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('papelera:limpiar')
        ->weekly()
        ->sundays()
        ->at('02:00');
}
```

---

## Mensajes del Sistema

### Eliminación Normal
```
"Curso eliminado exitosamente."
```

### Restauración
```
"Elemento restaurado exitosamente."
```

### Eliminación Permanente
```
"Elemento eliminado permanentemente."
```

---

## Testing

### Probar Eliminación
```php
$curso = Curso::find(1);
$curso->delete();

$this->assertTrue($curso->trashed());
```

### Probar Restauración
```php
$curso = Curso::withTrashed()->find(1);
$curso->restore();

$this->assertFalse($curso->trashed());
```

### Probar Eliminación Permanente
```php
$curso = Curso::withTrashed()->find(1);
$curso->forceDelete();

$this->assertNull(Curso::withTrashed()->find(1));
```

---

## Beneficios del Sistema

✅ **Recuperación de errores** - Restaura elementos eliminados por error  
✅ **Auditoría** - Mantiene historial de eliminaciones  
✅ **Seguridad** - Previene pérdida accidental de datos  
✅ **Flexibilidad** - Decide cuándo eliminar permanentemente  
✅ **Trazabilidad** - Sabe cuándo se eliminó cada elemento  

---

## Resumen de Archivos Creados

### Trait
- `app/Traits/HasTrash.php`

### Rutas
- Agregadas en `routes/web.php`

### Controladores
- Métodos agregados a todos los controllers

### Vistas (8 archivos)
- `resources/views/cursos/papelera.blade.php`
- `resources/views/modulos/papelera.blade.php`
- `resources/views/clases/papelera.blade.php`
- `resources/views/estatus/papelera.blade.php`
- `resources/views/autoridades/papelera.blade.php`
- `resources/views/alumnos/papelera.blade.php`
- `resources/views/cohortes/papelera.blade.php`
- `resources/views/certificados/papelera.blade.php`

---

**Versión:** 1.0  
**Fecha:** Febrero 2026  
**Estado:** ✅ Completamente Implementado
