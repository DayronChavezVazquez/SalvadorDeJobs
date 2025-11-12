# 📋 Sistema de Gestión de Comprobantes Telmex

Sistema web desarrollado en PHP para la gestión y generación de comprobantes de pago Telmex para departamentos educativos. Permite consultar, generar y descargar comprobantes en formato PDF.

## 🎯 Características Principales

- ✅ **Gestión de Departamentos**: Consultar, agregar, editar y eliminar departamentos
- ✅ **Generación de Comprobantes**: Crear comprobantes individuales por departamento
- ✅ **Consulta de Comprobantes**: Buscar comprobantes por teléfono o CCT
- ✅ **Consulta General**: Ver todos los comprobantes de un período específico
- ✅ **Generación de PDFs**: Exportar comprobantes en formato PDF profesional
- ✅ **Búsqueda Dinámica**: Años disponibles según datos en la base de datos
- ✅ **Interfaz Moderna**: Diseño responsive con Bootstrap 5

## 📁 Estructura del Proyecto

```
SalvadorDeJobs/
├── docker-compose.yml          # Configuración de Docker
├── README.md                   # Este archivo
├── GUIA_USUARIO.md            # Guía de uso para usuarios finales
├── GUIA_DESARROLLADOR.md      # Guía técnica para desarrolladores
└── php/                       # Código fuente de la aplicación
    ├── index.php              # Página principal (router)
    ├── conexion_base.php      # Configuración de base de datos
    ├── consultar.php          # Consulta de departamentos
    ├── agregar.php            # Formulario agregar departamento
    ├── editar.php             # Formulario editar departamento
    ├── comprobante.php        # Generar comprobante individual
    ├── consulta_comprobante.php           # Consulta por teléfono/CCT
    ├── consulta_comprobante_general.php    # Consulta general
    ├── pdf_escuela.php        # Generador PDF individual
    ├── pdf_comprobante_general.php        # Generador PDF general
    ├── buscar_comprobante.php             # API búsqueda individual
    ├── buscar_comprobante_general.php     # API búsqueda general
    ├── obtener_anios_disponibles.php      # API años disponibles
    ├── crear_tabla_comprobantes.sql       # Script SQL
    ├── css/
    │   └── estilos.css        # Estilos personalizados
    ├── js/
    │   └── scripts.js         # JavaScript personalizado
    └── imagenes/               # Logos e imágenes
```

## 🚀 Inicio Rápido

### Requisitos Previos

- Docker y Docker Compose instalados
- Navegador web moderno

### Instalación con Docker (Recomendado)

1. **Clonar o descargar el proyecto**
   ```bash
   cd SalvadorDeJobs
   ```

2. **Iniciar los contenedores**
   ```bash
   docker-compose up -d
   ```

3. **Acceder a la aplicación**
   - Aplicación: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

4. **Crear la base de datos**
   - Abre phpMyAdmin (http://localhost:8081)
   - La base de datos `prueba_php` se crea automáticamente
   - Ejecuta el script `crear_tabla_comprobantes.sql` desde phpMyAdmin

5. **Verificar instalación**
   - Abre http://localhost:8080
   - Deberías ver la interfaz principal

### Instalación Manual (Sin Docker)

Si prefieres instalar sin Docker, consulta [GUIA_DESARROLLADOR.md](GUIA_DESARROLLADOR.md) para instrucciones detalladas.

## 📖 Documentación

- **[GUIA_USUARIO.md](GUIA_USUARIO.md)**: Guía completa de uso para usuarios finales
- **[GUIA_DESARROLLADOR.md](GUIA_DESARROLLADOR.md)**: Documentación técnica para desarrolladores
- **[php/INSTRUCCIONES_INSTALACION.md](php/INSTRUCCIONES_INSTALACION.md)**: Instrucciones de instalación detalladas

## 🎓 Para Principiantes

Si eres nuevo en el proyecto, sigue estos pasos:

1. **Lee este README** para entender qué hace el sistema
2. **Revisa GUIA_USUARIO.md** para aprender a usar la aplicación
3. **Consulta GUIA_DESARROLLADOR.md** si quieres modificar el código
4. **Explora la estructura** de archivos para familiarizarte

## 🔧 Configuración

### Base de Datos

La configuración de la base de datos está en `php/conexion_base.php`:

```php
$host = "db";           // Servidor (en Docker usa "db")
$user = "root";         // Usuario MySQL
$password = "";         // Contraseña (vacía por defecto)
$database = "prueba_php"; // Nombre de la base de datos
```

### Tablas Requeridas

El sistema necesita estas tablas:
- `ct_departamentos`: Almacena información de departamentos
- `Pagos`: Almacena los pagos/comprobantes generados

## 📱 Uso Básico

### 1. Consultar Departamentos
- Menú: "Consultar escuelas"
- Permite ver, editar y eliminar departamentos

### 2. Generar Comprobante
- Menú: "Generar comprobante"
- Selecciona departamento, mes, año y cantidad
- Genera PDF del comprobante

### 3. Consultar Comprobante Individual
- Menú: "Consulta de comprobante Telmex"
- Busca por teléfono o CCT
- Descarga el comprobante si existe

### 4. Consultar Comprobantes Generales
- Menú: "Consulta de comprobante general"
- Ver todos los comprobantes de un período
- Descargar PDF general

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verifica que Docker esté corriendo: `docker-compose ps`
- Revisa `php/conexion_base.php`
- Verifica que el contenedor `db` esté activo

### No se generan PDFs
- Verifica que FPDF esté instalado
- Revisa permisos de escritura
- Consulta los logs: `docker-compose logs web`

### Página en blanco
- Revisa errores de PHP en los logs
- Verifica que todos los archivos estén presentes
- Comprueba permisos de archivos

## 🔒 Seguridad

⚠️ **Importante**: Este es un sistema de desarrollo. Para producción:
- Cambia las contraseñas por defecto
- Configura autenticación de usuarios
- Implementa validación de entrada
- Usa HTTPS

## 📞 Soporte

Para problemas o preguntas:
1. Revisa la documentación en `GUIA_USUARIO.md` y `GUIA_DESARROLLADOR.md`
2. Consulta los logs de Docker
3. Verifica la configuración de la base de datos

## 📝 Licencia

Este proyecto es de uso interno.

## 🔄 Actualizaciones

### Versión Actual
- Búsqueda por CCT además de teléfono
- Años dinámicos según datos disponibles
- Mejoras en la interfaz de usuario

---

**¿Necesitas ayuda?** Consulta las guías en la carpeta del proyecto o revisa los comentarios en el código.

