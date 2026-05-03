<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Solicitudes';
$layoutEntry = 'admin/solicitudes';
$layoutMenu  = menuAdmin('solicitudes');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Solicitudes</h2>
    <p class="text-muted mb-4">Gestiona las solicitudes de eventos recibidas</p>

    <!-- Filtros -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <button class="btn btn-sm btn-primary filtro-btn active" data-estado="">Todas</button>
        <button class="btn btn-sm btn-outline-warning filtro-btn" data-estado="pendiente">Pendientes</button>
        <button class="btn btn-sm btn-outline-info filtro-btn" data-estado="presupuestada">Presupuestadas</button>
        <button class="btn btn-sm btn-outline-success filtro-btn" data-estado="aceptada">Aceptadas</button>
        <button class="btn btn-sm btn-outline-danger filtro-btn" data-estado="rechazada">Rechazadas</button>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="tabla-solicitudes">
                <p class="text-muted p-4">Cargando...</p>
            </div>
        </div>
    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
