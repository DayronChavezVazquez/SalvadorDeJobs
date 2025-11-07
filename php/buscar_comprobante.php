<?php
/**
 * buscar_comprobante.php
 * 
 * Este archivo procesa la búsqueda de comprobantes por teléfono, mes y año.
 * Retorna un JSON con la información del comprobante si existe.
 */

header('Content-Type: application/json; charset=utf-8');

include 'conexion_base.php';

// Obtener parámetros
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$mes = isset($_POST['mes']) ? trim($_POST['mes']) : '';
$anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';

// Validar que todos los parámetros estén presentes
if (empty($telefono) || empty($mes) || empty($anio)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son requeridos'
    ]);
    exit;
}

try {
    // Buscar el departamento por teléfono
    $stmt_dept = $conn->prepare("SELECT * FROM ct_departamentos WHERE telefono = :telefono LIMIT 1");
    $stmt_dept->execute([':telefono' => $telefono]);
    $departamento = $stmt_dept->fetch(PDO::FETCH_ASSOC);
    
    if (!$departamento) {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró ningún departamento con ese teléfono'
        ]);
        exit;
    }
    
    // Buscar el comprobante por id_departamento, mes y año
    $stmt_comp = $conn->prepare("SELECT * FROM Pagos 
                                  WHERE id_departamento = :id_departamento 
                                  AND mes_pago = :mes 
                                  AND año_pago = :anio 
                                  LIMIT 1");
    $stmt_comp->execute([
        ':id_departamento' => $departamento['id_departamento'],
        ':mes' => $mes,
        ':anio' => $anio
    ]);
    $comprobante = $stmt_comp->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si existe comprobante y si tiene total_pagar válido
    $tiene_comprobante = false;
    if ($comprobante) {
        // Verificar que total_pagar exista, no sea NULL, y sea mayor que 0
        $total_pagar = isset($comprobante['total_pagar']) ? floatval($comprobante['total_pagar']) : 0;
        if ($total_pagar > 0) {
            $tiene_comprobante = true;
        }
    }
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'tiene_comprobante' => $tiene_comprobante,
        'departamento' => [
            'id_departamento' => $departamento['id_departamento'],
            'nombre_departamento' => $departamento['nombre_departamento'],
            'telefono' => $departamento['telefono'],
            'cct' => $departamento['cct'] ?? 'N/A',
            'folio' => $departamento['folio'] ?? 'N/A'
        ]
    ];
    
    if ($tiene_comprobante) {
        $response['comprobante'] = [
            'id_pago' => $comprobante['id_pago'],
            'total_pagar' => $comprobante['total_pagar'],
            'mes_pago' => $comprobante['mes_pago'],
            'año_pago' => $comprobante['año_pago']
        ];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
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

