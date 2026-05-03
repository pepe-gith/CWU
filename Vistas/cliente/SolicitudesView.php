<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Mis solicitudes';
$layoutEntry = 'cliente/solicitudes';
$layoutMenu  = menuCliente('solicitudes');

ob_start();
?>
<div class="area-page">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Mis solicitudes</h2>
        <a href="/cwu/Vistas/cliente/SolEventoView.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva solicitud
        </a>
    </div>

    <div id="lista-solicitudes">
        <p class="text-muted">Cargando...</p>
    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
