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

<!-- Modal motivo cancelación -->
<div class="modal fade" id="modalMotivoCancelacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Cancelar reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-3">Indica el motivo de la cancelación — el cliente lo verá en su área.</p>
                <label class="form-label">Motivo <span class="text-danger">*</span></label>
                <textarea class="form-control" id="admin-motivo-cancelacion" rows="3" placeholder="Ej: indisponibilidad del local en esa fecha..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-cancelacion-admin">Confirmar cancelación</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal asignar empleados -->
<div class="modal fade" id="modalEmpleadosReserva" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Empleados asignados — <span id="modal-empleados-titulo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="lista-asignaciones" class="mb-4"></div>
                <hr>
                <p class="fw-semibold mb-3">Añadir empleado</p>
                <form id="form-asignar-empleado" novalidate>
                    <input type="hidden" name="id_reserva">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Empleado <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_empleado" id="select-empleado-reserva" required>
                                <option value="">Selecciona un empleado...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Rol en el evento</label>
                            <input type="text" class="form-control" name="rol_evento" placeholder="Ej: Monitor principal, Animador...">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">Añadir</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pagos -->
<div class="modal fade" id="modalPagosReserva" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pagos — <span id="modal-pagos-titulo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="lista-pagos" class="mb-3"></div>
                <div class="text-end fw-semibold mb-4" id="total-pagado"></div>
                <hr>
                <p class="fw-semibold mb-3">Registrar pago</p>
                <form id="form-registrar-pago" novalidate>
                    <input type="hidden" name="id_reserva">
                    <div class="row g-3">
                        <div class="col-sm-5">
                            <label class="form-label">Monto (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="monto" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label">Método <span class="text-danger">*</span></label>
                            <select class="form-select" name="metodo" required>
                                <option value="">Selecciona...</option>
                                <option value="bizum">Bizum</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="col-sm-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Añadir</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal editar reserva -->
<div class="modal fade" id="modalEditarReserva" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-editar-reserva" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Fecha del evento <span class="text-danger">*</span></label>
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
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
