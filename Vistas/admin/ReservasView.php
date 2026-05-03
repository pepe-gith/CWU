<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Reservas';
$layoutEntry = 'admin/reservas';
$layoutMenu  = menuAdmin('reservas');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Reservas</h2>
    <p class="text-muted mb-4">Gestiona las reservas confirmadas y pendientes</p>

    <!-- Filtros -->
    <div class="d-flex gap-2 mb-4">
        <button class="btn btn-sm btn-primary filtro-btn active" data-filtro="proximas">Próximas</button>
        <button class="btn btn-sm btn-outline-secondary filtro-btn" data-filtro="todas">Todas</button>
        <button class="btn btn-sm btn-outline-secondary filtro-btn" data-filtro="pasadas">Pasadas</button>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="tabla-reservas">
                <p class="text-muted p-4">Cargando...</p>
            </div>
        </div>
    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
