<?php
include 'conexion_base.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) {
    $stmt = $conn->prepare('DELETE FROM ct_departamentos WHERE id_departamento = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: index.php?page=consultar&deleted=1');
exit;
?>

