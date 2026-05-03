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

<!-- Modal crear reserva -->
<div class="modal fade" id="modalReserva" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-reserva" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="id_usuario">
                    <input type="hidden" name="id_solicitud">
                    <div id="modal-alert"></div>
                    <div class="mb-3">
                        <label class="form-label">Fecha del evento</label>
                        <input type="date" class="form-control" name="fecha_evento" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_inicio" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hora fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_fin" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Servicio <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_servicio" id="select-servicio" required>
                            <option value="">Cargando servicios...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nº asistentes <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="num_asistentes" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-crear-reserva">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="spinner-reserva"></span>
                        Crear reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
