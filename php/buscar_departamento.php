<?php
// buscar_departamento.php
include 'conexion_base.php';

$folio = $_GET['folio'] ?? '';
$cct = $_GET['cct'] ?? '';

if($folio != '') {
    $stmt = $conn->prepare("SELECT * FROM ct_departamentos WHERE folio = :folio LIMIT 1");
    $stmt->execute([':folio'=>$folio]);
} elseif($cct != '') {
    $stmt = $conn->prepare("SELECT * FROM ct_departamentos WHERE cct = :cct LIMIT 1");
    $stmt->execute([':cct'=>$cct]);
} else {
    echo json_encode(null);
    exit;
}

$data = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($data);
?>
