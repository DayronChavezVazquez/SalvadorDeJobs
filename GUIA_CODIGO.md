# 📚 Guía de Código - Sistema de Comprobantes Telmex

Esta guía explica cómo funciona el código del proyecto para que cualquier desarrollador, incluso principiante, pueda entenderlo y modificarlo.

## 📋 Tabla de Contenidos

1. [Estructura del Proyecto](#estructura-del-proyecto)
2. [Archivos Principales](#archivos-principales)
3. [Flujo de Datos](#flujo-de-datos)
4. [Cómo Modificar el Código](#cómo-modificar-el-código)
5. [Conceptos Importantes](#conceptos-importantes)

---

## 🏗️ Estructura del Proyecto

```
php/
├── index.php                    # Página principal y router
├── conexion_base.php            # Configuración de base de datos
├── consultar.php                # Lista de departamentos
├── agregar.php                  # Formulario agregar departamento
├── editar.php                   # Formulario editar departamento
├── comprobante.php              # Generar comprobante individual
├── consulta_comprobante.php     # Consultar comprobante por teléfono/CCT
├── consulta_comprobante_general.php  # Consultar todos los comprobantes
├── buscar_comprobante.php        # API: buscar comprobante individual
├── buscar_comprobante_general.php      # API: buscar comprobantes generales
├── obtener_anios_disponibles.php      # API: obtener años disponibles
├── generar_comprobante.php      # Procesar generación de comprobante
├── pdf_escuela.php              # Generar PDF individual
├── pdf_comprobante_general.php  # Generar PDF general
└── eliminar_departamento.php   # Eliminar departamento
```

---

## 📄 Archivos Principales

### 1. `index.php` - Router Principal

**¿Qué hace?**
- Es el punto de entrada de la aplicación
- Decide qué página mostrar según la URL
- Incluye el menú lateral y la estructura HTML base

**¿Cómo funciona?**
```php
// Si visitas: index.php?page=consultar
// Muestra: consultar.php

// Si visitas: index.php?page=comprobante
// Muestra: comprobante.php
```

**¿Cómo agregar una nueva página?**
1. Crea tu archivo PHP (ej: `mi_pagina.php`)
2. En `index.php`, línea ~154, agrega:
```php
elseif ($page == 'mi_pagina') {
    include 'mi_pagina.php';
}
```
3. Agrega el enlace en el menú lateral (línea ~32)

---

### 2. `conexion_base.php` - Conexión a Base de Datos

**¿Qué hace?**
- Establece la conexión con MySQL/MariaDB
- Crea el objeto `$conn` que se usa en todo el proyecto

**Configuración:**
```php
$host = "db";           // En Docker usa "db", local usa "localhost"
$user = "root";         // Usuario MySQL
$password = "";         // Contraseña (vacía en desarrollo)
$database = "prueba_php"; // Nombre de la base de datos
```

**Uso en otros archivos:**
```php
include 'conexion_base.php';
// Ahora puedes usar $conn para hacer consultas
$stmt = $conn->prepare("SELECT * FROM tabla");
```

---

### 3. Archivos de Consulta (API)

#### `buscar_comprobante.php`

**¿Qué hace?**
- Recibe datos del formulario (teléfono/CCT, mes, año)
- Busca el departamento en la base de datos
- Busca el comprobante asociado
- Retorna JSON con los resultados

**Flujo:**
1. Recibe `$_POST['telefono']` o `$_POST['cct']`
2. Busca en `ct_departamentos`
3. Busca en `Pagos` usando `id_departamento`, `mes_pago`, `año_pago`
4. Retorna JSON con `success`, `tiene_comprobante`, `departamento`, `comprobante`

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "tiene_comprobante": true,
  "departamento": {
    "id_departamento": 1,
    "nombre_departamento": "Escuela Primaria",
    "telefono": "1234567890"
  },
  "comprobante": {
    "id_pago": 5,
    "total_pagar": "1500.00",
    "mes_pago": "Enero",
    "año_pago": 2024
  }
}
```

#### `obtener_anios_disponibles.php`

**¿Qué hace?**
- Consulta la tabla `Pagos` para obtener años únicos
- Retorna los años en los que hay comprobantes
- Si no hay datos, retorna solo el año actual

**Uso:**
- Se llama desde JavaScript cuando se carga la página
- Pobla dinámicamente los selectores de año

---

### 4. Archivos de Generación de PDF

#### `pdf_escuela.php` - PDF Individual

**¿Qué hace?**
- Genera un PDF para un comprobante individual
- Usa la librería FPDF
- Incluye logos, datos del departamento y área de firma

**Parámetros:**
- `id_pago`: ID del pago
- `id_departamento`: ID del departamento

**Modificar diseño:**
- Línea ~214: Posición y tamaño de imagen izquierda
- Línea ~222: Posición y tamaño de imagen derecha
- Líneas siguientes: Texto, tablas, etc.

#### `pdf_comprobante_general.php` - PDF General

**¿Qué hace?**
- Genera un PDF con todos los comprobantes de un período
- Formato horizontal (landscape)
- Tabla con todos los departamentos

**Parámetros:**
- `mes`: Mes del período
- `anio`: Año del período

---

## 🔄 Flujo de Datos

### Flujo de Búsqueda de Comprobante

```
1. Usuario llena formulario (consulta_comprobante.php)
   ↓
2. JavaScript envía datos vía AJAX
   ↓
3. buscar_comprobante.php recibe datos
   ↓
4. Busca departamento en ct_departamentos
   ↓
5. Busca comprobante en Pagos
   ↓
6. Retorna JSON
   ↓
7. JavaScript muestra resultados en la página
```

### Flujo de Generación de PDF

```
1. Usuario genera comprobante (comprobante.php)
   ↓
2. generar_comprobante.php guarda en base de datos
   ↓
3. Redirige a pdf_escuela.php
   ↓
4. pdf_escuela.php consulta datos
   ↓
5. Genera PDF usando FPDF
   ↓
6. Descarga automática del PDF
```

---

## 🛠️ Cómo Modificar el Código

### Agregar un Nuevo Campo a la Búsqueda

**Ejemplo: Agregar búsqueda por folio**

1. **Modificar el formulario** (`consulta_comprobante.php`):
```html
<input type="text" name="folio" id="folio" placeholder="Folio">
```

2. **Modificar la API** (`buscar_comprobante.php`):
```php
$folio = isset($_POST['folio']) ? trim($_POST['folio']) : '';

if (!empty($folio)) {
    $stmt_dept = $conn->prepare("SELECT * FROM ct_departamentos WHERE folio = :folio LIMIT 1");
    $stmt_dept->execute([':folio' => $folio]);
    $departamento = $stmt_dept->fetch(PDO::FETCH_ASSOC);
}
```

### Cambiar el Diseño del PDF

**Ejemplo: Cambiar tamaño de imágenes**

En `pdf_comprobante_general.php`, línea ~204:
```php
// Cambiar de 70mm a 80mm de ancho
$pdf->Image($imagen_izq, 10, $y_sepe, 80, 0);
```

**Parámetros de Image():**
- `10` = posición X (izquierda)
- `$y_sepe` = posición Y (arriba)
- `80` = ancho en milímetros
- `0` = alto (0 = mantener proporción)

### Agregar una Nueva Columna a una Tabla

**Ejemplo: Agregar columna "Estado"**

1. **Modificar la consulta SQL**:
```php
$stmt = $conn->prepare("SELECT *, estado FROM ct_departamentos ...");
```

2. **Mostrar en la tabla HTML**:
```html
<td><?= $dep['estado'] ?></td>
```

---

## 💡 Conceptos Importantes

### PDO (PHP Data Objects)

**¿Qué es?**
- Método seguro para conectarse a bases de datos
- Previene inyección SQL

**Ejemplo básico:**
```php
// Preparar consulta
$stmt = $conn->prepare("SELECT * FROM tabla WHERE id = :id");

// Ejecutar con parámetros
$stmt->execute([':id' => 123]);

// Obtener resultados
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
```

### AJAX (JavaScript)

**¿Qué es?**
- Envía datos al servidor sin recargar la página
- Usa jQuery en este proyecto

**Ejemplo:**
```javascript
$.ajax({
    url: 'buscar_comprobante.php',
    type: 'POST',
    data: { telefono: '1234567890', mes: 'Enero', anio: 2024 },
    success: function(response) {
        // response es el JSON retornado
        console.log(response);
    }
});
```

### JSON (JavaScript Object Notation)

**¿Qué es?**
- Formato para intercambiar datos
- Fácil de leer y escribir

**Ejemplo:**
```json
{
  "success": true,
  "mensaje": "Operación exitosa",
  "datos": {
    "id": 1,
    "nombre": "Ejemplo"
  }
}
```

### FPDF (Generación de PDFs)

**¿Qué es?**
- Librería PHP para generar PDFs
- Permite crear documentos desde código

**Conceptos básicos:**
- `AddPage()`: Agregar nueva página
- `SetFont()`: Cambiar fuente
- `Cell()`: Agregar celda de texto
- `Image()`: Agregar imagen
- `SetXY()`: Posicionar cursor

---

## 🐛 Solución de Problemas Comunes

### Error: "Conexión fallida"

**Causa:** Base de datos no disponible o configuración incorrecta

**Solución:**
1. Verifica que Docker esté corriendo: `docker-compose ps`
2. Revisa `conexion_base.php`
3. Verifica que el contenedor `db` esté activo

### Error: "Class 'FPDF' not found"

**Causa:** FPDF no está instalado

**Solución:**
1. Instala FPDF: `composer require setasign/fpdf`
2. O descarga `fpdf.php` manualmente

### Error: "Undefined variable"

**Causa:** Variable no definida antes de usarse

**Solución:**
```php
// Antes de usar, verifica que exista
$variable = isset($_POST['campo']) ? $_POST['campo'] : '';
```

### PDF no se genera

**Causa:** Error en el código o datos faltantes

**Solución:**
1. Revisa los logs de PHP
2. Verifica que todos los datos estén presentes
3. Asegúrate de que las imágenes existan en `imagenes/`

---

## 📝 Buenas Prácticas

1. **Siempre valida datos de entrada:**
```php
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
```

2. **Usa prepared statements (PDO):**
```php
$stmt = $conn->prepare("SELECT * FROM tabla WHERE id = :id");
$stmt->execute([':id' => $id]);
```

3. **Maneja errores:**
```php
try {
    // Código que puede fallar
} catch (Exception $e) {
    // Manejar error
}
```

4. **Comenta tu código:**
```php
// Esto hace X porque Y
```

5. **Usa nombres descriptivos:**
```php
// ❌ Mal
$d = $r['n'];

// ✅ Bien
$departamento = $resultado['nombre'];
```

---

## 🎓 Recursos Adicionales

- **PDO:** https://www.php.net/manual/es/book.pdo.php
- **FPDF:** http://www.fpdf.org/
- **jQuery AJAX:** https://api.jquery.com/jquery.ajax/
- **Bootstrap 5:** https://getbootstrap.com/docs/5.3/

---

**¿Tienes dudas?** Revisa los comentarios en el código o consulta la documentación oficial de PHP, PDO y FPDF.

