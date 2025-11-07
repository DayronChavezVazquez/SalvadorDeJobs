<?php include 'conexion_base.php'; // Incluimos la conexión 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Telmex</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Estilos personalizados -->
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
        <ul class="list-unstyled">
            <li class="mb-3"><a href="?page=consultar" class="text-white text-decoration-none fw-bold">Consultar escuelas</a></li>
            <li class="mb-3"><a href="?page=comprobante" class="text-white text-decoration-none fw-bold">Generar comprobante</a></li>
            <li class="mb-3"><a href="?page=consulta_comprobante" class="text-white text-decoration-none fw-bold">Consulta de comprobante Telmex</a></li>
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
        } elseif ($page == 'consulta_comprobante') {
            include 'consulta_comprobante.php';
        } elseif ($page == 'agregar') {
            include 'agregar.php';
        } elseif ($page == 'editar') {
            include 'editar.php';
        }
        ?>
    </div>

    <script src="js/scripts.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>