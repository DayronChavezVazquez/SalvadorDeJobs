<?php
$host = "db";    // Servidor MySQL
$user = "root";         // Usuario de MySQL
$password = "";         // Contraseña de MySQL
$database = "prueba_php";   // Nombre de tu base de datos

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
