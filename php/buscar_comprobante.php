<?php
/**
 * ============================================
 * buscar_comprobante.php - API DE BÚSQUEDA DE COMPROBANTES
 * ============================================
 * 
 * Este archivo es una API que busca comprobantes en la base de datos.
 * Se llama desde JavaScript (AJAX) cuando el usuario busca un comprobante.
 * 
 * ¿QUÉ HACE ESTE ARCHIVO?
 * 1. Recibe datos del formulario (teléfono/CCT, mes, año)
 * 2. Busca el departamento en la base de datos
 * 3. Busca el comprobante asociado
 * 4. Retorna un JSON con los resultados
 * 
 * ¿CÓMO SE USA?
 * - Se llama automáticamente desde consulta_comprobante.php
 * - No se accede directamente desde el navegador
 * - Retorna datos en formato JSON para JavaScript
 * 
 * ¿QUÉ MODIFICAR AQUÍ?
 * - Para cambiar la búsqueda: modifica las consultas SQL (líneas 34, 47, 61)
 * - Para agregar más campos: añade parámetros en $_POST y en la respuesta JSON
 */

// Indicar que la respuesta será JSON (no HTML)
// Esto permite que JavaScript pueda leer los datos fácilmente
header('Content-Type: application/json; charset=utf-8');

// Incluir la conexión a la base de datos
include 'conexion_base.php';

// ============================================
// OBTENER Y LIMPIAR PARÁMETROS DEL FORMULARIO
// ============================================

// $_POST contiene los datos enviados desde el formulario
// trim() elimina espacios al inicio y final
// isset() verifica que la variable exista antes de usarla

// Teléfono del departamento (opcional si se busca por CCT)
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';

// CCT del departamento (opcional si se busca por teléfono)
$cct = isset($_POST['cct']) ? trim($_POST['cct']) : '';

// Mes del comprobante (Enero, Febrero, etc.)
$mes = isset($_POST['mes']) ? trim($_POST['mes']) : '';

// Año del comprobante (2023, 2024, etc.)
$anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';

// ============================================
// VALIDACIÓN DE DATOS
// ============================================

// Verificar que se haya proporcionado al menos teléfono O CCT
// Y que mes y año estén presentes
if ((empty($telefono) && empty($cct)) || empty($mes) || empty($anio)) {
    // Si faltan datos, retornar error en formato JSON
    echo json_encode([
        'success' => false,  // Indica que hubo un error
        'message' => 'Todos los campos son requeridos'
    ]);
    exit; // Detener la ejecución del script
}

// ============================================
// BUSCAR DEPARTAMENTO EN LA BASE DE DATOS
// ============================================

try {
    // Variable para almacenar el departamento encontrado
    $departamento = null;
    
    // Decidir si buscar por teléfono o por CCT
    if (!empty($telefono)) {
        /**
         * BÚSQUEDA POR TELÉFONO
         * 
         * prepare() = prepara una consulta SQL de forma segura
         * :telefono = marcador de posición (evita inyección SQL)
         * LIMIT 1 = solo obtener el primer resultado
         */
        $stmt_dept = $conn->prepare("SELECT * FROM ct_departamentos WHERE telefono = :telefono LIMIT 1");
        
        // execute() = ejecuta la consulta con los valores reales
        // ['telefono' => $telefono] = reemplaza :telefono con el valor de $telefono
        $stmt_dept->execute([':telefono' => $telefono]);
        
        // fetch() = obtiene una fila de resultados
        // PDO::FETCH_ASSOC = retorna un array asociativo (nombre_columna => valor)
        $departamento = $stmt_dept->fetch(PDO::FETCH_ASSOC);
        
        // Si no se encontró ningún departamento, retornar error
        if (!$departamento) {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró ningún departamento con ese teléfono'
            ]);
            exit;
        }
        
    } else if (!empty($cct)) {
        /**
         * BÚSQUEDA POR CCT (Clave de Centro de Trabajo)
         * 
         * Mismo proceso que la búsqueda por teléfono,
         * pero usando el campo 'cct' en lugar de 'telefono'
         */
        $stmt_dept = $conn->prepare("SELECT * FROM ct_departamentos WHERE cct = :cct LIMIT 1");
        $stmt_dept->execute([':cct' => $cct]);
        $departamento = $stmt_dept->fetch(PDO::FETCH_ASSOC);
        
        // Si no se encontró ningún departamento, retornar error
        if (!$departamento) {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró ningún departamento con ese CCT'
            ]);
            exit;
        }
    }
    
    // ============================================
    // BUSCAR COMPROBANTE EN LA TABLA Pagos
    // ============================================
    
    /**
     * Buscar el comprobante asociado al departamento encontrado
     * para el mes y año especificados
     * 
     * La consulta busca en la tabla 'Pagos' donde:
     * - id_departamento coincide con el departamento encontrado
     * - mes_pago coincide con el mes proporcionado
     * - año_pago coincide con el año proporcionado
     */
    $stmt_comp = $conn->prepare("SELECT * FROM Pagos 
                                  WHERE id_departamento = :id_departamento 
                                  AND mes_pago = :mes 
                                  AND año_pago = :anio 
                                  LIMIT 1");
    
    // Ejecutar la consulta con los parámetros
    $stmt_comp->execute([
        ':id_departamento' => $departamento['id_departamento'], // ID del departamento encontrado
        ':mes' => $mes,  // Mes del comprobante
        ':anio' => $anio // Año del comprobante
    ]);
    
    // Obtener el resultado (si existe)
    $comprobante = $stmt_comp->fetch(PDO::FETCH_ASSOC);
    
    // ============================================
    // VERIFICAR SI EL COMPROBANTE ES VÁLIDO
    // ============================================
    
    /**
     * Un comprobante es válido si:
     * 1. Existe en la base de datos ($comprobante no es false)
     * 2. Tiene un total_pagar mayor a 0
     */
    $tiene_comprobante = false;
    
    if ($comprobante) {
        // Obtener el total a pagar y convertirlo a número decimal
        // floatval() convierte el valor a número decimal (ej: "1500.50" → 1500.50)
        // Si no existe, usar 0 como valor por defecto
        $total_pagar = isset($comprobante['total_pagar']) ? floatval($comprobante['total_pagar']) : 0;
        
        // Solo considerar válido si el total es mayor a 0
        if ($total_pagar > 0) {
            $tiene_comprobante = true;
        }
    }
    
    // ============================================
    // PREPARAR RESPUESTA JSON
    // ============================================
    
    /**
     * Construir el array de respuesta que se enviará al navegador
     * Este array se convertirá a JSON para que JavaScript pueda leerlo
     */
    $response = [
        'success' => true,  // Indica que la operación fue exitosa
        'tiene_comprobante' => $tiene_comprobante,  // true si hay comprobante válido, false si no
        'departamento' => [
            // Información del departamento encontrado
            'id_departamento' => $departamento['id_departamento'],
            'nombre_departamento' => $departamento['nombre_departamento'],
            'telefono' => $departamento['telefono'],
            'cct' => $departamento['cct'] ?? 'N/A',  // ?? = si no existe, usar 'N/A'
            'folio' => $departamento['folio'] ?? 'N/A'
        ]
    ];
    
    // Si hay un comprobante válido, agregar su información a la respuesta
    if ($tiene_comprobante) {
        $response['comprobante'] = [
            'id_pago' => $comprobante['id_pago'],        // ID único del pago
            'total_pagar' => $comprobante['total_pagar'], // Cantidad a pagar
            'mes_pago' => $comprobante['mes_pago'],       // Mes del comprobante
            'año_pago' => $comprobante['año_pago']        // Año del comprobante
        ];
    }
    
    // Convertir el array a JSON y enviarlo al navegador
    // JSON_UNESCAPED_UNICODE = permite caracteres especiales (ñ, acentos, etc.)
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    /**
     * CAPTURAR ERRORES DE BASE DE DATOS
     * 
     * PDOException se lanza cuando hay un problema con la base de datos:
     * - Error de conexión
     * - Error en la consulta SQL
     * - Tabla no existe
     * etc.
     */
    echo json_encode([
        'success' => false,
        'message' => 'Error al realizar la búsqueda: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    /**
     * CAPTURAR CUALQUIER OTRO ERROR
     * 
     * Exception es la clase base para todos los errores en PHP
     * Captura cualquier error que no sea específico de PDO
     */
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

