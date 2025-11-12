<?php
/**
 * ============================================
 * conexion_base.php - CONFIGURACIÓN DE BASE DE DATOS
 * ============================================
 * 
 * Este archivo establece la conexión con la base de datos MySQL/MariaDB.
 * Se incluye en todos los archivos que necesitan acceder a la base de datos.
 * 
 * ¿QUÉ HACE ESTE ARCHIVO?
 * - Define los datos de conexión (servidor, usuario, contraseña, base de datos)
 * - Crea un objeto PDO para interactuar con MySQL
 * - Configura el manejo de errores
 * 
 * ¿CÓMO MODIFICAR LA CONEXIÓN?
 * - Si usas Docker: deja "db" como host (nombre del contenedor)
 * - Si usas instalación local: cambia "db" por "localhost"
 * - Modifica $user, $password y $database según tu configuración
 * 
 * IMPORTANTE: Si cambias estos valores, asegúrate de que coincidan
 * con tu configuración de Docker o MySQL.
 */

// ============================================
// CONFIGURACIÓN DE CONEXIÓN
// ============================================

// Servidor de base de datos
// - "db" = nombre del contenedor Docker (cuando usas docker-compose)
// - "localhost" = si instalas MySQL directamente en tu computadora
$host = "db";

// Usuario de MySQL (por defecto 'root' en desarrollo)
$user = "root";

// Contraseña de MySQL (vacía por defecto en Docker)
// ⚠️ IMPORTANTE: En producción, usa una contraseña segura
$password = "";

// Nombre de la base de datos
// Debe coincidir con el nombre en docker-compose.yml (línea 19)
$database = "prueba_php";

// ============================================
// ESTABLECER CONEXIÓN
// ============================================

try {
    /**
     * Crear conexión usando PDO (PHP Data Objects)
     * PDO es una forma segura y moderna de conectarse a bases de datos
     * 
     * Parámetros del constructor:
     * 1. "mysql:host=...;dbname=...;charset=..." = cadena de conexión
     *    - mysql: = tipo de base de datos
     *    - host = servidor donde está MySQL
     *    - dbname = nombre de la base de datos
     *    - charset=utf8mb4 = codificación de caracteres (soporta emojis y caracteres especiales)
     * 2. $user = usuario de MySQL
     * 3. $password = contraseña de MySQL
     */
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    
    /**
     * Configurar el modo de error de PDO
     * PDO::ERRMODE_EXCEPTION = lanza excepciones cuando hay errores
     * Esto hace más fácil detectar y solucionar problemas
     */
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Si llegamos aquí, la conexión fue exitosa
    // La variable $conn está disponible para usar en otros archivos
    
} catch (PDOException $e) {
    /**
     * Si hay un error al conectar, mostrar mensaje y detener la ejecución
     * PDOException = tipo de error específico de PDO
     * $e->getMessage() = mensaje descriptivo del error
     */
    die("Conexión fallida: " . $e->getMessage());
    
    /**
     * POSIBLES ERRORES Y SOLUCIONES:
     * 
     * "Unknown database 'prueba_php'"
     * → La base de datos no existe. Crea la base de datos o verifica el nombre.
     * 
     * "Access denied for user 'root'@'localhost'"
     * → Usuario o contraseña incorrectos. Verifica $user y $password.
     * 
     * "Connection refused" o "Can't connect to MySQL server"
     * → MySQL no está corriendo. Inicia Docker o el servicio MySQL.
     * 
     * "SQLSTATE[HY000] [2002] No such file or directory"
     * → En Docker, cambia "localhost" por "db" en $host.
     */
}
