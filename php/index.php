<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion_base.php'; // Incluimos la conexión 
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
            <li class="mb-3"><a href="?page=consultar" class="text-white text-decoration-none fw-bold menu-link" data-page="consultar">Consultar escuelas</a></li>
            <li class="mb-3"><a href="?page=comprobante" class="text-white text-decoration-none fw-bold menu-link" data-page="comprobante">Generar comprobante</a></li>
            <li class="mb-3"><a href="?page=consulta_comprobante" class="text-white text-decoration-none fw-bold menu-link" data-page="consulta_comprobante">Consulta de comprobante Telmex</a></li>
            <li class="mb-3"><a href="?page=consulta_comprobante_general" class="text-white text-decoration-none fw-bold menu-link" data-page="consulta_comprobante_general">Consulta de comprobante general</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="main">
        <!-- Alertas de éxito/error -->
        <div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>
        
        <?php if (isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
            <script>
            $(document).ready(function() {
                mostrarAlertaBonita('¡Éxito!', 'La institución ha sido guardada correctamente.', 'success');
            });
            </script>
        <?php endif; ?>
        <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
            <script>
            $(document).ready(function() {
                mostrarAlertaBonita('¡Actualizado!', 'La institución ha sido actualizada correctamente.', 'success');
            });
            </script>
        <?php endif; ?>
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
            <script>
            $(document).ready(function() {
                mostrarAlertaBonita('¡Eliminado!', 'La institución ha sido eliminada correctamente.', 'danger');
            });
            </script>
        <?php endif; ?>
        
        <style>
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        </style>
        <script>
        function mostrarAlertaBonita(titulo, mensaje, tipo) {
            tipo = tipo || 'success';
            var bgColor = tipo === 'success' ? '#28a745' : tipo === 'danger' ? '#dc3545' : '#17a2b8';
            var icon = tipo === 'success' ? 
                '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 2.384 5.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>' :
                '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>';
            
            var html = '<div class="alert alert-' + tipo + ' shadow-lg" role="alert" style="background: linear-gradient(135deg, ' + bgColor + ' 0%, ' + bgColor + 'dd 100%); color: white; border: none; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease-out;">';
            html += '<div class="d-flex align-items-start">';
            html += '<div style="flex-shrink: 0; margin-right: 15px; margin-top: 2px;">' + icon + '</div>';
            html += '<div style="flex: 1;">';
            html += '<h5 class="mb-1" style="color: white; font-weight: bold; font-size: 18px; margin: 0;">' + titulo + '</h5>';
            html += '<p class="mb-0" style="color: white; font-size: 14px; opacity: 0.95; margin-top: 5px;">' + mensaje + '</p>';
            html += '</div>';
            html += '<button type="button" class="btn-close btn-close-white" onclick="$(this).closest(\'.alert\').fadeOut(300, function(){$(this).remove();})" style="margin-left: 10px;"></button>';
            html += '</div>';
            html += '</div>';
            
            $('#alertContainer').append(html);
            
            setTimeout(function() {
                $('#alertContainer .alert').last().fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
        </script>
        <?php
        $page = $_GET['page'] ?? 'consultar'; // Por defecto mostrar consultar
        if ($page == 'consultar') {
            include 'consultar.php';
        } elseif ($page == 'comprobante') {
            include 'comprobante.php';
        } elseif ($page == 'consulta_comprobante') {
            include 'consulta_comprobante.php';
        } elseif ($page == 'consulta_comprobante_general') {
            include 'consulta_comprobante_general.php';
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
    <script>
    // Resaltar la opción del menú activa
    $(document).ready(function() {
        var currentPage = new URLSearchParams(window.location.search).get('page') || 'consultar';
        $('.menu-link[data-page="' + currentPage + '"]').addClass('active');
    });
    </script>
</body>

</html>