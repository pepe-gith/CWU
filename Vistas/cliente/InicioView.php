<?php
require_once __DIR__ . '/../../inc/sesion.php';
iniciarSesion();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$tituloPagina = 'Mi área';
$entradaVite = 'cliente/inicio';
$menuLateral  = menuCliente('resumen');

ob_start();
$nombre = htmlspecialchars($_SESSION['cliente']['nombre']);
?>
<div class="area-page">

    <h2 class="mb-1">Hola, <?php echo $nombre ?></h2>
    <p class="text-muted mb-5">¿Qué quieres hacer hoy?</p>

    <div class="area-shortcut-cards">
        <a href="/cwu/Vistas/cliente/SolEventoView.php" class="area-shortcut-card">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Nueva solicitud</span>
        </a>
        <a href="/cwu/Vistas/cliente/SolicitudesView.php" class="area-shortcut-card">
            <i class="bi bi-list-check"></i>
            <span>Mis solicitudes</span>
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h3 class="mb-0">Últimas solicitudes</h3>
        <a href="/cwu/Vistas/cliente/SolicitudesView.php" class="btn btn-outline-primary btn-sm">Ver todas →</a>
    </div>
    <div id="resumen-solicitudes"></div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
