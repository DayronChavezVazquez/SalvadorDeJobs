<?php
/**
 * Script de instalación automática de FPDF
 * Ejecuta este archivo desde el navegador o desde la línea de comandos
 */

echo "<h1>Instalador de FPDF</h1>";

$fpdf_url = "http://www.fpdf.org/en/download/fpdf186.zip";
$zip_file = __DIR__ . "/fpdf.zip";
$extract_dir = __DIR__ . "/fpdf_temp";
$target_file = __DIR__ . "/fpdf.php";

// Verificar si FPDF ya está instalado
if (file_exists($target_file) || file_exists(__DIR__ . "/fpdf/fpdf.php")) {
    echo "<p style='color: green;'>✓ FPDF ya está instalado.</p>";
    echo "<p><a href='index.php?page=comprobante'>← Volver</a></p>";
    exit;
}

echo "<p>Descargando FPDF...</p>";

// Intentar descargar FPDF
$ch = curl_init($fpdf_url);
$fp = fopen($zip_file, 'w');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
fclose($fp);

if (!$result || !file_exists($zip_file)) {
    echo "<p style='color: red;'>✗ Error al descargar FPDF. Intentando método alternativo...</p>";
    
    // Método alternativo: crear fpdf.php básico desde una URL directa
    echo "<p>Creando archivo FPDF básico...</p>";
    
    // Intentar descargar directamente el archivo fpdf.php
    $fpdf_php_url = "https://raw.githubusercontent.com/Setasign/FPDF/master/fpdf.php";
    $ch2 = curl_init($fpdf_php_url);
    $fp2 = fopen($target_file, 'w');
    curl_setopt($ch2, CURLOPT_FILE, $fp2);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    $result2 = curl_exec($ch2);
    curl_close($ch2);
    fclose($fp2);
    
    if ($result2 && file_exists($target_file)) {
        echo "<p style='color: green;'>✓ FPDF instalado correctamente!</p>";
        echo "<p><a href='index.php?page=comprobante'>← Volver y probar</a></p>";
    } else {
        echo "<p style='color: red;'>✗ No se pudo instalar automáticamente.</p>";
        echo "<h2>Instalación Manual:</h2>";
        echo "<ol>";
        echo "<li>Descarga FPDF desde: <a href='http://www.fpdf.org/' target='_blank'>http://www.fpdf.org/</a></li>";
        echo "<li>Extrae el archivo <code>fpdf.php</code></li>";
        echo "<li>Colócalo en la carpeta: <code>" . __DIR__ . "</code></li>";
        echo "</ol>";
    }
    exit;
}

// Extraer el ZIP
echo "<p>Extrayendo archivos...</p>";
$zip = new ZipArchive;
if ($zip->open($zip_file) === TRUE) {
    $zip->extractTo($extract_dir);
    $zip->close();
    
    // Buscar fpdf.php en el directorio extraído
    $fpdf_found = false;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($extract_dir),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === 'fpdf.php') {
            copy($file->getPathname(), $target_file);
            $fpdf_found = true;
            break;
        }
    }
    
    // Limpiar archivos temporales
    if (file_exists($zip_file)) unlink($zip_file);
    if (is_dir($extract_dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extract_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        rmdir($extract_dir);
    }
    
    if ($fpdf_found) {
        echo "<p style='color: green;'>✓ FPDF instalado correctamente!</p>";
        echo "<p><a href='index.php?page=comprobante'>← Volver y probar</a></p>";
    } else {
        echo "<p style='color: red;'>✗ No se encontró fpdf.php en el archivo descargado.</p>";
        echo "<h2>Instalación Manual:</h2>";
        echo "<ol>";
        echo "<li>Descarga FPDF desde: <a href='http://www.fpdf.org/' target='_blank'>http://www.fpdf.org/</a></li>";
        echo "<li>Extrae el archivo <code>fpdf.php</code></li>";
        echo "<li>Colócalo en la carpeta: <code>" . __DIR__ . "</code></li>";
        echo "</ol>";
    }
} else {
    echo "<p style='color: red;'>✗ Error al extraer el archivo ZIP.</p>";
    echo "<h2>Instalación Manual:</h2>";
    echo "<ol>";
    echo "<li>Descarga FPDF desde: <a href='http://www.fpdf.org/' target='_blank'>http://www.fpdf.org/</a></li>";
    echo "<li>Extrae el archivo <code>fpdf.php</code></li>";
    echo "<li>Colócalo en la carpeta: <code>" . __DIR__ . "</code></li>";
    echo "</ol>";
}
?>


