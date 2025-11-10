<?php
header('Content-Type: application/json; charset=utf-8');

include 'conexion_base.php';

$mes = isset($_POST['mes']) ? trim($_POST['mes']) : '';
$anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';

if (empty($mes) || empty($anio)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son requeridos'
    ]);
    exit;
}

try {
    // Primero verificar si hay registros en la tabla Pagos para ese mes y año
    $stmt_pagos = $conn->prepare("SELECT COUNT(*) as total FROM Pagos WHERE mes_pago = :mes AND año_pago = :anio");
    $stmt_pagos->execute([
        ':mes' => $mes,
        ':anio' => $anio
    ]);
    $pagos_count = $stmt_pagos->fetch(PDO::FETCH_ASSOC);
    
    // Si no hay registros en Pagos para ese mes y año, retornar alerta
    if ($pagos_count && (int)$pagos_count['total'] == 0) {
        echo json_encode([
            'success' => true,
            'departamentos' => [],
            'total_general' => 0,
            'mes' => $mes,
            'anio' => $anio,
            'sin_registros_pagos' => true,
            'message' => 'No hay registros para el mes y año seleccionados.'
        ]);
        exit;
    }
    
    $stmt = $conn->prepare("
        SELECT 
            d.id_departamento,
            d.folio,
            d.nombre_departamento,
            d.telefono,
            d.cct,
            COALESCE(p.total_pagar, 0) AS total_pagar
        FROM ct_departamentos d
        LEFT JOIN Pagos p 
            ON p.id_departamento = d.id_departamento
            AND p.mes_pago = :mes
            AND p.año_pago = :anio
        ORDER BY d.id_departamento ASC
    ");

    $stmt->execute([
        ':mes' => $mes,
        ':anio' => $anio
    ]);

    $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$departamentos || count($departamentos) == 0) {
        echo json_encode([
            'success' => true,
            'departamentos' => [],
            'total_general' => 0,
            'mes' => $mes,
            'anio' => $anio,
            'sin_registros' => true,
            'message' => 'No se cuenta con información registrada para el mes y año seleccionados.'
        ]);
        exit;
    }

    $total_general = 0;
    foreach ($departamentos as &$departamento) {
        $total = isset($departamento['total_pagar']) ? (float)$departamento['total_pagar'] : 0;
        $departamento['total_pagar'] = $total;
        $total_general += $total;
    }
    unset($departamento);

    echo json_encode([
        'success' => true,
        'departamentos' => $departamentos,
        'total_general' => $total_general,
        'mes' => $mes,
        'anio' => $anio
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al realizar la búsqueda: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>


