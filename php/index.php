<?php include 'conexion_base.php'; // Incluimos la conexión 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Telmex</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <!-- Barra superior -->
    <div class="topbar">
        <img src="imagenes/SEPE_USET.png" alt="Logo Superior" class="logo-superior">
    </div>

    <!-- Sidebar lateral -->
    <div class="sidebar">
        <h2><img src="imagenes/florColor2.png" alt="Telmex logo" class="logo-lateral"></h2>
        <ul>
            <li><a href="?page=consultar">Consultar escuelas</a></li>
            <li><a href="?page=comprobante">Generar comprobante</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="main">
        <?php if (isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
            <div class="toast" id="toast-success">Institución guardada con éxito</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
            <div class="toast" id="toast-updated">Institución actualizada con éxito</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
            <div class="toast toast-deleted" id="toast-deleted">Institución eliminada con éxito</div>
        <?php endif; ?>
        <?php
        $page = $_GET['page'] ?? 'consultar'; // Por defecto mostrar consultar
        if ($page == 'consultar') {
            include 'consultar.php';
        } elseif ($page == 'comprobante') {
            include 'comprobante.php';
        } elseif ($page == 'agregar') {
            include 'agregar.php';
        } elseif ($page == 'editar') {
            include 'editar.php';
        }
        ?>
    </div>

    <script src="js/scripts.js"></script>
</body>

</html>