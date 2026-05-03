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
    <div class="d-flex flex-wrap gap-3 mb-4 align-items-end">
        <div>
            <label class="form-label mb-1 small fw-medium">Fecha</label>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary filtro-fecha active" data-filtro="proximas">Próximas</button>
                <button class="btn btn-sm btn-outline-secondary filtro-fecha" data-filtro="todas">Todas</button>
                <button class="btn btn-sm btn-outline-secondary filtro-fecha" data-filtro="pasadas">Pasadas</button>
            </div>
        </div>
        <div>
            <label class="form-label mb-1 small fw-medium">Estado</label>
            <select class="form-select form-select-sm" id="filtro-estado" style="min-width:150px">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>
        <div>
            <label class="form-label mb-1 small fw-medium">Cliente</label>
            <input type="text" class="form-control form-control-sm" id="filtro-cliente" placeholder="Nombre o apellidos..." style="min-width:200px">
        </div>
        <button class="btn btn-sm btn-outline-secondary" id="btn-limpiar">Limpiar</button>
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
