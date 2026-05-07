<?php
require_once __DIR__ . '/../../inc/sesion.php';
iniciarSesion();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$tituloPagina = 'Mis reservas';
$entradaVite = 'cliente/reservas';
$menuLateral  = menuCliente('reservas');

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

<!-- Modal notificar pago -->
<div class="modal fade" id="modalNotificarPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Notificar pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-notificar-pago" novalidate>
                <div class="modal-body pt-0">
                    <p class="text-muted mb-3">Indícanos el pago que has realizado y lo verificaremos en breve.</p>
                    <input type="hidden" name="id_reserva">
                    <div class="mb-3">
                        <label class="form-label">Monto (€) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="monto" min="0.01" step="0.01" required placeholder="Ej: 150.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Método <span class="text-danger">*</span></label>
                        <select class="form-select" name="metodo" required>
                            <option value="">Selecciona...</option>
                            <option value="bizum">Bizum</option>
                            <option value="transferencia">Transferencia bancaria</option>
                            <option value="efectivo">Efectivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia <span class="text-muted small">(opcional)</span></label>
                        <input type="text" class="form-control" name="referencia" placeholder="Ej: Bizum a 600 000 000, ref. XXXX">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar notificación</button>
                </div>
            </form>
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
