<?php
// generar_comprobante.php
include 'conexion_base.php';

// Recibimos datos del formulario
$id_departamento = isset($_POST['id_departamento']) ? (int)$_POST['id_departamento'] : 0;
$mes = isset($_POST['mes']) ? trim($_POST['mes']) : '';
$anio = isset($_POST['anio']) ? (int)$_POST['anio'] : 0;
$cantidad = isset($_POST['cantidad']) ? floatval($_POST['cantidad']) : 0;
$nombre_firmante = isset($_POST['nombre_firmante']) ? trim($_POST['nombre_firmante']) : '';
$puesto_firmante = isset($_POST['puesto_firmante']) ? trim($_POST['puesto_firmante']) : '';

// Validar que todos los campos requeridos estén presentes
if ($id_departamento <= 0 || $mes == '' || $anio <= 0 || $cantidad <= 0) {
    echo "<script>alert('Error: Faltan datos requeridos'); window.location='index.php?page=comprobante';</script>";
    exit;
}

try {
    // Primero, verificar si ya existe un comprobante para este departamento, mes y año
    $stmt_check = $conn->prepare("SELECT id_pago FROM Pagos 
                                   WHERE id_departamento = :id_departamento 
                                   AND mes_pago = :mes 
                                   AND año_pago = :anio 
                                   LIMIT 1");
    $stmt_check->execute([
        ':id_departamento' => $id_departamento,
        ':mes' => $mes,
        ':anio' => $anio
    ]);
    
    $comprobante_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($comprobante_existente) {
        // Ya existe un comprobante para este mes y año
        echo "<script>alert('Error: Ya existe un comprobante para este departamento en el mes de $mes del año $anio. No se puede generar otro comprobante para el mismo período.'); window.location='index.php?page=comprobante';</script>";
        exit;
    }
    
    // Obtener datos del departamento para el PDF
    $stmt_dept = $conn->prepare("SELECT * FROM ct_departamentos WHERE id_departamento = :id LIMIT 1");
    $stmt_dept->execute([':id' => $id_departamento]);
    $departamento = $stmt_dept->fetch(PDO::FETCH_ASSOC);
    
    if (!$departamento) {
        echo "<script>alert('Error: Departamento no encontrado'); window.location='index.php?page=comprobante';</script>";
        exit;
    }
    
    // Insertar el comprobante en la base de datos (tabla Pagos)
    $stmt = $conn->prepare("INSERT INTO Pagos 
        (id_departamento, mes_pago, año_pago, total_pagar, nombre_firmante, cargo_firmante) 
        VALUES (:id_departamento, :mes, :anio, :cantidad, :nombre_firmante, :puesto_firmante)");
    
    $stmt->execute([
        ':id_departamento' => $id_departamento,
        ':mes' => $mes,
        ':anio' => $anio,
        ':cantidad' => $cantidad,
        ':nombre_firmante' => $nombre_firmante,
        ':puesto_firmante' => $puesto_firmante
    ]);
    
    // Obtener el ID del comprobante recién creado
    $id_pago = $conn->lastInsertId();
    
    // Redirigir a la generación del PDF con los datos necesarios
    $params = http_build_query([
        'id_pago' => $id_pago,
        'id_departamento' => $id_departamento
    ]);
    
    header("Location: pdf_escuela.php?$params");
    exit;
    
} catch (PDOException $e) {
    echo "<script>alert('Error al guardar el comprobante: " . addslashes($e->getMessage()) . "'); window.location='index.php?page=comprobante';</script>";
    exit;
}
?>
