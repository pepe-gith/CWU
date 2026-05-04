<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Calendario';
$layoutEntry = 'admin/calendario';
$layoutMenu  = menuAdmin('calendario');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Calendario</h2>
    <p class="text-muted mb-3">Vista mensual de reservas y solicitudes</p>

    <div class="d-flex gap-3 mb-4 flex-wrap">
        <span class="d-flex align-items-center gap-2"><span style="width:14px;height:14px;border-radius:3px;background:#198754;display:inline-block"></span> Reserva confirmada</span>
        <span class="d-flex align-items-center gap-2"><span style="width:14px;height:14px;border-radius:3px;background:#ffc107;display:inline-block"></span> Solicitud pendiente</span>
        <span class="d-flex align-items-center gap-2"><span style="width:14px;height:14px;border-radius:3px;background:#dc3545;display:inline-block"></span> Reserva cancelada</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div id="calendario"></div>
        </div>
    </div>

</div>

<!-- Modal detalle evento -->
<div class="modal fade" id="modalDetalleEvento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalle-titulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalle-cuerpo"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
