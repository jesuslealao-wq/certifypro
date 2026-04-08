# Guia de Instalacion - Sistema de Certificados

## Descripcion General

Sistema de gestion de certificados academicos desarrollado con Laravel 12 y PHP 8.2+.
Permite crear cursos, cohortes, alumnos, emitir certificados y generar PDFs
personalizados mediante plantillas HTML/CSS editables con un editor visual integrado.

---

## Requisitos Previos

Antes de comenzar la instalacion, asegurate de tener instalados los siguientes programas
en tu sistema operativo:

- **PHP 8.2 o superior**: El framework Laravel 12 requiere esta version minima.
  Para verificar tu version, ejecuta `php -v` en la terminal.
- **Composer 2.x**: Gestor de dependencias de PHP.
  Para verificar, ejecuta `composer --version`.
- **Node.js 18+ y npm**: Necesarios para compilar los assets del frontend (Tailwind CSS, Vite).
  Para verificar, ejecuta `node -v` y `npm -v`.
- **SQLite** (incluido con PHP) o **MySQL 8+**: La configuracion por defecto usa SQLite.
  Si prefieres MySQL, deberas ajustar el archivo `.env`.

### Extensiones PHP Requeridas

Estas extensiones deben estar habilitadas en tu archivo `php.ini`:

- `pdo_sqlite` (si usas SQLite) o `pdo_mysql` (si usas MySQL)
- `mbstring` (manejo de cadenas multibyte, necesario para nombres con acentos)
- `gd` (procesamiento de imagenes para fondos de plantillas PDF)
- `dom` (requerido por DomPDF para parsear HTML)
- `xml` (requerido por DomPDF)
- `fileinfo` (deteccion de tipos MIME al subir imagenes)

Para verificar las extensiones habilitadas, ejecuta:

```
php -m
```

---

## Paso 1: Clonar el Repositorio

```
git clone <URL_DEL_REPOSITORIO> certificados-app
cd certificados-app
```

El codigo fuente de la aplicacion Laravel se encuentra dentro del directorio `app/`.
Todos los comandos de artisan, composer y npm deben ejecutarse desde esa carpeta.

```
cd app
```

---

## Paso 2: Instalar Dependencias de PHP

Este comando descarga todas las librerias PHP definidas en `composer.json`,
incluyendo Laravel, DomPDF y sus dependencias:

```
composer install
```

Si estas en un entorno de produccion y no necesitas las herramientas de desarrollo
(PHPUnit, Faker, etc.), usa:

```
composer install --no-dev --optimize-autoloader
```

### Paquetes Principales Instalados

- `laravel/framework ^12.0`: Framework principal de la aplicacion.
- `barryvdh/laravel-dompdf ^3.1`: Libreria para generar archivos PDF a partir de HTML/CSS.
  Se usa en el servicio `CertificadoPdfService` para renderizar los certificados.
- `laravel/tinker ^2.10`: Consola interactiva para depuracion y pruebas rapidas.

---

## Paso 3: Configurar el Archivo de Entorno

Copia el archivo de ejemplo `.env.example` a `.env`. Este archivo contiene todas las
variables de configuracion del sistema (base de datos, colas, cache, etc.):

```
cp .env.example .env
```

En Windows (PowerShell):

```
Copy-Item .env.example .env
```

### Generar la Clave de Aplicacion

Laravel requiere una clave unica de 32 caracteres para cifrar sesiones, cookies y tokens CSRF.
Este comando la genera automaticamente y la guarda en `APP_KEY` dentro del archivo `.env`:

```
php artisan key:generate
```

---

## Paso 4: Configurar la Base de Datos

### Opcion A: SQLite (por defecto, recomendada para desarrollo)

SQLite almacena toda la base de datos en un unico archivo. No requiere instalar
un servidor de base de datos separado. Solo necesitas crear el archivo vacio:

```
touch database/database.sqlite
```

En Windows (PowerShell):

```
New-Item database/database.sqlite -ItemType File
```

Asegurate de que tu archivo `.env` contenga:

```
DB_CONNECTION=sqlite
```

### Opcion B: MySQL

Si prefieres usar MySQL, crea primero la base de datos en tu servidor MySQL:

```sql
CREATE DATABASE certificados_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego edita tu archivo `.env` con los datos de conexion:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=certificados_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contrasena
```

---

## Paso 5: Ejecutar las Migraciones

Las migraciones crean todas las tablas necesarias en la base de datos.
Se ejecutan en orden cronologico segun el prefijo de fecha de cada archivo.

```
php artisan migrate
```

### Tablas Creadas (en orden de ejecucion)

1. `users` - Usuarios del sistema (autenticacion).
2. `cache` - Almacenamiento de cache en base de datos.
3. `jobs` - Cola de trabajos asincrona.
4. `estatus` - Catalogo de estados reutilizable (para cursos, cohortes, certificados).
5. `cursos` - Cursos academicos con nombre, horas y descripcion.
6. `modulos` - Modulos que componen un curso.
7. `clases` - Clases individuales dentro de cada modulo.
8. `autoridades` - Personas con cargo institucional (firmantes, instructores).
9. `alumnos` - Estudiantes registrados en el sistema.
10. `cohortes` - Grupos de alumnos que toman un curso en un periodo especifico.
11. `alumno_cohorte` - Tabla pivote que relaciona alumnos con cohortes.
12. `certificados` - Certificados emitidos a alumnos por completar una cohorte.
13. `configuraciones` - Parametros globales del sistema (estados por defecto, etc.).
14. `plantillas_pdf` - Plantillas HTML/CSS para generar los PDFs de certificados.

### Migraciones Adicionales

- `add_fondos_to_plantillas_pdf` - Agrega campos para imagenes de fondo por pagina.
- `add_plantilla_pdf_id_to_cohortes_and_certificados` - Agrega la relacion entre
  cohortes/certificados y sus plantillas PDF asignadas.
- `add_soft_deletes_to_tables` - Agrega eliminacion suave (papelera) a todas las tablas.
- `fix_missing_columns` - Agrega columnas faltantes como `estado`, `pdf_path`, `qr_path`.

---

## Paso 6: Ejecutar los Seeders (datos iniciales)

Los seeders insertan datos iniciales necesarios para que el sistema funcione correctamente:

```
php artisan db:seed
```

### Seeders Disponibles

- `EstatusSeeder`: Crea los estados predeterminados del sistema (activo, inactivo, etc.)
  para las entidades curso, cohorte y certificado.
- `PlantillaPdfSeeder`: Crea una plantilla PDF de ejemplo con diseno profesional
  en formato A4 horizontal (landscape) con dos paginas. Para ejecutarlo individualmente:

### Seeders de Datos de Prueba (recomendado para desarrollo)

El proyecto incluye un seeder orquestador que carga un conjunto de datos de prueba
en el orden correcto para respetar llaves foraneas:

- `DatosPruebaSeeder`: Ejecuta los seeders en el siguiente orden:
  - `EstatusSeeder`
  - `PlantillaPdfSeeder`
  - `AutoridadesPruebaSeeder`
  - `CursosPruebaSeeder`
  - `ModulosPruebaSeeder`
  - `ClasesPruebaSeeder`
  - `AlumnosPruebaSeeder`

Estos seeders agregan registros de ejemplo para poder probar rapidamente:

- Autoridades activas (firmantes e instructor)
- Cursos de ejemplo
- Modulos y clases de ejemplo asociados a los cursos
- Alumnos de ejemplo

Para ejecutar solo el conjunto de datos de prueba:

```
php artisan db:seed --class=DatosPruebaSeeder
```

Si quieres reconstruir la base de datos completa desde cero y sembrar datos de prueba:

```
php artisan migrate:fresh --seed
```

Nota: `CursosPruebaSeeder` detecta automaticamente si la columna de estado del curso
se llama `estado_id` o `estatus_id`, para ser compatible con bases de datos que ya
aplicaron el rename.

```
php artisan db:seed --class=PlantillaPdfSeeder
```

Nota: `PlantillaPdfSeeder` actualmente crea una nueva plantilla con `create()`. Si lo ejecutas
varias veces, se duplicaran registros. Si deseas, se puede ajustar para que sea idempotente.

---

## Paso 7: Crear el Enlace Simbolico de Storage

Laravel necesita un enlace simbolico desde `public/storage` hacia `storage/app/public`
para servir archivos subidos (como las imagenes de fondo de las plantillas PDF):

```
php artisan storage:link
```

Si no ejecutas este comando, las imagenes de fondo de las plantillas no se mostraran
correctamente en la vista previa ni en los PDFs generados.

---

## Paso 8: Instalar Dependencias del Frontend

```
npm install
```

### Para Desarrollo

Este comando inicia el servidor de Vite que compila Tailwind CSS y otros assets
en tiempo real con recarga automatica del navegador:

```
npm run dev
```

### Para Produccion

Este comando genera los archivos CSS y JavaScript minificados y optimizados:

```
npm run build
```

---

## Paso 9: Iniciar el Servidor de Desarrollo

```
php artisan serve
```

El sistema estara disponible en `http://localhost:8000`.

Si deseas usar un puerto diferente:

```
php artisan serve --port=8080
```

### Comando Combinado (servidor + vite + logs)

El proyecto incluye un script de Composer que inicia todo junto:

```
composer dev
```

Esto ejecuta simultaneamente el servidor PHP, la cola de trabajos, los logs
y el servidor de Vite.

---

## Paso 10: Verificar la Instalacion

1. Abre `http://localhost:8000` en tu navegador.
2. Deberias ver el dashboard del sistema.
3. Navega a "Plantillas PDF" en el menu lateral para verificar que la plantilla
   de ejemplo se cargo correctamente (si ejecutaste el seeder).

---

## Estructura de Directorios Relevante

```
app/
  app/
    Http/
      Controllers/
        CertificadoController.php   -- CRUD y generacion PDF de certificados
        CohorteController.php       -- CRUD de cohortes y generacion PDF masiva
        PlantillaPdfController.php  -- CRUD y editor visual de plantillas PDF
        CursoController.php         -- CRUD de cursos academicos
        AlumnoController.php        -- CRUD de alumnos
        AutoridadController.php     -- CRUD de autoridades/firmantes
        EstatusController.php       -- CRUD de estados del sistema
        ModuloController.php        -- CRUD de modulos de un curso
        ClaseController.php         -- CRUD de clases de un modulo
    Models/
      Certificado.php     -- Modelo de certificado con relaciones y auto-generacion de UUID
      Cohorte.php          -- Modelo de cohorte con relaciones a curso, alumnos, firmas
      PlantillaPdf.php     -- Modelo de plantilla con variables disponibles y datos de prueba
      Curso.php            -- Modelo de curso academico
      Alumno.php           -- Modelo de alumno/estudiante
      Autoridad.php        -- Modelo de autoridad/firmante
      Estatus.php          -- Modelo de estado configurable por entidad
      Configuracion.php    -- Modelo para parametros globales del sistema
    Services/
      CertificadoPdfService.php  -- Servicio central de generacion de PDF
    Traits/
      HasTrash.php         -- Trait reutilizable para papelera (soft delete)
  database/
    migrations/            -- Todas las migraciones de base de datos
    seeders/               -- Datos iniciales del sistema
  resources/
    views/
      layouts/app.blade.php           -- Layout principal con sidebar
      certificados/                    -- Vistas de certificados (index, create, edit, show)
      cohortes/                        -- Vistas de cohortes (index, create, edit, show)
      plantillas-pdf/                  -- Vistas del editor de plantillas (index, create, editor, preview)
      cursos/                          -- Vistas de cursos
      alumnos/                         -- Vistas de alumnos
      autoridades/                     -- Vistas de autoridades
      estatus/                         -- Vistas de estados
  routes/
    web.php                -- Todas las rutas del sistema
  storage/
    app/public/
      plantillas-pdf/fondos/  -- Imagenes de fondo subidas para las plantillas
```

---

## Flujo de Trabajo: Generar un Certificado PDF

Este es el flujo completo desde la creacion de datos hasta la impresion del certificado:

### 1. Crear los Datos Base

Antes de poder emitir certificados, necesitas tener registrados en el sistema:

- Al menos un **Curso** con su nombre y horas academicas.
- Al menos una **Autoridad** activa que actue como firmante.
- Al menos un **Alumno** registrado.
- Al menos un **Estado** para certificados (ej: "Valido", "Anulado").

### 2. Crear una Plantilla PDF

Navega a "Plantillas PDF" y crea una nueva plantilla. El editor visual te permite:

- Escribir HTML con variables dinamicas como `{{ $alumno_nombre }}`, `{{ $curso_nombre }}`, etc.
- Agregar estilos CSS personalizados.
- Subir imagenes de fondo para la pagina 1 y pagina 2 del certificado.
- Previsualizar el resultado con datos de prueba.

### 3. Crear una Cohorte

Crea una cohorte asociada a un curso. En la vista de detalle de la cohorte puedes:

- Agregar alumnos a la cohorte.
- Asignar una plantilla PDF a la cohorte (todas los certificados de esta cohorte
  usaran esta plantilla por defecto).

### 4. Generar Certificados

Desde la vista de la cohorte, usa el boton "Generar Masivo" para crear certificados
para todos los alumnos seleccionados. Tambien puedes crear certificados individuales
desde la seccion "Certificados".

### 5. Configurar Certificados en Lote

Desde la vista de la cohorte puedes configurar en lote:

- Libro y folio (con auto-incremento).
- Codigo de registro manual.
- Fecha de emision.
- Estado.
- Firmantes (firma 1, 2 y 3).

### 6. Imprimir o Descargar PDF

- **Individual**: Desde la lista de certificados o la vista de detalle, usa los
  botones "PDF" (ver en navegador) o "Descargar" (guardar archivo).
- **Masivo**: Desde la vista de la cohorte, usa los botones "Imprimir Todos"
  (ver todos en un solo PDF) o "Descargar PDF" (guardar archivo combinado).

### Prioridad de Plantilla

Cuando se genera un PDF, el sistema busca la plantilla en este orden:

1. Plantilla asignada directamente al certificado individual.
2. Plantilla asignada a la cohorte del certificado.
3. Plantilla marcada como predeterminada en el sistema.

Si ninguna plantilla esta disponible, se muestra un mensaje de error.

---

## Variables Disponibles en las Plantillas

Estas son las variables que puedes usar dentro del HTML de una plantilla.
Se reemplazan automaticamente con los datos reales del certificado al generar el PDF.

| Variable                      | Descripcion                              | Valor por defecto si esta vacio |
|-------------------------------|------------------------------------------|---------------------------------|
| `{{ $alumno_nombre }}`        | Nombre completo del alumno (mayusculas)  | NOMBRE DEL ALUMNO               |
| `{{ $alumno_identificacion }}`| Cedula o identificacion nacional         | 00000000                         |
| `{{ $alumno_email }}`         | Correo electronico del alumno            | sin-email@ejemplo.com            |
| `{{ $curso_nombre }}`         | Nombre del curso                         | Nombre del Curso                 |
| `{{ $curso_horas }}`          | Horas academicas del curso               | 0                                |
| `{{ $curso_descripcion }}`    | Descripcion del curso                    | (vacio)                          |
| `{{ $cohorte_codigo }}`       | Codigo identificador de la cohorte       | COH-0000                         |
| `{{ $cohorte_modalidad }}`    | Modalidad de la cohorte                  | Presencial                       |
| `{{ $cohorte_fecha_inicio }}` | Fecha de inicio (formato dd/mm/aaaa)     | Fecha actual                     |
| `{{ $cohorte_fecha_fin }}`    | Fecha de fin (formato dd/mm/aaaa)        | Fecha actual                     |
| `{{ $certificado_fecha_emision }}` | Fecha de emision del certificado    | Fecha actual                     |
| `{{ $certificado_codigo }}`   | Codigo de verificacion unico             | SIN-CODIGO                       |
| `{{ $certificado_libro }}`    | Numero de libro                          | 0                                |
| `{{ $certificado_folio }}`    | Numero de folio                          | 0                                |
| `{{ $certificado_registro }}` | Codigo de registro manual                | (vacio)                          |
| `{{ $firma_1_nombre }}`       | Nombre del firmante 1                    | (vacio)                          |
| `{{ $firma_1_cargo }}`        | Cargo del firmante 1                     | (vacio)                          |
| `{{ $firma_2_nombre }}`       | Nombre del firmante 2                    | (vacio)                          |
| `{{ $firma_2_cargo }}`        | Cargo del firmante 2                     | (vacio)                          |
| `{{ $firma_3_nombre }}`       | Nombre del firmante 3                    | (vacio)                          |
| `{{ $firma_3_cargo }}`        | Cargo del firmante 3                     | (vacio)                          |

Cualquier variable no reconocida que aparezca en el HTML sera eliminada automaticamente
al momento de generar el PDF, evitando que se muestren etiquetas crudas en el documento final.

---

## Rutas del Sistema

### Rutas de Recursos (CRUD completo)

Cada recurso tiene las 7 rutas estandar de Laravel (index, create, store, show, edit, update, destroy)
mas rutas adicionales para papelera (soft delete):

| Recurso        | Prefijo URL       | Controlador              |
|----------------|-------------------|--------------------------|
| Estatus        | `/estatus`        | EstatusController        |
| Cursos         | `/cursos`         | CursoController          |
| Modulos        | `/modulos`        | ModuloController         |
| Clases         | `/clases`         | ClaseController          |
| Autoridades    | `/autoridades`    | AutoridadController      |
| Alumnos        | `/alumnos`        | AlumnoController         |
| Cohortes       | `/cohortes`       | CohorteController        |
| Certificados   | `/certificados`   | CertificadoController    |
| Plantillas PDF | `/plantillas-pdf` | PlantillaPdfController   |

### Rutas Especiales de Certificados

| Metodo | URL                                      | Descripcion                          |
|--------|------------------------------------------|--------------------------------------|
| GET    | `/certificados/{id}/pdf`                 | Ver PDF del certificado en navegador |
| GET    | `/certificados/{id}/descargar-pdf`       | Descargar PDF del certificado        |

### Rutas Especiales de Cohortes

| Metodo | URL                                               | Descripcion                                    |
|--------|---------------------------------------------------|------------------------------------------------|
| POST   | `/cohortes/{id}/alumnos/agregar`                  | Agregar alumnos a la cohorte                   |
| DELETE | `/cohortes/{id}/alumnos/{alumno_id}`              | Remover alumno de la cohorte                   |
| POST   | `/cohortes/{id}/generar-certificados-masivo`       | Generar certificados para alumnos seleccionados|
| POST   | `/cohortes/{id}/asignar-plantilla`                | Asignar plantilla PDF a la cohorte             |
| POST   | `/cohortes/{id}/configurar-certificados-masivo`    | Configurar certificados en lote                |
| PUT    | `/cohortes/{id}/certificados/{cert_id}`           | Editar certificado individual en la cohorte    |
| GET    | `/cohortes/{id}/generar-pdfs`                     | Ver PDF de todos los certificados juntos       |
| GET    | `/cohortes/{id}/descargar-pdfs`                   | Descargar PDF masivo de la cohorte             |

### Rutas Especiales de Plantillas PDF

| Metodo | URL                                        | Descripcion                              |
|--------|--------------------------------------------|------------------------------------------|
| GET    | `/plantillas-pdf/{id}/preview`             | Vista previa con datos de prueba         |
| POST   | `/plantillas-pdf/{id}/duplicar`            | Duplicar una plantilla existente         |
| POST   | `/plantillas-pdf/{id}/upload-fondo`        | Subir imagen de fondo para una pagina    |
| POST   | `/plantillas-pdf/{id}/remove-fondo`        | Eliminar imagen de fondo de una pagina   |

---

## Comandos Utiles

### Limpiar Caches

Cuando hagas cambios en la configuracion, rutas o vistas, limpia las caches:

```
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

O todos juntos:

```
php artisan optimize:clear
```

### Listar Rutas Registradas

Para ver todas las rutas del sistema con sus nombres y controladores:

```
php artisan route:list
```

Para filtrar por nombre:

```
php artisan route:list --name=certificados
php artisan route:list --name=plantillas
```

### Probar con Tinker

Tinker es una consola interactiva para ejecutar codigo PHP directamente:

```
php artisan tinker
```

Ejemplos de uso:

```php
// Ver cuantas plantillas activas hay
App\Models\PlantillaPdf::where('activa', true)->count();

// Ver los datos de un certificado especifico
App\Models\Certificado::with('alumno', 'cohorte.curso')->first();

// Verificar el estado de las migraciones
// (sal de tinker y ejecuta en terminal)
php artisan migrate:status
```

---

## Solucion de Problemas Comunes

### El PDF se genera en blanco

- Verifica que la plantilla tiene contenido HTML guardado.
- Verifica que el certificado o la cohorte tiene una plantilla asignada.
- Revisa que las imagenes de fondo existen en `storage/app/public/plantillas-pdf/fondos/`.

### Las imagenes de fondo no aparecen

- Ejecuta `php artisan storage:link` si no lo has hecho.
- Verifica que las imagenes tienen formato JPG, JPEG, PNG o WEBP.
- El tamano maximo por imagen es 5 MB.

### Error "Class not found"

- Ejecuta `composer dump-autoload` para regenerar el autoloader.
- Verifica que el namespace en el archivo PHP coincida con la ruta del archivo.

### Error en las migraciones

- Si una migracion falla, revisa el estado con `php artisan migrate:status`.
- Para rehacer todo desde cero (borra todos los datos):

```
php artisan migrate:fresh --seed
```

### Las vistas no reflejan los cambios

```
php artisan view:clear
```

---

## Notas para Produccion

- Cambia `APP_ENV=production` y `APP_DEBUG=false` en el archivo `.env`.
- Ejecuta `npm run build` para generar assets optimizados.
- Ejecuta `php artisan optimize` para cachear configuracion, rutas y vistas.
- Configura un servidor web como Nginx o Apache apuntando a la carpeta `public/`.
- Asegurate de que la carpeta `storage/` y `bootstrap/cache/` tengan permisos de escritura.
