# Instrucciones de Instalación - Sistema de Comprobantes

## 📋 Requisitos Previos

Para que el sistema de generación de comprobantes funcione correctamente, necesitas:

1. **PHP 7.4 o superior**
2. **MySQL/MariaDB**
3. **Librería FPDF** para generar PDFs

---

## 🔧 Paso 1: Crear la Tabla de Comprobantes

Ejecuta el script SQL en tu base de datos MySQL:

```bash
# Opción 1: Desde la línea de comandos MySQL
mysql -u root -p prueba_php < crear_tabla_comprobantes.sql

# Opción 2: Desde phpMyAdmin o cualquier cliente MySQL
# Abre el archivo crear_tabla_comprobantes.sql y ejecuta su contenido
```

Este script creará la tabla `comprobantes` con todas las restricciones necesarias, incluyendo la validación que evita duplicados por mes y año.

---

## 📦 Paso 2: Instalar FPDF

Tienes dos opciones para instalar FPDF:

### Opción A: Usando Composer (Recomendado)

1. Asegúrate de tener Composer instalado
2. En la carpeta `php`, ejecuta:

```bash
composer require setasign/fpdf
```

3. Luego, en el archivo `pdf_escuela.php`, descomenta esta línea (quita los `//`):

```php
require_once __DIR__ . '/vendor/autoload.php';
```

### Opción B: Descarga Manual

1. Descarga FPDF desde: http://www.fpdf.org/
2. Extrae el archivo `fpdf.php`
3. Colócalo en la carpeta `php`
4. En el archivo `pdf_escuela.php`, descomenta esta línea:

```php
require_once('fpdf.php');
```

---

## ✅ Paso 3: Verificar que Todo Funcione

1. **Verifica la tabla**: Asegúrate de que la tabla `comprobantes` se creó correctamente
2. **Verifica FPDF**: Intenta generar un comprobante de prueba
3. **Prueba la validación**: Intenta crear dos comprobantes para el mismo departamento, mes y año (debería mostrar un error)

---

## 🐛 Solución de Problemas

### Error: "Class 'FPDF' not found"
- **Solución**: Asegúrate de que FPDF está instalado correctamente (ver Paso 2)

### Error: "Table 'comprobantes' doesn't exist"
- **Solución**: Ejecuta el script SQL `crear_tabla_comprobantes.sql`

### Error: "Ya existe un comprobante para este mes y año"
- **Esto es normal**: El sistema está funcionando correctamente. No se pueden crear dos comprobantes para el mismo departamento, mes y año.

---

## 📝 Estructura del PDF Generado

El PDF incluye:

1. **Folio** (parte superior)
2. **Título**: "COMPROBANTE DE PAGO TELMEX"
3. **Datos del Departamento**:
   - Nombre del Departamento
   - Teléfono
4. **Datos del Firmante**:
   - Nombre del Firmante
   - Puesto del Firmante
5. **Datos del Período**:
   - Mes
   - Año
   - Cantidad a Pagar
6. **Dirección** (al final de los datos)
7. **Área de Firma** (lado izquierdo)
8. **Recuadro para Sello** (3cm alto x 6cm ancho, lado derecho)

---

## 📞 Soporte

Si tienes problemas, verifica:
- Los logs de PHP
- Los logs de MySQL
- Que todos los archivos estén en su lugar correcto



