<?php
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/font/');
}

$fpdf_cargado = false;
$FPDFClass = null;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('FPDF')) {
        $FPDFClass = 'FPDF';
        $fpdf_cargado = true;
    } elseif (class_exists('setasign\Fpdi\Fpdf\Fpdf')) {
        $FPDFClass = 'setasign\Fpdi\Fpdf\Fpdf';
        $fpdf_cargado = true;
    }
}

if (!$fpdf_cargado && file_exists(__DIR__ . '/fpdf.php')) {
    require_once __DIR__ . '/fpdf.php';
    if (class_exists('FPDF')) {
        $FPDFClass = 'FPDF';
        $fpdf_cargado = true;
    }
}

if (!$fpdf_cargado && file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    require_once __DIR__ . '/fpdf/fpdf.php';
    if (class_exists('FPDF')) {
        $FPDFClass = 'FPDF';
        $fpdf_cargado = true;
    }
}

if (!$fpdf_cargado) {
    header('Content-Type: text/html; charset=utf-8');
    die('No se pudo cargar la librería FPDF. Verifica la instalación.');
}

include 'conexion_base.php';

$mes = isset($_GET['mes']) ? trim($_GET['mes']) : '';
$anio = isset($_GET['anio']) ? trim($_GET['anio']) : '';

if ($mes === '' || $anio === '') {
    die('Parámetros inválidos.');
}

try {
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

    if (!$departamentos) {
        header('Content-Type: text/html; charset=utf-8');
        die('<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error - Sin comprobantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .error-container { max-width: 600px; padding: 20px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="alert alert-warning shadow-lg" role="alert" style="border: 3px solid #ffc107; border-radius: 10px; padding: 30px;">
            <div class="d-flex align-items-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#ffc107" class="me-3" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div>
                    <h4 class="alert-heading mb-3" style="color: #856404; font-weight: bold;">⚠️ No se encontraron comprobantes</h4>
                    <p class="mb-2" style="color: #856404; font-size: 16px;">
                        <strong>No se cuenta con ningún comprobante de pago para el mes y año seleccionados.</strong>
                    </p>
                    <p class="mb-0" style="color: #856404;">
                        Periodo consultado: ' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') . '
                    </p>
                    <hr>
                    <p class="mb-0">
                        <a href="index.php?page=consulta_comprobante_general" class="btn btn-warning">Volver a consulta</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>');
    }
    
    // Verificar si hay al menos un departamento con total_pagar > 0
    $tieneComprobantes = false;
    foreach ($departamentos as $dep) {
        if (isset($dep['total_pagar']) && (float)$dep['total_pagar'] > 0) {
            $tieneComprobantes = true;
            break;
        }
    }
    
    if (!$tieneComprobantes) {
        header('Content-Type: text/html; charset=utf-8');
        die('<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error - Sin comprobantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .error-container { max-width: 600px; padding: 20px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="alert alert-warning shadow-lg" role="alert" style="border: 3px solid #ffc107; border-radius: 10px; padding: 30px;">
            <div class="d-flex align-items-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#ffc107" class="me-3" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div>
                    <h4 class="alert-heading mb-3" style="color: #856404; font-weight: bold;">⚠️ No se encontraron comprobantes</h4>
                    <p class="mb-2" style="color: #856404; font-size: 16px;">
                        <strong>No se cuenta con ningún comprobante de pago para el mes y año seleccionados.</strong>
                    </p>
                    <p class="mb-0" style="color: #856404;">
                        Periodo consultado: ' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') . '
                    </p>
                    <hr>
                    <p class="mb-0">
                        <a href="index.php?page=consulta_comprobante_general" class="btn btn-warning">Volver a consulta</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>');
    }

    function convertirTexto($texto) {
        if (!is_string($texto) || $texto === '') {
            return is_numeric($texto) ? $texto : 'N/A';
        }

        if (function_exists('mb_convert_encoding')) {
            $converted = @mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
            if ($converted !== false && $converted !== '') {
                return $converted;
            }
        }

        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
            if ($converted !== false && $converted !== '') {
                return $converted;
            }
        }

        if (function_exists('utf8_decode')) {
            $decoded = @utf8_decode($texto);
            if ($decoded !== false && $decoded !== '') {
                return $decoded;
            }
        }

        return $texto;
    }

    $pdf = new $FPDFClass('L', 'mm', 'Letter');
    $pdf->AddPage();

    $imagen_izq = __DIR__ . '/imagenes/sepe_uset.png';
    $imagen_der = __DIR__ . '/imagenes/nueva_historia.png';

    if (file_exists($imagen_izq)) {
        $pdf->Image($imagen_izq, 10, 10, 50, 0);
    }

    if (file_exists($imagen_der)) {
        $pdf->Image($imagen_der, 230, 10, 40, 0);
    }

    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetY(32);
    $pdf->Cell(0, 10, convertirTexto('COMPROBANTE DE GASTOS GENERAL'), 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 7, convertirTexto("Periodo: $mes $anio"), 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(88, 37, 116);
    $pdf->SetTextColor(255, 255, 255);

    $columnas = [
        ['titulo' => 'Folio', 'ancho' => 18],
        ['titulo' => 'Nombre del Departamento', 'ancho' => 145],
        ['titulo' => 'Teléfono', 'ancho' => 30],
        ['titulo' => 'CCT', 'ancho' => 35],
        ['titulo' => 'Total a pagar', 'ancho' => 27]
    ];

    $pdf->SetX(10);
    foreach ($columnas as $columna) {
        $pdf->Cell($columna['ancho'], 10, convertirTexto($columna['titulo']), 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);

    $relleno = false;
    $total_general = 0;

    foreach ($departamentos as $departamento) {
        $total = isset($departamento['total_pagar']) ? (float)$departamento['total_pagar'] : 0;
        $total_general += $total;

        $pdf->SetFillColor($relleno ? 240 : 255);
        $pdf->SetX(10);

        $pdf->Cell($columnas[0]['ancho'], 8, convertirTexto($departamento['folio'] ?: 'N/A'), 1, 0, 'L', true);
        $pdf->Cell($columnas[1]['ancho'], 8, convertirTexto($departamento['nombre_departamento'] ?: 'N/A'), 1, 0, 'L', true);
        $pdf->Cell($columnas[2]['ancho'], 8, convertirTexto($departamento['telefono'] ?: 'N/A'), 1, 0, 'L', true);
        $pdf->Cell($columnas[3]['ancho'], 8, convertirTexto($departamento['cct'] ?: 'N/A'), 1, 0, 'L', true);
        $pdf->Cell($columnas[4]['ancho'], 8, convertirTexto('$' . number_format($total, 2, '.', ',')), 1, 1, 'R', true);

        $relleno = !$relleno;

        if ($pdf->GetY() > 180) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(88, 37, 116);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetX(10);
            foreach ($columnas as $columna) {
                $pdf->Cell($columna['ancho'], 10, convertirTexto($columna['titulo']), 1, 0, 'C', true);
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
        }
    }

    // Calcular: Total a pagar es la suma de total_pagar de todos los departamentos
    // IVA es el 16% del total a pagar
    // Subtotal es el total a pagar menos el IVA
    $total_a_pagar = $total_general; // Total a pagar = suma de total_pagar de todos los departamentos
    $iva = $total_a_pagar * 0.16; // IVA del 16% del total a pagar
    $subtotal = $total_a_pagar - $iva; // Subtotal = Total a pagar - IVA
    
    // Tabla de resumen con IVA y total
    $pdf->Ln(5);
    $y_pos_resumen = $pdf->GetY();
    
    // Ancho de la tabla de resumen (alineada a la derecha)
    $ancho_tabla_resumen = 100;
    $x_inicio_resumen = 10 + (255 - $ancho_tabla_resumen); // 255 es el ancho total de las columnas
    
    // Encabezado de la tabla de resumen
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(88, 37, 116);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY($x_inicio_resumen, $y_pos_resumen);
    $pdf->Cell($ancho_tabla_resumen, 8, convertirTexto('Resumen'), 1, 1, 'C', true);
    
    // Subtotal (suma de total_pagar de todos los departamentos)
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY($x_inicio_resumen, $pdf->GetY());
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell($ancho_tabla_resumen / 2, 8, convertirTexto('Subtotal:'), 1, 0, 'L', true);
    $pdf->Cell($ancho_tabla_resumen / 2, 8, convertirTexto('$' . number_format($subtotal, 2, '.', ',')), 1, 1, 'R', true);
    
    // IVA (16%)
    $pdf->SetXY($x_inicio_resumen, $pdf->GetY());
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell($ancho_tabla_resumen / 2, 8, convertirTexto('IVA (16%):'), 1, 0, 'L', true);
    $pdf->Cell($ancho_tabla_resumen / 2, 8, convertirTexto('$' . number_format($iva, 2, '.', ',')), 1, 1, 'R', true);
    
    // Total por pagar (suma de total_pagar de todos los departamentos)
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(88, 37, 116);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY($x_inicio_resumen, $pdf->GetY());
    $pdf->Cell($ancho_tabla_resumen / 2, 8, convertirTexto('Total por pagar:'), 1, 0, 'L', true);
    $pdf->Cell($ancho_tabla_resumen / 2, 8, convertirTexto('$' . number_format($total_a_pagar, 2, '.', ',')), 1, 1, 'R', true);

    $nombre_archivo = 'comprobante_gastos_general_' . preg_replace('/\s+/', '_', strtolower($mes)) . '_' . $anio . '.pdf';
    $pdf->Output('D', $nombre_archivo);
    exit;

} catch (PDOException $e) {
    die('Error al generar el PDF: ' . $e->getMessage());
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>


