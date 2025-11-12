<?php
/**
 * obtener_anios_disponibles.php
 * 
 * Este archivo retorna los años disponibles en la tabla Pagos.
 * Si hay datos de años anteriores, los incluye. Si no, solo retorna el año actual.
 */

header('Content-Type: application/json; charset=utf-8');

include 'conexion_base.php';

try {
    $anio_actual = (int)date('Y');
    
    // Obtener años únicos de la tabla Pagos
    $stmt = $conn->prepare("SELECT DISTINCT año_pago FROM Pagos WHERE año_pago IS NOT NULL ORDER BY año_pago DESC");
    $stmt->execute();
    $anios_db = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Convertir a enteros y filtrar valores válidos
    $anios_disponibles = [];
    foreach ($anios_db as $anio) {
        $anio_int = (int)$anio;
        if ($anio_int > 0 && $anio_int <= $anio_actual) {
            $anios_disponibles[] = $anio_int;
        }
    }
    
    // Si hay años en la BD, usarlos. Si no, solo el año actual
    if (empty($anios_disponibles)) {
        $anios_disponibles = [$anio_actual];
    } else {
        // Asegurar que el año actual esté incluido
        if (!in_array($anio_actual, $anios_disponibles)) {
            $anios_disponibles[] = $anio_actual;
            rsort($anios_disponibles); // Ordenar descendente
        }
    }
    
    // Eliminar duplicados y ordenar descendente
    $anios_disponibles = array_unique($anios_disponibles);
    rsort($anios_disponibles);
    
    echo json_encode([
        'success' => true,
        'anios' => $anios_disponibles,
        'anio_actual' => $anio_actual
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // En caso de error, retornar solo el año actual
    $anio_actual = (int)date('Y');
    echo json_encode([
        'success' => true,
        'anios' => [$anio_actual],
        'anio_actual' => $anio_actual
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $anio_actual = (int)date('Y');
    echo json_encode([
        'success' => true,
        'anios' => [$anio_actual],
        'anio_actual' => $anio_actual
    ], JSON_UNESCAPED_UNICODE);
}
?>

