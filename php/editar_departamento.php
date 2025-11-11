<?php
session_start();
include 'conexion_base.php';

$id = (int)$_POST['id'];
$nombre = $_POST['nombre_departamento'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$folio = $_POST['folio'] ?? '';
$folio_interno = $_POST['folio_interno'] ?? '';
$nombre_encargado = $_POST['nombre_encargado'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$domicilio = $_POST['domicilio'] ?? '';
$cct = isset($_POST['cct']) ? strtoupper(trim($_POST['cct'])) : '';

try {
    $campos_duplicados = [];
    $telefono_existente = null;
    $cct_existente = null;
    
    // Verificar si el teléfono ya existe en otro departamento
    $stmt_telefono = $conn->prepare("SELECT id_departamento, nombre_departamento FROM ct_departamentos WHERE telefono = :telefono AND id_departamento != :id LIMIT 1");
    $stmt_telefono->execute([':telefono' => $telefono, ':id' => $id]);
    $telefono_existente = $stmt_telefono->fetch(PDO::FETCH_ASSOC);
    
    if ($telefono_existente) {
        $campos_duplicados[] = 'telefono';
    }
    
    // Verificar si el CCT ya existe en otro departamento
    $stmt_cct = $conn->prepare("SELECT id_departamento, nombre_departamento FROM ct_departamentos WHERE cct = :cct AND id_departamento != :id LIMIT 1");
    $stmt_cct->execute([':cct' => $cct, ':id' => $id]);
    $cct_existente = $stmt_cct->fetch(PDO::FETCH_ASSOC);
    
    if ($cct_existente) {
        $campos_duplicados[] = 'cct';
    }
    
    // Si hay campos duplicados, guardar en sesión y redirigir de vuelta
    if (count($campos_duplicados) > 0) {
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
        
        header("Location: index.php?page=editar&id=$id&error=duplicados");
        exit;
    }
    
    // Iniciar transacción
    $conn->beginTransaction();
    
    try {
        // Si no hay duplicados, actualizar el departamento
        $stmt = $conn->prepare("UPDATE ct_departamentos SET 
            nombre_departamento = :nombre,
            telefono = :telefono,
            folio = :folio,
            folio_interno = :folio_interno,
            nombre_encargado = :nombre_encargado,
            cargo = :cargo,
            domicilio = :domicilio,
            cct = :cct
            WHERE id_departamento = :id");

        $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':folio' => $folio,
            ':folio_interno' => $folio_interno,
            ':nombre_encargado' => $nombre_encargado,
            ':cargo' => $cargo,
            ':domicilio' => $domicilio,
            ':cct' => $cct
        ]);

        // Confirmar transacción
        $conn->commit();

        header('Location: index.php?page=consultar&updated=1');
        exit;
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $conn->rollBack();
        throw $e; // Re-lanzar la excepción para que sea capturada por el catch externo
    }
    
} catch (PDOException $e) {
    // Si hay una transacción activa, revertirla
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "<script>
        alert('Error al actualizar el departamento: " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
    exit;
}
?>

