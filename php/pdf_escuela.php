<?php
/**
 * pdf_escuela.php
 * 
 * Este archivo genera un PDF con la información del comprobante.
 * 
 * REQUISITOS:
 * Para que este archivo funcione, necesitas tener instalada la librería FPDF.
 * 
 * Instalación con Composer:
 * composer require setasign/fpdf
 * 
 * O descarga FPDF desde: http://www.fpdf.org/
 * y coloca el archivo fpdf.php en esta misma carpeta.
 */

// Incluir FPDF
// Si usas Composer, descomenta esta línea:
// require_once __DIR__ . '/vendor/autoload.php';

// Si descargaste FPDF manualmente, descomenta y ajusta esta ruta:
// require_once('fpdf.php');

// Si no tienes FPDF instalado, puedes usar esta alternativa simple:
// require_once('fpdf/fpdf.php');

// Definir la ruta de las fuentes ANTES de cargar FPDF
// Esto asegura que FPDF encuentre las fuentes correctamente
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/font/');
}

// Intentar cargar FPDF de diferentes formas
$fpdf_cargado = false;
$FPDFClass = null;

// Intentar desde Composer (setasign/fpdf)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('FPDF')) {
        $FPDFClass = 'FPDF';
        $fpdf_cargado = true;
    } elseif (class_exists('setasign\Fpdi\Fpdf\Fpdf')) {
        // Si usa FPDI, intentar con el namespace correcto
        $FPDFClass = 'setasign\Fpdi\Fpdf\Fpdf';
        $fpdf_cargado = true;
    }
}

// Si no se cargó, intentar desde archivo directo
if (!$fpdf_cargado && file_exists(__DIR__ . '/fpdf.php')) {
    require_once(__DIR__ . '/fpdf.php');
    if (class_exists('FPDF')) {
        $FPDFClass = 'FPDF';
        $fpdf_cargado = true;
    }
}

// Si aún no se cargó, intentar desde carpeta fpdf
if (!$fpdf_cargado && file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    require_once(__DIR__ . '/fpdf/fpdf.php');
    if (class_exists('FPDF')) {
        $FPDFClass = 'FPDF';
        $fpdf_cargado = true;
    }
}

if (!$fpdf_cargado) {
    // Mostrar mensaje de error más amigable
    header('Content-Type: text/html; charset=utf-8');
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error - FPDF no instalado</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
            .error-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
            h1 { color: #d32f2f; }
            code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>❌ Error: FPDF no está instalado</h1>
            <p>Para generar PDFs, necesitas instalar la librería FPDF.</p>
            <h3>Opción 1: Usando Composer (Recomendado)</h3>
            <p>Ejecuta en la terminal dentro de la carpeta <code>php</code>:</p>
            <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>composer require setasign/fpdf</code></pre>
            <h3>Opción 2: Descarga Manual</h3>
            <ol>
                <li>Descarga FPDF desde: <a href="http://www.fpdf.org/" target="_blank">http://www.fpdf.org/</a></li>
                <li>Extrae el archivo <code>fpdf.php</code></li>
                <li>Colócalo en la carpeta <code>php</code></li>
            </ol>
            <p><a href="index.php?page=comprobante">← Volver</a></p>
        </div>
    </body>
    </html>
    ');
}

include 'conexion_base.php';

// Obtener parámetros
$id_pago = isset($_GET['id_pago']) ? (int)$_GET['id_pago'] : 0;
$id_departamento = isset($_GET['id_departamento']) ? (int)$_GET['id_departamento'] : 0;

if ($id_pago <= 0 || $id_departamento <= 0) {
    die('Error: Parámetros inválidos');
}

try {
    // Obtener datos del comprobante desde la tabla Pagos
    $stmt_comp = $conn->prepare("SELECT * FROM Pagos WHERE id_pago = :id LIMIT 1");
    $stmt_comp->execute([':id' => $id_pago]);
    $comprobante = $stmt_comp->fetch(PDO::FETCH_ASSOC);
    
    if (!$comprobante) {
        die('Error: Comprobante no encontrado');
    }
    
    // Obtener datos del departamento
    $stmt_dept = $conn->prepare("SELECT * FROM ct_departamentos WHERE id_departamento = :id LIMIT 1");
    $stmt_dept->execute([':id' => $id_departamento]);
    $departamento = $stmt_dept->fetch(PDO::FETCH_ASSOC);
    
    if (!$departamento) {
        die('Error: Departamento no encontrado');
    }
    
    // Función auxiliar para convertir texto a formato compatible con FPDF (ISO-8859-1)
    // FPDF requiere ISO-8859-1 para mostrar correctamente caracteres especiales como acentos
    function convertirTexto($texto) {
        if (empty($texto) || !is_string($texto)) {
            return $texto;
        }
        
        // Intentar convertir de UTF-8 a ISO-8859-1 usando mb_convert_encoding (más confiable)
        if (function_exists('mb_convert_encoding')) {
            $converted = @mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
            if ($converted !== false && $converted !== '') {
                return $converted;
            }
        }
        
        // Si mb_convert_encoding no funciona, intentar con iconv
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
            if ($converted !== false && $converted !== '') {
                return $converted;
            }
        }
        
        // Último recurso: utf8_decode (puede estar deprecado en PHP 8.2+ pero aún funciona)
        if (function_exists('utf8_decode')) {
            $decoded = @utf8_decode($texto);
            if ($decoded !== false && $decoded !== '') {
                return $decoded;
            }
        }
        
        // Si nada funciona, devolver el texto original
        return $texto;
    }
    
    // Crear instancia de FPDF
    // Usar la clase correcta según cómo se cargó
    // Usamos una variable de clase para evitar errores del linter
    $pdf = new $FPDFClass('P', 'mm', 'Letter'); // P = Portrait (vertical), mm = milímetros, Letter = tamaño carta
    
    // Agregar una página
    $pdf->AddPage();
    
    // ============================================
    // IMÁGENES EN EL ENCABEZADO
    // ============================================
    // INSTRUCCIONES PARA MODIFICAR IMÁGENES:
    // Para cambiar la posición y tamaño de las imágenes, modifica los parámetros en $pdf->Image():
    // $pdf->Image($ruta_imagen, x, y, ancho, alto);
    // 
    // PARÁMETROS:
    // - x: posición horizontal desde la izquierda (en mm)
    //   - Valores menores = más a la izquierda
    //   - Valores mayores = más a la derecha
    //   - Ejemplo: x=10 (izquierda), x=100 (centro), x=180 (derecha)
    //
    // - y: posición vertical desde arriba (en mm)
    //   - Valores menores = más arriba
    //   - Valores mayores = más abajo
    //   - Ejemplo: y=10 (arriba), y=50 (medio), y=100 (abajo)
    //
    // - ancho: ancho de la imagen (en mm)
    //   - Valores mayores = imagen más ancha
    //   - Si es 0, se calcula automáticamente manteniendo proporción
    //   - Ejemplo: ancho=30 (pequeña), ancho=50 (mediana), ancho=70 (grande)
    //
    // - alto: alto de la imagen (en mm)
    //   - Valores mayores = imagen más alta
    //   - Si es 0, se mantiene la proporción automáticamente
    //   - Ejemplo: alto=20 (pequeña), alto=30 (mediana), alto=40 (grande)
    //
    // NOTA: Si pones ancho=0 o alto=0, la imagen mantendrá su proporción original
    
    $imagen_izq = __DIR__ . '/imagenes/sepe_uset.png';
    $imagen_der = __DIR__ . '/imagenes/nueva_historia.png';
    
    // Imagen izquierda (sepe_uset.png)
    // Parámetros actuales: x=10mm (izquierda), y=20mm (bajada), ancho=60mm (más grande), alto=0 (proporción automática)
    // Para moverla: cambia x (izquierda/derecha) y y (arriba/abajo)
    // Para agrandarla/reducirla: cambia ancho (y opcionalmente alto)
    if (file_exists($imagen_izq)) {
        $pdf->Image($imagen_izq, 10, 20, 60, 0);
    }
    
    // Imagen derecha (nueva_historia.png)
    // Parámetros actuales: x=145mm (movida a la izquierda), y=10mm (arriba), ancho=40mm, alto=0 (proporción automática)
    // Para moverla: cambia x (izquierda/derecha) y y (arriba/abajo)
    // Para agrandarla/reducirla: cambia ancho (y opcionalmente alto)
    if (file_exists($imagen_der)) {
        $pdf->Image($imagen_der, 145, 10, 40, 0);
    }
    
    // ============================================
    // PARTE SUPERIOR: FOLIO (LADO DERECHO)
    // ============================================
    $folio = convertirTexto($departamento['folio'] ?? 'N/A');
    $pdf->SetXY(150, 10);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(50, 10, convertirTexto('Folio: ') . $folio, 0, 1, 'R'); // 'R' = alineación derecha
    
    // ============================================
    // TÍTULO DEL COMPROBANTE
    // ============================================
    $pdf->SetY(40); // Posición Y después de las imágenes (ajustada porque la imagen izquierda está más abajo)
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 10, convertirTexto('COMPROBANTE DE PAGO TELMEX'), 0, 1, 'C'); // 'C' = centrado
    $pdf->Ln(5);
    
    // ============================================
    // TABLA CON INFORMACIÓN DEL COMPROBANTE
    // ============================================
    $y_pos = 55; // Posición Y inicial para la tabla (ajustada)
    $ancho_col1 = 70; // Ancho de la columna de etiquetas
    $ancho_col2 = 120; // Ancho de la columna de valores
    $alto_fila = 8; // Alto de cada fila
    
    // Preparar datos para la tabla (aplicar convertirTexto a los nombres de campos también)
    // IMPORTANTE: Todos los textos deben pasar por convertirTexto() para que los acentos se muestren correctamente
    $datos = [
        [convertirTexto('Nombre del Departamento'), convertirTexto($departamento['nombre_departamento'] ?? 'N/A')],
        [convertirTexto('Teléfono'), convertirTexto($departamento['telefono'] ?? 'N/A')],
        [convertirTexto('Mes'), convertirTexto($comprobante['mes_pago'] ?? 'N/A')],
        [convertirTexto('Año'), convertirTexto($comprobante['año_pago'] ?? 'N/A')],
        [convertirTexto('Cantidad a Pagar'), '$' . number_format($comprobante['total_pagar'], 2, '.', ',')],
        [convertirTexto('Nombre del Firmante'), convertirTexto($comprobante['nombre_firmante'] ?? 'N/A')],
        [convertirTexto('Puesto del Firmante'), convertirTexto($comprobante['cargo_firmante'] ?? 'N/A')],
        [convertirTexto('Dirección del Departamento'), convertirTexto($departamento['domicilio'] ?? 'N/A')]
    ];
    
    // Color del encabezado: #582574 (RGB: 88, 37, 116)
    $color_encabezado_r = 88;
    $color_encabezado_g = 37;
    $color_encabezado_b = 116;
    
    // Color gris claro para el cuerpo alternado
    $color_gris = 240;
    
    // Encabezado de la tabla
    $pdf->SetXY(10, $y_pos);
    $pdf->SetFillColor($color_encabezado_r, $color_encabezado_g, $color_encabezado_b);
    $pdf->SetTextColor(255, 255, 255); // Texto blanco
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($ancho_col1, $alto_fila, convertirTexto('Campo'), 1, 0, 'C', true);
    $pdf->Cell($ancho_col2, $alto_fila, convertirTexto('Valor'), 1, 1, 'C', true);
    $y_pos += $alto_fila;
    
    // Filas de datos (alternando colores)
    $pdf->SetTextColor(0, 0, 0); // Texto negro
    $pdf->SetFont('Arial', '', 11);
    
    foreach ($datos as $index => $fila) {
        $color_fondo = ($index % 2 == 0) ? 255 : $color_gris; // Alternar blanco y gris
        $pdf->SetFillColor($color_fondo, $color_fondo, $color_fondo);
        
        // Para la dirección, usar MultiCell si es muy larga para que se ajuste correctamente
        if ($index == 7) { // Es la fila de Dirección del Departamento (ahora es el índice 7)
            $x_inicio = 10 + $ancho_col1;
            $texto_direccion = $fila[1];
            $ancho_maximo = $ancho_col2 - 4; // Dejar margen de 2mm a cada lado
            
            // Configurar fuente para calcular el ancho
            $pdf->SetFont('Arial', '', 11);
            
            // Calcular cuántas líneas necesitará usando MultiCell
            // Guardar posición actual
            $x_temp = $pdf->GetX();
            $y_temp = $pdf->GetY();
            
            // Calcular altura necesaria usando MultiCell (sin dibujar)
            $pdf->SetXY(0, 0); // Posición temporal
            $altura_texto = 0;
            $num_lineas = 1;
            
            // Dividir el texto en palabras para calcular mejor
            $palabras = explode(' ', $texto_direccion);
            $linea_actual = '';
            $ancho_linea = 0;
            
            foreach ($palabras as $palabra) {
                $ancho_palabra = $pdf->GetStringWidth($linea_actual . ' ' . $palabra);
                if ($ancho_palabra > $ancho_maximo && $linea_actual != '') {
                    $num_lineas++;
                    $linea_actual = $palabra;
                } else {
                    $linea_actual = ($linea_actual == '') ? $palabra : $linea_actual . ' ' . $palabra;
                }
            }
            
            // Calcular altura total necesaria (mejorado para mejor visualización)
            $alto_por_linea = 5.5; // Altura por línea ligeramente aumentada
            $alto_celda = max($alto_fila + 2, ($num_lineas * $alto_por_linea) + 4); // Margen superior e inferior
            
            // Dibujar la celda de campo (columna izquierda) con la altura necesaria
            $pdf->SetXY(10, $y_pos);
            $pdf->Rect(10, $y_pos, $ancho_col1, $alto_celda);
            $pdf->SetFillColor($color_fondo, $color_fondo, $color_fondo);
            $pdf->Rect(10, $y_pos, $ancho_col1, $alto_celda, 'F');
            
            // Escribir el texto del campo centrado verticalmente
            $pdf->SetXY(10 + 2, $y_pos + ($alto_celda / 2) - 3);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell($ancho_col1 - 4, $alto_fila, $fila[0], 0, 0, 'L');
            
            // Dibujar el borde de la celda de valor (columna derecha) con la altura necesaria
            $pdf->SetXY($x_inicio, $y_pos);
            $pdf->Rect($x_inicio, $y_pos, $ancho_col2, $alto_celda);
            $pdf->SetFillColor($color_fondo, $color_fondo, $color_fondo);
            $pdf->Rect($x_inicio, $y_pos, $ancho_col2, $alto_celda, 'F');
            
            // Escribir el texto de la dirección con MultiCell para que se ajuste mejor
            $pdf->SetXY($x_inicio + 3, $y_pos + 3);
            $pdf->SetFont('Arial', '', 11);
            $pdf->MultiCell($ancho_maximo, $alto_por_linea, $texto_direccion, 0, 'L'); // Altura mejorada por línea
            
            // Ajustar y_pos para la siguiente fila
            $y_pos += $alto_celda;
        } else {
            // Para las demás filas, usar Cell normal
            $pdf->SetXY(10, $y_pos);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell($ancho_col1, $alto_fila, $fila[0], 1, 0, 'L', true);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell($ancho_col2, $alto_fila, $fila[1], 1, 1, 'L', true);
            $y_pos += $alto_fila;
        }
    }
    
    $y_pos += 15; // Espacio después de la tabla
    
    // ============================================
    // APARTADO PARA FIRMA DEL REPRESENTANTE
    // ============================================
    // Línea para la firma (centrada)
    $ancho_linea = 80; // Ancho de la línea
    $x_linea = 10; // Posición X de la línea
    
    $pdf->SetXY($x_linea, $y_pos);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell($ancho_linea, 8, '_________________________', 0, 0, 'C'); // 'C' = centrado
    
    // Texto "Firma del representante" centrado debajo de la línea
    $texto_firma = convertirTexto('Firma del representante');
    $ancho_texto = $pdf->GetStringWidth($texto_firma);
    $x_texto = $x_linea + (($ancho_linea - $ancho_texto) / 2); // Centrar el texto
    
    $pdf->SetXY($x_texto, $y_pos + 8);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell($ancho_texto, 8, $texto_firma, 0, 0, 'L');
    
    // ============================================
    // RECUADRO PARA EL SELLO (3cm alto x 6cm ancho)
    // ============================================
    // 3cm = 30mm, 6cm = 60mm
    $sello_x = 140; // Posición X (desde la izquierda)
    $sello_y = $y_pos; // Posición Y (misma altura que la firma)
    $sello_ancho = 60; // 6cm = 60mm
    $sello_alto = 30; // 3cm = 30mm
    
    // Dibujar el recuadro
    $pdf->SetXY($sello_x, $sello_y);
    $pdf->SetDrawColor(0, 0, 0); // Color negro para el borde
    $pdf->SetLineWidth(0.5); // Grosor de línea
    $pdf->Rect($sello_x, $sello_y, $sello_ancho, $sello_alto); // Rectángulo
    
    // Texto dentro del recuadro
    $pdf->SetXY($sello_x + 5, $sello_y + 10);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($sello_ancho - 10, 5, convertirTexto('SELLO'), 0, 0, 'C');
    
    // ============================================
    // GENERAR Y ENVIAR EL PDF
    // ============================================
    // Nombre del archivo usando el nombre del departamento
    $nombre_departamento_original = $departamento['nombre_departamento'] ?? 'departamento';
    // Convertir a formato compatible con nombres de archivo
    $nombre_departamento_limpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre_departamento_original);
    $nombre_departamento_limpio = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nombre_departamento_limpio);
    $nombre_departamento_limpio = preg_replace('/_+/', '_', $nombre_departamento_limpio); // Reemplazar múltiples guiones bajos
    $nombre_departamento_limpio = trim($nombre_departamento_limpio, '_'); // Eliminar guiones bajos al inicio y final
    $nombre_departamento_limpio = mb_substr($nombre_departamento_limpio, 0, 50); // Limitar a 50 caracteres
    if (empty($nombre_departamento_limpio)) {
        $nombre_departamento_limpio = 'departamento';
    }
    $mes_limpio = preg_replace('/[^a-zA-Z0-9_\-]/', '_', strtolower($comprobante['mes_pago']));
    $nombre_archivo = 'comprobante_' . $nombre_departamento_limpio . '_' . $mes_limpio . '_' . $comprobante['año_pago'] . '.pdf';
    
    // Enviar el PDF para descarga automática
    $pdf->Output('D', $nombre_archivo); // 'D' = descargar automáticamente
    
} catch (PDOException $e) {
    die('Error al generar el PDF: ' . $e->getMessage());
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>

