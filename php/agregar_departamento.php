<?php
// agregar_departamento.php
include 'conexion_base.php';

// Recibimos los datos del formulario
$nombre = $_POST['nombre_departamento'];
$telefono = $_POST['telefono'];
$folio = $_POST['folio'];
$folio_interno = $_POST['folio_interno'];
$nombre_encargado = $_POST['nombre_encargado'];
$cargo = $_POST['cargo'];
$domicilio = $_POST['domicilio'];
$cct = $_POST['cct'];

// Insertamos en la base (id_departamento es auto_increment)
$stmt = $conn->prepare("INSERT INTO ct_departamentos 
    (nombre_departamento, telefono, folio, folio_interno, nombre_encargado, cargo, domicilio, cct)
    VALUES (:nombre, :telefono, :folio, :folio_interno, :nombre_encargado, :cargo, :domicilio, :cct)");

$stmt->execute([
    ':nombre' => $nombre,
    ':telefono' => $telefono,
    ':folio' => $folio,
    ':folio_interno' => $folio_interno,
    ':nombre_encargado' => $nombre_encargado,
    ':cargo' => $cargo,
    ':domicilio' => $domicilio,
    ':cct' => $cct
]);

// Redirigimos de nuevo a consultar con bandera de éxito para mostrar toast
header("Location: index.php?page=consultar&saved=1");
?>
