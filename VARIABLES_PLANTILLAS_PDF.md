# Variables de Plantillas PDF

## Objetivo

Este documento define la lista oficial de variables soportadas en las plantillas PDF
(`PlantillaPdf`) y describe como se sustituyen en el HTML al generar certificados.

Tambien explica como agregar nuevas variables de forma segura, manteniendo consistencia
entre:

- `App\Models\PlantillaPdf::variablesDisponibles()` (lista visible en el editor)
- `App\Models\PlantillaPdf::datosDePrueba()` (valores para preview)
- `App\Services\CertificadoPdfService::extraerDatos()` (datos reales)
- `App\Services\CertificadoPdfService::extraerDatos()` `$defaults` (valores por defecto)

---

## Donde se guardan las variables

Las variables **no se guardan en una tabla propia**. Lo que se guarda es el **HTML**
de la plantilla (con los placeholders) y el sistema sustituye esos placeholders al generar
el PDF.

### En base de datos

Tabla: `plantillas_pdf`

- `contenido_html`: Guarda el HTML con las variables escritas como `{{ $variable }}`.
- `estilos_css`: Guarda el CSS de la plantilla.

Las variables como lista (catalogo) **no se guardan en BD**. Solo existe el texto
del HTML que el usuario escribio.

### En codigo

- Catalogo de variables mostradas en el editor:
  - `app/Models/PlantillaPdf.php` -> `PlantillaPdf::variablesDisponibles()`

- Datos de prueba para preview:
  - `app/Models/PlantillaPdf.php` -> `PlantillaPdf::datosDePrueba()`

- Extraccion de datos reales desde el certificado:
  - `app/Services/CertificadoPdfService.php` -> `CertificadoPdfService::extraerDatos()`

- Valores por defecto cuando un dato real viene vacio:
  - `app/Services/CertificadoPdfService.php` -> `CertificadoPdfService::extraerDatos()` -> `$defaults`

---

## Formato de Variables en el HTML

Las variables se escriben dentro del HTML de la plantilla usando la sintaxis:

- `{{ $nombre_variable }}`

El sistema soporta dos formatos equivalentes:

- `{{ $nombre_variable }}`
- `{{$nombre_variable}}`

Recomendacion: usar siempre el formato con espacios `{{ $variable }}` para mejor legibilidad.

---

## Como funciona la sustitucion de variables

La sustitucion ocurre dentro de `CertificadoPdfService`:

1. Se determina la plantilla a usar con esta prioridad:
   - Plantilla asignada al certificado (certificados.plantilla_pdf_id)
   - Plantilla asignada a la cohorte (cohortes.plantilla_pdf_id)
   - Plantilla predeterminada activa (plantillas_pdf.es_predeterminada = true)

2. Se extraen datos del certificado y sus relaciones:
   - Alumno (`alumno`)
   - Cohorte y curso (`cohorte.curso`)
   - Firmas (`firma1`, `firma2`, `firma3`)

3. Se reemplazan las variables dentro del HTML.

4. Si quedan variables sin reemplazar, se eliminan automaticamente para que no aparezcan
   etiquetas crudas en el PDF final.

---

## Valores por defecto (cuando el dato esta vacio)

Si el valor real de una variable es vacio (`''`) o `null`, el sistema usa valores por defecto.
Esto evita que un PDF salga con campos vacios o con variables sin completar.

Los valores por defecto estan definidos en:

- `App\Services\CertificadoPdfService::extraerDatos()` dentro del array `$defaults`

---

## Lista oficial de variables disponibles

Las siguientes variables estan soportadas por el sistema. Se pueden usar en el HTML de cualquier
plantilla PDF.

### Datos del alumno

- `{{ $alumno_nombre }}`
- `{{ $alumno_identificacion }}`
- `{{ $alumno_email }}`

### Datos del curso

- `{{ $curso_nombre }}`
- `{{ $curso_horas }}`
- `{{ $curso_descripcion }}`

### Datos de la cohorte

- `{{ $cohorte_codigo }}`
- `{{ $cohorte_modalidad }}`
- `{{ $cohorte_fecha_inicio }}`
- `{{ $cohorte_fecha_fin }}`

### Datos del certificado

- `{{ $certificado_fecha_emision }}`
- `{{ $certificado_codigo }}`
- `{{ $certificado_libro }}`
- `{{ $certificado_folio }}`
- `{{ $certificado_registro }}`

### Firmas (autoridades)

Estas variables vienen de las relaciones `firma1`, `firma2`, `firma3` del certificado.
Si una firma no esta asignada, se sustituye por cadena vacia.

- `{{ $firma_1_nombre }}`
- `{{ $firma_1_cargo }}`
- `{{ $firma_2_nombre }}`
- `{{ $firma_2_cargo }}`
- `{{ $firma_3_nombre }}`
- `{{ $firma_3_cargo }}`

---

## Recomendaciones de uso dentro del HTML

### Texto normal

Ejemplo:

```html
<p>Se otorga a: <strong>{{ $alumno_nombre }}</strong></p>
<p>Por completar: {{ $curso_nombre }}</p>
```

### Evitar depender de valores vacios

Si una variable puede estar vacia (por ejemplo, `curso_descripcion` o alguna firma),
es recomendable maquetar el diseño para que un valor vacio no rompa el layout.

---

## Como agregar una nueva variable

Para agregar una nueva variable al sistema, se deben actualizar 4 puntos.

Regla principal: el nombre debe ser exactamente el mismo en los 4 puntos.
Ejemplo: si defines `curso_codigo_interno`, en el HTML debe usarse como `{{ $curso_codigo_interno }}`.

### 1) Agregar a la lista del editor

Archivo:

- `app/Models/PlantillaPdf.php`

Metodo:

- `variablesDisponibles()`

Accion:

- Agregar una nueva entrada en el array, por ejemplo:
  - `"certificado_registro" => "Codigo de registro manual"`

Este paso controla:

- Que la variable aparezca en el listado del editor.
- Que el usuario pueda insertarla facilmente desde la interfaz.

### 2) Agregar valor de prueba para preview

Archivo:

- `app/Models/PlantillaPdf.php`

Metodo:

- `datosDePrueba()`

Accion:

- Agregar un valor representativo, por ejemplo:
  - `"certificado_registro" => "REG-2026-0001"`

Este paso controla:

- El resultado del preview (vista previa) cuando no hay un certificado real.
- La experiencia en el editor al probar el diseno.

### 3) Extraer el dato real del certificado

Archivo:

- `app/Services/CertificadoPdfService.php`

Metodo:

- `extraerDatos()`

Accion:

- Agregar la nueva clave al array `$datos` con el valor real.

Este paso controla:

- Que el PDF real tenga el dato correcto al generar certificados.
- Que el valor venga de las relaciones correctas (alumno, cohorte, curso, firmas).

### 4) Definir el default

Archivo:

- `app/Services/CertificadoPdfService.php`

Metodo:

- `extraerDatos()`

Accion:

- Agregar la nueva clave al array `$defaults`.

Este paso controla:

- Que el PDF nunca quede con campos inesperadamente vacios cuando el dato real no existe.
- Que el diseno de la plantilla se mantenga estable incluso con informacion incompleta.

Esto asegura que:

- El editor muestre la variable
- El preview la sustituya
- El PDF real la sustituya
- Siempre exista un fallback si el dato real viene vacio

---

## Debug rapido de variables

Si en un PDF aparece un hueco donde esperabas una variable:

1. Verifica que la variable esta escrita exactamente (mismo nombre y con `$`).
2. Verifica que el certificado tiene cargadas las relaciones necesarias.
3. Verifica que el dato existe en DB (por ejemplo, el alumno tiene email, o la firma esta asignada).

Si ves texto del tipo `{{ $variable }}` en el resultado:

- El sistema deberia eliminar variables no sustituidas automaticamente. Si aparece,
  significa que la plantilla tiene una sintaxis que no coincide con los patrones soportados
  o contiene caracteres adicionales.

---

## Referencias en el codigo

- `app/Models/PlantillaPdf.php`
  - `variablesDisponibles()`
  - `datosDePrueba()`

- `app/Services/CertificadoPdfService.php`
  - `extraerDatos()`
  - `renderizarHtml()`
  - `generarPdf()`
  - `generarPdfMultiple()`
