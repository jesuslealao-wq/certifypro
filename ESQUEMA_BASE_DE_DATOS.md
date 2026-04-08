# Esquema de Base de Datos - Sistema de Gestión de Certificados Académicos

## Índice
1. [Visión General](#visión-general)
2. [Tablas del Sistema](#tablas-del-sistema)
3. [Relaciones](#relaciones)
4. [Sistema de Configuración](#sistema-de-configuración)
5. [Flujo de Datos](#flujo-de-datos)

---

## Visión General

El sistema está diseñado para gestionar el ciclo completo de certificación académica, desde la definición de cursos hasta la emisión de certificados con validación y trazabilidad.

### Arquitectura de Datos

```
┌─────────────────────────────────────────────────────────────┐
│                    CONTENIDO (El "Qué")                      │
├─────────────────────────────────────────────────────────────┤
│  Cursos → Módulos → Clases                                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                  EJECUCIÓN (El "Cuándo")                     │
├─────────────────────────────────────────────────────────────┤
│  Cohortes (Curso + Fechas + Instructor)                     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│               CERTIFICACIÓN (El Resultado)                   │
├─────────────────────────────────────────────────────────────┤
│  Certificados (Alumno + Cohorte + Firmas + Validación)      │
└─────────────────────────────────────────────────────────────┘
```

---

## Tablas del Sistema

### 1. **estatus** - Catálogo Centralizado de Estados

Gestiona todos los ciclos de vida del sistema mediante un catálogo único.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `entidad` | varchar(255) | Tabla a la que pertenece: `curso`, `cohorte`, `inscripcion`, `certificado` |
| `nombre` | varchar(255) | Nombre del estado: Publicado, Borrador, Activo, etc. |
| `descripcion` | text | Explicación para el usuario |
| `orden_visual` | integer | Orden de visualización (default: 0) |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Ejemplos de uso:**
```sql
-- Estados para cursos
entidad='curso', nombre='Publicado'
entidad='curso', nombre='Borrador'

-- Estados para certificados
entidad='certificado', nombre='Emitido'
entidad='certificado', nombre='Anulado'
```

---

### 2. **cursos** - Definición de Cursos Académicos

Contiene la información base de cada curso ofrecido.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `nombre_curso` | varchar(255) | Nombre del curso |
| `horas_academicas` | integer | Total de horas del curso |
| `estado_id` | bigint | FK → `estatus.id` (filtrado por entidad='curso') |
| `descripcion` | text | Descripción del curso |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `hasMany` → Módulos
- `hasMany` → Cohortes
- `belongsTo` → Estatus

---

### 3. **modulos** - Módulos de los Cursos

Divide el contenido del curso en módulos temáticos.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `curso_id` | bigint | FK → `cursos.id` (CASCADE) |
| `titulo_modulo` | varchar(255) | Título del módulo |
| `horas_modulo` | integer | Horas específicas de este módulo |
| `orden` | integer | Orden de presentación (default: 1) |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `belongsTo` → Curso
- `hasMany` → Clases

---

### 4. **clases** - Clases dentro de Módulos

Detalla el contenido específico de cada módulo.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `modulo_id` | bigint | FK → `modulos.id` (CASCADE) |
| `titulo_clase` | varchar(255) | Título de la clase |
| `orden` | integer | Orden de presentación (default: 1) |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `belongsTo` → Módulo

---

### 5. **autoridades** - Personal Autorizado

Gestiona instructores y firmantes de certificados.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `nombre_completo` | varchar(255) | Nombre completo |
| `cargo` | varchar(255) | Cargo o posición |
| `especialidad` | varchar(255) | Área de especialidad |
| `firma_path` | varchar(255) | Ruta a imagen PNG transparente de la firma |
| `sello_path` | varchar(255) | Ruta a imagen del sello |
| `activo` | boolean | Estado activo/inactivo (default: true) |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `hasMany` → Cohortes (como instructor)
- `hasMany` → Certificados (como firmante 1, 2 o 3)

---

### 6. **alumnos** - Estudiantes

Registro de estudiantes del sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `identificacion_nacional` | varchar(255) | Cédula/DNI (UNIQUE) |
| `nombre_completo` | varchar(255) | Nombre completo |
| `email` | varchar(255) | Correo electrónico |
| `telefono` | varchar(255) | Número de teléfono |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `hasMany` → Certificados

---

### 7. **cohortes** - Ejecuciones de Cursos

Representa una instancia específica de un curso con fechas e instructor.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `curso_id` | bigint | FK → `cursos.id` (CASCADE) |
| `instructor_id` | bigint | FK → `autoridades.id` (SET NULL) |
| `fecha_inicio` | date | Fecha de inicio |
| `fecha_fin` | date | Fecha de finalización |
| `codigo_cohorte` | varchar(255) | Código único (ej: PHP-2026-Q1) |
| `estado_id` | bigint | FK → `estatus.id` (filtrado por entidad='cohorte') |
| `modalidad` | varchar(255) | `presencial`, `online_vivo`, `hibrido` |
| `firma_default_1_id` | bigint | FK → `autoridades.id` (Firmante 1 por defecto) |
| `firma_default_2_id` | bigint | FK → `autoridades.id` (Firmante 2 por defecto) |
| `firma_default_3_id` | bigint | FK → `autoridades.id` (Firmante 3 por defecto) |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `belongsTo` → Curso
- `belongsTo` → Autoridad (instructor)
- `belongsTo` → Estatus
- `belongsTo` → Autoridad (firmas por defecto x3)
- `hasMany` → Certificados

---

### 8. **certificados** - Certificados Emitidos

Documento final de certificación con trazabilidad completa.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `alumno_id` | bigint | FK → `alumnos.id` (CASCADE) |
| `cohorte_id` | bigint | FK → `cohortes.id` (CASCADE) |
| **Registro Legal** | | |
| `libro` | varchar(255) | Número de libro de registro |
| `folio` | varchar(255) | Número de folio |
| `codigo_registro_manual` | varchar(255) | Código alfanumérico externo |
| **Seguridad del Sistema** | | |
| `codigo_verificacion_app` | varchar(255) | Código generado automáticamente (UNIQUE) |
| `uuid_seguridad` | uuid | UUID para validación profunda (UNIQUE) |
| **Metadatos** | | |
| `fecha_emision` | date | Fecha de emisión del certificado |
| `estado_id` | bigint | FK → `estatus.id` (filtrado por entidad='certificado') |
| `estado` | varchar(255) | Estado simple: `valido` / `anulado` (default: 'valido') |
| `creado_por_usuario_id` | bigint | ID del administrativo que emitió |
| **Firmas Finales** | | |
| `firma_1_id` | bigint | FK → `autoridades.id` (Firmante 1) |
| `firma_2_id` | bigint | FK → `autoridades.id` (Firmante 2) |
| `firma_3_id` | bigint | FK → `autoridades.id` (Firmante 3) |
| **Snapshot del Contenido** | | |
| `temario_snapshot` | json | Copia exacta de módulos y clases al momento de emitir |
| **Archivos Generados** | | |
| `pdf_path` | varchar(255) | Ruta al PDF: `/storage/certs/2026/CERT-ABC.pdf` |
| `qr_path` | varchar(255) | Ruta a la imagen del QR |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Relaciones:**
- `belongsTo` → Alumno
- `belongsTo` → Cohorte
- `belongsTo` → Estatus
- `belongsTo` → Autoridad (firmas x3)

**Nota Importante:** El campo `temario_snapshot` congela el contenido del curso al momento de aprobar, garantizando que el certificado refleje exactamente lo que se cursó.

---

### 9. **configuraciones** - Sistema de Configuración

Gestiona valores por defecto y configuraciones del sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único |
| `clave` | varchar(255) | Identificador único de la configuración (UNIQUE) |
| `valor` | text | Valor de la configuración |
| `tipo` | varchar(255) | Tipo de dato: `string`, `integer`, `boolean`, `json` |
| `grupo` | varchar(255) | Grupo: `general`, `cursos`, `certificados`, etc. |
| `descripcion` | varchar(255) | Descripción de la configuración |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

**Configuraciones por Defecto:**

| Clave | Valor | Tipo | Grupo | Descripción |
|-------|-------|------|-------|-------------|
| `estado_default_curso` | 1 | integer | cursos | Estado por defecto al crear un curso |
| `estado_default_cohorte` | 1 | integer | cohortes | Estado por defecto al crear una cohorte |
| `estado_default_certificado` | 1 | integer | certificados | Estado por defecto al crear un certificado |
| `certificado_estado_valido` | valido | string | certificados | Estado de validez por defecto |

---

## Relaciones

### Diagrama de Relaciones Principales

```
estatus (1) ←──── (N) cursos (1) ←──── (N) modulos (1) ←──── (N) clases
                       ↓
                   cohortes (N) ──→ (1) autoridades (instructor)
                       ↓                      ↓
                 certificados (N) ──→ (1) alumnos
                       ↓
                   autoridades (firmas x3)
```

### Tipos de Relaciones

1. **Cascada (CASCADE)**: Al eliminar el padre, se eliminan los hijos
   - `cursos` → `modulos` → `clases`
   - `cohortes` → `certificados`
   - `alumnos` → `certificados`

2. **Set Null (SET NULL)**: Al eliminar el padre, el campo se pone en NULL
   - `autoridades` → `cohortes.instructor_id`
   - `autoridades` → `certificados.firma_X_id`

3. **Restrict (RESTRICT)**: No permite eliminar si hay referencias
   - `estatus` → `cursos`, `cohortes`, `certificados`

---

## Sistema de Configuración

### Uso del Modelo Configuracion

```php
// Obtener un valor de configuración
$estadoDefault = Configuracion::obtener('estado_default_curso', 1);

// Establecer un valor de configuración
Configuracion::establecer('estado_default_curso', 2);

// Obtener todas las configuraciones de un grupo
$configsCursos = Configuracion::porGrupo('cursos');
```

### Implementación en Controladores

Los controladores automáticamente asignan valores por defecto cuando no se proporcionan:

```php
// En CursoController
if (empty($validated['estado_id'])) {
    $validated['estado_id'] = Configuracion::obtener('estado_default_curso', 1);
}
```

---

## Flujo de Datos

### 1. Creación de Contenido
```
1. Crear Curso → 2. Agregar Módulos → 3. Agregar Clases
```

### 2. Programación de Ejecución
```
1. Crear Cohorte (Curso + Fechas + Instructor) → 2. Asignar Firmas por Defecto
```

### 3. Emisión de Certificados
```
1. Seleccionar Alumno + Cohorte
2. Sistema genera código_verificacion_app y uuid_seguridad automáticamente
3. Se copia temario_snapshot del curso (módulos y clases)
4. Se asignan firmas (desde cohorte o personalizadas)
5. Se registra en libro/folio
6. Se genera PDF y QR
7. Certificado queda con estado 'valido'
```

### 4. Validación de Certificados
```
Usuario ingresa código_verificacion_app → Sistema busca en BD → Muestra datos del certificado + estado de validez
```

---

## Consideraciones de Diseño

### Timestamps
Todas las tablas incluyen `created_at` y `updated_at` para trazabilidad completa.

### Seguridad
- Códigos de verificación únicos generados automáticamente
- UUID adicional para validación profunda
- Snapshot del temario para inmutabilidad

### Flexibilidad
- Sistema de estados centralizado y configurable
- Configuraciones dinámicas sin modificar código
- Múltiples firmantes configurables por cohorte

### Integridad
- Restricciones de clave foránea apropiadas
- Validaciones a nivel de base de datos
- Unique constraints en campos críticos

---

## Notas de Implementación

1. **Índices Recomendados:**
   - `certificados.codigo_verificacion_app` (ya es UNIQUE)
   - `certificados.uuid_seguridad` (ya es UNIQUE)
   - `alumnos.identificacion_nacional` (ya es UNIQUE)
   - `estatus(entidad, orden_visual)` para filtros rápidos

2. **Backups:**
   - El campo `temario_snapshot` es crítico - no debe modificarse después de emisión
   - Considerar backups incrementales de la tabla `certificados`

3. **Performance:**
   - El modelo `Configuracion` usa caché (1 hora) para optimizar consultas frecuentes
   - Eager loading recomendado en listados con relaciones

---

**Versión:** 1.0  
**Fecha:** Febrero 2026  
**Autor:** Sistema CertifyPro
