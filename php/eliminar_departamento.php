<?php
include 'conexion_base.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
    try {
        // Iniciar transacción
        $conn->beginTransaction();
        
        try {
            // Verificar si hay comprobantes relacionados (opcional: eliminar comprobantes relacionados)
            // Si la tabla Pagos tiene ON DELETE CASCADE, esto se hará automáticamente
            // Si no, podríamos eliminar los comprobantes primero aquí
            
            // Eliminar el departamento
            $stmt = $conn->prepare('DELETE FROM ct_departamentos WHERE id_departamento = :id');
            $stmt->execute([':id' => $id]);
            
            // Verificar que se eliminó correctamente
            if ($stmt->rowCount() === 0) {
                throw new PDOException('No se pudo eliminar el departamento. Puede que no exista.');
            }
            
            // Confirmar transacción
            $conn->commit();
            
            header('Location: index.php?page=consultar&deleted=1');
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
            alert('Error al eliminar el departamento: " . addslashes($e->getMessage()) . "');
            window.location='index.php?page=consultar';
        </script>";
        exit;
    }
} else {
    header('Location: index.php?page=consultar');
    exit;
}
?>

