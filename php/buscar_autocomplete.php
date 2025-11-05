<?php
// buscar_autocomplete.php
include 'conexion_base.php';

$tipo = $_GET['tipo'] ?? ''; // 'nombre' o 'telefono'
$term = $_GET['term'] ?? '';

if (empty($term) || empty($tipo)) {
    echo json_encode([]);
    exit;
}

$results = [];
if ($tipo === 'nombre') {
    $stmt = $conn->prepare("SELECT DISTINCT nombre_departamento FROM ct_departamentos WHERE nombre_departamento LIKE :term LIMIT 10");
    $stmt->execute([':term' => "%$term%"]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
} elseif ($tipo === 'telefono') {
    $stmt = $conn->prepare("SELECT DISTINCT telefono FROM ct_departamentos WHERE telefono LIKE :term AND telefono IS NOT NULL AND telefono != '' LIMIT 10");
    $stmt->execute([':term' => "%$term%"]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

echo json_encode($results);

