# Auditoría de Migraciones vs Controladores

## Resumen Ejecutivo

Se encontraron **inconsistencias críticas** entre las migraciones de base de datos y el código de los controladores/modelos.

---

## Problemas Encontrados

### 🔴 **CRÍTICO: Columnas Faltantes**

#### 1. **modulos** - Falta `horas_modulo`
- **Migración**: NO tiene la columna `horas_modulo`
- **Modelo**: Espera `horas_modulo` en fillable
- **Controlador**: Valida y guarda `horas_modulo`
- **Impacto**: Error al crear/editar módulos

#### 2. **cohortes** - Falta `modalidad`
- **Migración**: NO tiene la columna `modalidad`
- **Modelo**: Espera `modalidad` en fillable
- **Controlador**: Valida y guarda `modalidad`
- **Impacto**: Error al crear/editar cohortes

#### 3. **cohortes** - Nombre incorrecto de columna
- **Migración**: Usa `codigo_promocion`
- **Modelo/Controlador**: Usa `codigo_cohorte`
- **Impacto**: Error al guardar código de cohorte

#### 4. **certificados** - Falta `estado`
- **Migración**: NO tiene la columna `estado` (string)
- **Modelo**: Espera `estado` en fillable
- **Controlador**: Valida y guarda `estado`
- **Impacto**: Error al crear certificados

#### 5. **certificados** - Falta `creado_por_usuario_id`
- **Migración**: NO tiene la columna
- **Modelo**: Espera `creado_por_usuario_id` en fillable
- **Impacto**: No se puede rastrear quién creó el certificado

#### 6. **certificados** - Falta `temario_snapshot`
- **Migración**: NO tiene la columna
- **Modelo**: Espera `temario_snapshot` (json) en fillable
- **Impacto**: No se guarda el snapshot del temario

#### 7. **certificados** - Falta `pdf_path` y `qr_path`
- **Migración**: NO tiene estas columnas
- **Modelo**: Espera `pdf_path` y `qr_path` en fillable
- **Impacto**: No se pueden guardar rutas de archivos generados

---

## Comparación Detallada por Tabla

### ✅ **estatus** - CORRECTO
| Columna | Migración | Modelo | Controlador |
|---------|-----------|--------|-------------|
| id | ✅ | ✅ | ✅ |
| entidad | ✅ | ✅ | ✅ |
| nombre | ✅ | ✅ | ✅ |
| descripcion | ✅ | ✅ | ✅ |
| orden_visual | ✅ | ✅ | ✅ |
| created_at | ✅ (agregado) | ✅ | ✅ |
| updated_at | ✅ (agregado) | ✅ | ✅ |
| deleted_at | ✅ (agregado) | ✅ | ✅ |

### ✅ **cursos** - CORRECTO (después de fix)
| Columna | Migración | Modelo | Controlador |
|---------|-----------|--------|-------------|
| id | ✅ | ✅ | ✅ |
| nombre_curso | ✅ | ✅ | ✅ |
| horas_academicas | ✅ | ✅ | ✅ |
| estado_id | ✅ (renombrado) | ✅ | ✅ |
| descripcion | ✅ | ✅ | ✅ |
| created_at | ✅ (agregado) | ✅ | ✅ |
| updated_at | ✅ (agregado) | ✅ | ✅ |
| deleted_at | ✅ (agregado) | ✅ | ✅ |

### 🔴 **modulos** - PROBLEMAS
| Columna | Migración | Modelo | Controlador | Estado |
|---------|-----------|--------|-------------|--------|
| id | ✅ | ✅ | ✅ | OK |
| curso_id | ✅ | ✅ | ✅ | OK |
| titulo_modulo | ✅ | ✅ | ✅ | OK |
| horas_modulo | ❌ | ✅ | ✅ | **FALTA EN BD** |
| orden | ✅ | ✅ | ✅ | OK |
| created_at | ✅ (agregado) | ✅ | ✅ | OK |
| updated_at | ✅ (agregado) | ✅ | ✅ | OK |
| deleted_at | ✅ (agregado) | ✅ | ✅ | OK |

### ✅ **clases** - CORRECTO
| Columna | Migración | Modelo | Controlador |
|---------|-----------|--------|-------------|
| id | ✅ | ✅ | ✅ |
| modulo_id | ✅ | ✅ | ✅ |
| titulo_clase | ✅ | ✅ | ✅ |
| orden | ✅ | ✅ | ✅ |
| created_at | ✅ (agregado) | ✅ | ✅ |
| updated_at | ✅ (agregado) | ✅ | ✅ |
| deleted_at | ✅ (agregado) | ✅ | ✅ |

### ✅ **autoridades** - CORRECTO
| Columna | Migración | Modelo | Controlador |
|---------|-----------|--------|-------------|
| id | ✅ | ✅ | ✅ |
| nombre_completo | ✅ | ✅ | ✅ |
| cargo | ✅ | ✅ | ✅ |
| especialidad | ✅ | ✅ | ✅ |
| firma_path | ✅ | ✅ | ✅ |
| sello_path | ✅ | ✅ | ✅ |
| activo | ✅ | ✅ | ✅ |
| created_at | ✅ (agregado) | ✅ | ✅ |
| updated_at | ✅ (agregado) | ✅ | ✅ |
| deleted_at | ✅ (agregado) | ✅ | ✅ |

### ✅ **alumnos** - CORRECTO
| Columna | Migración | Modelo | Controlador |
|---------|-----------|--------|-------------|
| id | ✅ | ✅ | ✅ |
| identificacion_nacional | ✅ | ✅ | ✅ |
| nombre_completo | ✅ | ✅ | ✅ |
| email | ✅ | ✅ | ✅ |
| telefono | ✅ | ✅ | ✅ |
| created_at | ✅ (agregado) | ✅ | ✅ |
| updated_at | ✅ (agregado) | ✅ | ✅ |
| deleted_at | ✅ (agregado) | ✅ | ✅ |

### 🔴 **cohortes** - PROBLEMAS MÚLTIPLES
| Columna | Migración | Modelo | Controlador | Estado |
|---------|-----------|--------|-------------|--------|
| id | ✅ | ✅ | ✅ | OK |
| curso_id | ✅ | ✅ | ✅ | OK |
| instructor_id | ✅ | ✅ | ✅ | OK |
| fecha_inicio | ✅ | ✅ | ✅ | OK |
| fecha_fin | ✅ | ✅ | ✅ | OK |
| codigo_cohorte | ❌ (es codigo_promocion) | ✅ | ✅ | **NOMBRE INCORRECTO** |
| estado_id | ✅ (renombrado) | ✅ | ✅ | OK |
| modalidad | ❌ | ✅ | ✅ | **FALTA EN BD** |
| firma_default_1_id | ✅ | ✅ | ✅ | OK |
| firma_default_2_id | ✅ | ✅ | ✅ | OK |
| firma_default_3_id | ✅ | ✅ | ✅ | OK |
| created_at | ✅ (agregado) | ✅ | ✅ | OK |
| updated_at | ✅ (agregado) | ✅ | ✅ | OK |
| deleted_at | ✅ (agregado) | ✅ | ✅ | OK |

### 🔴 **certificados** - PROBLEMAS CRÍTICOS
| Columna | Migración | Modelo | Controlador | Estado |
|---------|-----------|--------|-------------|--------|
| id | ✅ | ✅ | ✅ | OK |
| alumno_id | ✅ | ✅ | ✅ | OK |
| cohorte_id | ✅ | ✅ | ✅ | OK |
| libro | ✅ | ✅ | ✅ | OK |
| folio | ✅ | ✅ | ✅ | OK |
| codigo_registro_manual | ✅ | ✅ | ✅ | OK |
| codigo_verificacion_app | ✅ | ✅ | ✅ | OK |
| uuid_seguridad | ✅ | ✅ | ✅ | OK |
| fecha_emision | ✅ | ✅ | ✅ | OK |
| estado_id | ✅ (renombrado) | ✅ | ✅ | OK |
| estado | ❌ | ✅ | ✅ | **FALTA EN BD** |
| creado_por_usuario_id | ❌ | ✅ | ❌ | **FALTA EN BD** |
| firma_1_id | ✅ | ✅ | ✅ | OK |
| firma_2_id | ✅ | ✅ | ✅ | OK |
| firma_3_id | ✅ | ✅ | ✅ | OK |
| temario_snapshot | ❌ | ✅ | ❌ | **FALTA EN BD** |
| pdf_path | ❌ | ✅ | ❌ | **FALTA EN BD** |
| qr_path | ❌ | ✅ | ❌ | **FALTA EN BD** |
| created_at | ✅ (agregado) | ✅ | ✅ | OK |
| updated_at | ✅ (agregado) | ✅ | ✅ | OK |
| deleted_at | ✅ (agregado) | ✅ | ✅ | OK |

---

## Soluciones Requeridas

### Migración de Corrección Necesaria

Se debe crear una migración que agregue/corrija las siguientes columnas:

```php
// modulos
$table->integer('horas_modulo')->nullable();

// cohortes
$table->renameColumn('codigo_promocion', 'codigo_cohorte');
$table->string('modalidad')->nullable();

// certificados
$table->string('estado')->default('valido');
$table->unsignedBigInteger('creado_por_usuario_id')->nullable();
$table->json('temario_snapshot')->nullable();
$table->string('pdf_path')->nullable();
$table->string('qr_path')->nullable();
```

---

## Impacto en Producción

### 🔴 **ALTO RIESGO**
- **modulos**: No se pueden crear/editar con horas
- **cohortes**: No se pueden crear/editar con modalidad o código correcto
- **certificados**: Faltan campos críticos para el funcionamiento completo

### ⚠️ **FUNCIONALIDAD LIMITADA**
- No se puede guardar snapshot del temario
- No se puede rastrear quién creó certificados
- No se pueden guardar rutas de PDFs y QRs generados

---

## Recomendaciones

1. ✅ **Ejecutar migración de corrección INMEDIATAMENTE**
2. ✅ **Verificar que todos los formularios funcionen después del fix**
3. ✅ **Actualizar documentación del esquema**
4. ⚠️ **Implementar tests automatizados para prevenir futuras inconsistencias**
5. ⚠️ **Establecer proceso de revisión de migraciones vs modelos**

---

## Estado Actual

- ✅ Timestamps agregados a todas las tablas
- ✅ SoftDeletes implementado
- ✅ Sistema de configuración creado
- ✅ `estatus_id` renombrado a `estado_id`
- 🔴 **Faltan columnas críticas en 3 tablas**

---

**Fecha de Auditoría**: 17 de Febrero 2026  
**Prioridad**: 🔴 CRÍTICA - Requiere acción inmediata
