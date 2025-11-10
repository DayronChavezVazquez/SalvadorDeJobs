<?php
include 'conexion_base.php';

$id = (int)$_POST['id'];
$nombre = $_POST['nombre_departamento'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$folio = $_POST['folio'] ?? '';
$folio_interno = $_POST['folio_interno'] ?? '';
$nombre_encargado = $_POST['nombre_encargado'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$domicilio = $_POST['domicilio'] ?? '';
$cct = isset($_POST['cct']) ? strtoupper(trim($_POST['cct'])) : '';

$stmt = $conn->prepare("UPDATE ct_departamentos SET 
    nombre_departamento = :nombre,
    telefono = :telefono,
    folio = :folio,
    folio_interno = :folio_interno,
    nombre_encargado = :nombre_encargado,
    cargo = :cargo,
    domicilio = :domicilio,
    cct = :cct
    WHERE id_departamento = :id");

$stmt->execute([
    ':id' => $id,
    ':nombre' => $nombre,
    ':telefono' => $telefono,
    ':folio' => $folio,
    ':folio_interno' => $folio_interno,
    ':nombre_encargado' => $nombre_encargado,
    ':cargo' => $cargo,
    ':domicilio' => $domicilio,
    ':cct' => $cct
]);

header('Location: index.php?page=consultar&updated=1');
exit;
?>

