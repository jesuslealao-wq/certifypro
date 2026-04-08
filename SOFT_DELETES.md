# Sistema de Eliminación Lógica (SoftDeletes)

## ¿Qué es SoftDeletes?

El sistema de **SoftDeletes** (eliminación lógica) permite "eliminar" registros sin borrarlos físicamente de la base de datos. En lugar de ejecutar un `DELETE`, Laravel actualiza la columna `deleted_at` con la fecha y hora actual.

## Ventajas

✅ **Recuperación de datos**: Los registros eliminados pueden restaurarse  
✅ **Auditoría**: Mantiene historial completo de eliminaciones  
✅ **Seguridad**: Previene pérdida accidental de datos  
✅ **Trazabilidad**: Permite saber cuándo se eliminó un registro  

---

## Tablas con SoftDeletes

Todas las tablas del sistema tienen SoftDeletes habilitado:

- ✅ `estatus`
- ✅ `cursos`
- ✅ `modulos`
- ✅ `clases`
- ✅ `autoridades`
- ✅ `alumnos`
- ✅ `cohortes`
- ✅ `certificados`
- ✅ `configuraciones`

---

## Cómo Funciona

### Eliminación Normal (Soft Delete)

Cuando eliminas un registro desde la interfaz:

```php
// En el controlador
$curso->delete();
```

**Lo que sucede:**
- NO se elimina el registro de la base de datos
- Se actualiza `deleted_at` con la fecha actual
- El registro ya no aparece en consultas normales

**SQL ejecutado:**
```sql
UPDATE cursos SET deleted_at = '2026-02-17 17:12:00' WHERE id = 1;
```

### Consultas Automáticas

Laravel automáticamente excluye registros eliminados:

```php
// Solo trae registros NO eliminados
Curso::all();
Curso::where('nombre_curso', 'PHP')->get();
```

### Ver Registros Eliminados

```php
// Incluir registros eliminados
Curso::withTrashed()->get();

// Solo registros eliminados
Curso::onlyTrashed()->get();
```

### Restaurar Registros

```php
// Restaurar un registro específico
$curso = Curso::withTrashed()->find(1);
$curso->restore();

// Restaurar múltiples registros
Curso::onlyTrashed()->where('estado_id', 1)->restore();
```

### Eliminación Permanente (Force Delete)

Si necesitas eliminar permanentemente:

```php
// Esto SÍ elimina el registro de la base de datos
$curso->forceDelete();
```

---

## Uso en la Interfaz Web

### Botón de Eliminación

Los botones "Eliminar" en las vistas realizan SoftDelete automáticamente:

```blade
<form action="{{ route('cursos.destroy', $curso) }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('¿Está seguro?')">
        Eliminar
    </button>
</form>
```

### Ver Registros Eliminados (Papelera)

Para agregar una vista de "papelera" en cualquier controlador:

```php
public function papelera()
{
    $cursos = Curso::onlyTrashed()->paginate(15);
    return view('cursos.papelera', compact('cursos'));
}
```

### Restaurar desde la Interfaz

Agregar ruta y método en el controlador:

```php
// En routes/web.php
Route::post('cursos/{id}/restore', [CursoController::class, 'restore'])->name('cursos.restore');

// En CursoController
public function restore($id)
{
    $curso = Curso::withTrashed()->findOrFail($id);
    $curso->restore();
    
    return redirect()->route('cursos.index')
        ->with('success', 'Curso restaurado exitosamente.');
}
```

---

## Consultas Útiles

### Verificar si un registro está eliminado

```php
if ($curso->trashed()) {
    echo "Este curso está eliminado";
}
```

### Contar registros eliminados

```php
$eliminados = Curso::onlyTrashed()->count();
```

### Obtener fecha de eliminación

```php
$curso = Curso::withTrashed()->find(1);
echo $curso->deleted_at; // 2026-02-17 17:12:00
```

---

## Relaciones y SoftDeletes

### Comportamiento con Relaciones

Cuando eliminas un registro padre:

```php
$curso->delete(); // SoftDelete del curso
```

**Los registros relacionados NO se eliminan automáticamente** (módulos, clases, etc.)

### Consultar Relaciones con Registros Eliminados

```php
// Incluir módulos eliminados
$curso->modulos()->withTrashed()->get();

// Solo módulos eliminados
$curso->modulos()->onlyTrashed()->get();
```

---

## Base de Datos

### Estructura de la Columna

Cada tabla tiene la columna:

```sql
deleted_at TIMESTAMP NULL DEFAULT NULL
```

**Valores posibles:**
- `NULL` = Registro activo (no eliminado)
- `2026-02-17 17:12:00` = Registro eliminado en esa fecha

### Consulta SQL Directa

```sql
-- Ver todos los registros (incluyendo eliminados)
SELECT * FROM cursos;

-- Ver solo registros activos
SELECT * FROM cursos WHERE deleted_at IS NULL;

-- Ver solo registros eliminados
SELECT * FROM cursos WHERE deleted_at IS NOT NULL;

-- Restaurar un registro
UPDATE cursos SET deleted_at = NULL WHERE id = 1;

-- Eliminar permanentemente
DELETE FROM cursos WHERE id = 1;
```

---

## Ejemplo Completo: Gestión de Papelera

### 1. Agregar Ruta

```php
// routes/web.php
Route::get('cursos/papelera', [CursoController::class, 'papelera'])->name('cursos.papelera');
Route::post('cursos/{id}/restore', [CursoController::class, 'restore'])->name('cursos.restore');
Route::delete('cursos/{id}/force-delete', [CursoController::class, 'forceDelete'])->name('cursos.forceDelete');
```

### 2. Métodos en el Controlador

```php
// CursoController.php
public function papelera()
{
    $cursos = Curso::onlyTrashed()->paginate(15);
    return view('cursos.papelera', compact('cursos'));
}

public function restore($id)
{
    $curso = Curso::withTrashed()->findOrFail($id);
    $curso->restore();
    return redirect()->route('cursos.papelera')->with('success', 'Curso restaurado.');
}

public function forceDelete($id)
{
    $curso = Curso::withTrashed()->findOrFail($id);
    $curso->forceDelete();
    return redirect()->route('cursos.papelera')->with('success', 'Curso eliminado permanentemente.');
}
```

### 3. Vista de Papelera

```blade
@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-lg font-semibold">Papelera de Cursos</h3>
    <a href="{{ route('cursos.index') }}" class="btn-secondary">Volver a Cursos</a>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Eliminado el</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cursos as $curso)
            <tr>
                <td>{{ $curso->nombre_curso }}</td>
                <td>{{ $curso->deleted_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form action="{{ route('cursos.restore', $curso->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600">Restaurar</button>
                    </form>
                    
                    <form action="{{ route('cursos.forceDelete', $curso->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600" onclick="return confirm('¿Eliminar permanentemente?')">
                            Eliminar Definitivamente
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

---

## Consideraciones Importantes

### ⚠️ Claves Foráneas

Si tienes restricciones de clave foránea, el SoftDelete puede causar problemas. Asegúrate de:

1. Usar `onDelete('set null')` o `onDelete('cascade')` en migraciones
2. O manejar las relaciones manualmente antes de eliminar

### 🔒 Seguridad

Los registros eliminados siguen en la base de datos, así que:

- NO contienen información sensible que deba ser borrada completamente
- Considera políticas de retención de datos
- Implementa limpieza periódica con `forceDelete()` si es necesario

### 📊 Performance

- Los índices en `deleted_at` mejoran el rendimiento
- Considera agregar: `$table->index('deleted_at');` en migraciones

### 🔄 Restauración en Masa

```php
// Restaurar todos los cursos eliminados en los últimos 7 días
Curso::onlyTrashed()
    ->where('deleted_at', '>', now()->subDays(7))
    ->restore();
```

---

## Comandos Artisan Útiles

### Limpiar Registros Antiguos

Crear un comando personalizado:

```bash
php artisan make:command LimpiarEliminados
```

```php
// app/Console/Commands/LimpiarEliminados.php
public function handle()
{
    $dias = 30;
    
    $eliminados = Curso::onlyTrashed()
        ->where('deleted_at', '<', now()->subDays($dias))
        ->forceDelete();
    
    $this->info("Se eliminaron permanentemente {$eliminados} cursos.");
}
```

Ejecutar:
```bash
php artisan limpiar:eliminados
```

---

## Resumen

✅ **Todos los modelos tienen SoftDeletes activado**  
✅ **Las eliminaciones son reversibles**  
✅ **Los datos se mantienen en la base de datos**  
✅ **Puedes restaurar o eliminar permanentemente cuando sea necesario**

**Próximos pasos recomendados:**
1. Implementar vistas de "papelera" para cada módulo
2. Agregar botones de "Restaurar" en la interfaz
3. Crear políticas de limpieza automática
4. Agregar permisos para `forceDelete` (solo administradores)
