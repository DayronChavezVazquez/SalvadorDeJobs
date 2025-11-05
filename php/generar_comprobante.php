<?php
// generar_comprobante.php
include 'conexion_base.php';

// Recibimos datos del formulario
$folio = $_POST['folio'];
$mes = $_POST['mes'];
$anio = $_POST['anio'];
$cantidad = $_POST['cantidad'];

// Aquí puedes guardar en otra tabla si deseas llevar historial
// Ejemplo: tabla 'comprobantes'
// $stmt = $conn->prepare("INSERT INTO comprobantes (folio, mes, anio, cantidad) VALUES (:folio, :mes, :anio, :cantidad)");
// $stmt->execute([...]);

echo "<script>alert('Comprobante generado para folio $folio'); window.location='index.php?page=comprobante';</script>";
?>
