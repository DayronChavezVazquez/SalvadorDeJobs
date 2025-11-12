<?php
/**
 * ============================================
 * obtener_anios_disponibles.php - API DE AÑOS DISPONIBLES
 * ============================================
 * 
 * Este archivo consulta la base de datos para obtener los años
 * en los que hay comprobantes registrados. Se usa para poblar
 * dinámicamente los selectores de año en los formularios.
 * 
 * ¿QUÉ HACE ESTE ARCHIVO?
 * 1. Consulta la tabla Pagos para obtener años únicos
 * 2. Filtra años válidos (mayores a 0, menores o iguales al año actual)
 * 3. Asegura que el año actual esté siempre incluido
 * 4. Retorna un JSON con la lista de años disponibles
 * 
 * ¿POR QUÉ ES ÚTIL?
 * - Si hay datos de 2023, 2024 y 2025, muestra esos años
 * - Si solo hay datos del año actual, solo muestra el año actual
 * - Evita mostrar años futuros o inválidos
 * 
 * ¿CÓMO SE USA?
 * - Se llama desde JavaScript (AJAX) cuando se carga la página
 * - Los años se cargan automáticamente en los selectores
 * - Si hay error, retorna solo el año actual como fallback
 */

// Indicar que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir la conexión a la base de datos
include 'conexion_base.php';

try {
    // ============================================
    // OBTENER EL AÑO ACTUAL
    // ============================================
    
    // date('Y') = obtiene el año actual (ej: 2024)
    // (int) = convierte a número entero
    $anio_actual = (int)date('Y');
    
    // ============================================
    // CONSULTAR AÑOS EN LA BASE DE DATOS
    // ============================================
    
    /**
     * Consulta SQL explicada:
     * - SELECT DISTINCT = obtener valores únicos (sin repetir)
     * - año_pago = columna que contiene el año
     * - FROM Pagos = tabla donde buscar
     * - WHERE año_pago IS NOT NULL = solo años que no sean nulos
     * - ORDER BY año_pago DESC = ordenar de mayor a menor (2025, 2024, 2023...)
     */
    $stmt = $conn->prepare("SELECT DISTINCT año_pago FROM Pagos WHERE año_pago IS NOT NULL ORDER BY año_pago DESC");
    $stmt->execute();
    
    // fetchAll(PDO::FETCH_COLUMN) = obtener todos los valores de la columna como array
    // Ejemplo: [2025, 2024, 2023] si hay comprobantes de esos años
    $anios_db = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // ============================================
    // FILTRAR Y VALIDAR AÑOS
    // ============================================
    
    /**
     * Procesar los años obtenidos de la base de datos:
     * 1. Convertir a enteros
     * 2. Verificar que sean válidos (mayores a 0, no mayores al año actual)
     * 3. Agregar a la lista de años disponibles
     */
    $anios_disponibles = [];
    foreach ($anios_db as $anio) {
        $anio_int = (int)$anio;  // Convertir a entero
        
        // Solo incluir años válidos (positivos y no futuros)
        if ($anio_int > 0 && $anio_int <= $anio_actual) {
            $anios_disponibles[] = $anio_int;
        }
    }
    
    // ============================================
    // GARANTIZAR QUE EL AÑO ACTUAL ESTÉ INCLUIDO
    // ============================================
    
    // Si no hay años en la BD, usar solo el año actual
    if (empty($anios_disponibles)) {
        $anios_disponibles = [$anio_actual];
    } else {
        // Si hay años pero no está el actual, agregarlo
        if (!in_array($anio_actual, $anios_disponibles)) {
            $anios_disponibles[] = $anio_actual;
            rsort($anios_disponibles); // Ordenar de mayor a menor
        }
    }
    
    // ============================================
    // LIMPIAR Y ORDENAR RESULTADOS
    // ============================================
    
    // Eliminar años duplicados (por si acaso)
    $anios_disponibles = array_unique($anios_disponibles);
    
    // Ordenar de mayor a menor (año más reciente primero)
    rsort($anios_disponibles);
    
    // ============================================
    // RETORNAR RESPUESTA JSON
    // ============================================
    
    echo json_encode([
        'success' => true,                    // Operación exitosa
        'anios' => $anios_disponibles,        // Array de años disponibles [2025, 2024, 2023]
        'anio_actual' => $anio_actual         // Año actual para seleccionarlo por defecto
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

