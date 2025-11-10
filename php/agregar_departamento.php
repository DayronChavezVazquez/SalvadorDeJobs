<?php
// agregar_departamento.php
session_start();
include 'conexion_base.php';

// Recibimos los datos del formulario
$nombre = isset($_POST['nombre_departamento']) ? trim($_POST['nombre_departamento']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$folio = isset($_POST['folio']) ? trim($_POST['folio']) : '';
$folio_interno = isset($_POST['folio_interno']) ? trim($_POST['folio_interno']) : '';
$nombre_encargado = isset($_POST['nombre_encargado']) ? trim($_POST['nombre_encargado']) : '';
$cargo = isset($_POST['cargo']) ? trim($_POST['cargo']) : '';
$domicilio = isset($_POST['domicilio']) ? trim($_POST['domicilio']) : '';
$cct = isset($_POST['cct']) ? strtoupper(trim($_POST['cct'])) : '';

// Validar que todos los campos requeridos estén presentes
if (empty($nombre) || empty($telefono) || empty($nombre_encargado) || empty($cargo) || empty($domicilio) || empty($cct)) {
    echo "<script>
        alert('Error: Todos los campos son obligatorios. Por favor, complete todos los campos.');
        window.history.back();
    </script>";
    exit;
}

try {
    $campos_duplicados = [];
    $telefono_existente = null;
    $cct_existente = null;
    
    // Verificar si el teléfono ya existe
    $stmt_telefono = $conn->prepare("SELECT id_departamento, nombre_departamento FROM ct_departamentos WHERE telefono = :telefono LIMIT 1");
    $stmt_telefono->execute([':telefono' => $telefono]);
    $telefono_existente = $stmt_telefono->fetch(PDO::FETCH_ASSOC);
    
    if ($telefono_existente) {
        $campos_duplicados[] = 'telefono';
    }
    
    // Verificar si el CCT ya existe
    $stmt_cct = $conn->prepare("SELECT id_departamento, nombre_departamento FROM ct_departamentos WHERE cct = :cct LIMIT 1");
    $stmt_cct->execute([':cct' => $cct]);
    $cct_existente = $stmt_cct->fetch(PDO::FETCH_ASSOC);
    
    if ($cct_existente) {
        $campos_duplicados[] = 'cct';
    }
    
    // Si hay campos duplicados, guardar en sessionStorage y redirigir de vuelta
    if (count($campos_duplicados) > 0) {
        // Guardar datos en sessionStorage para restaurar el formulario
        $_SESSION['camposDuplicados'] = $campos_duplicados;
        $_SESSION['formData'] = [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'folio' => $folio,
            'folio_interno' => $folio_interno,
            'nombre_encargado' => $nombre_encargado,
            'cargo' => $cargo,
            'domicilio' => $domicilio,
            'cct' => $cct,
            'telefono_existente' => $telefono_existente ? $telefono_existente['nombre_departamento'] : null,
            'cct_existente' => $cct_existente ? $cct_existente['nombre_departamento'] : null
        ];
        
        // Redirigir de vuelta con parámetro de error
        header("Location: index.php?page=agregar&error=duplicados");
        exit;
    }
    
    // Insertar en la base (id_departamento es auto_increment)
    $stmt = $conn->prepare("INSERT INTO ct_departamentos 
        (nombre_departamento, telefono, folio, folio_interno, nombre_encargado, cargo, domicilio, cct)
        VALUES (:nombre, :telefono, :folio, :folio_interno, :nombre_encargado, :cargo, :domicilio, :cct)");

    $stmt->execute([
        ':nombre' => $nombre,
        ':telefono' => $telefono,
        ':folio' => $folio,
        ':folio_interno' => $folio_interno,
        ':nombre_encargado' => $nombre_encargado,
        ':cargo' => $cargo,
        ':domicilio' => $domicilio,
        ':cct' => $cct
    ]);

    // Redirigir de nuevo a consultar con bandera de éxito para mostrar toast
    header("Location: index.php?page=consultar&saved=1");
    exit;
    
} catch (PDOException $e) {
    echo "<script>
        alert('Error al guardar el departamento: " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
    exit;
}
?>
