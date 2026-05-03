<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Mis reservas';
$layoutEntry = 'cliente/reservas';
$layoutMenu  = menuCliente('reservas');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Mis reservas</h2>
    <p class="text-muted mb-4">Aquí puedes ver todas tus reservas confirmadas y pendientes</p>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-info-circle-fill"></i>
        <span>Las reservas solo pueden cancelarse con más de <strong>24 horas</strong> de antelación.</span>
    </div>

    <div id="lista-reservas">
        <p class="text-muted">Cargando...</p>
    </div>

</div>

<!-- Modal solicitar cambio -->
<div class="modal fade" id="modalCambio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Solicitar cambio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-3">Explícanos qué quieres cambiar y nos pondremos en contacto contigo.</p>
                <label class="form-label">Motivo <span class="text-danger">*</span></label>
                <textarea class="form-control" id="motivo-cambio" rows="3" placeholder="Ej: necesito cambiar la fecha al día siguiente..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-cambio">Enviar solicitud</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal confirmación cancelar -->
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">¿Cancelar reserva?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-3">Esta acción no se puede deshacer.</p>
                <label class="form-label">Motivo <span class="text-muted small">(opcional)</span></label>
                <textarea class="form-control" id="motivo-cancelacion" rows="2" placeholder="Ej: cambio de planes, enfermedad..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Volver</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirmar-cancelar">Sí, cancelar</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
