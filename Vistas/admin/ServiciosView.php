<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Servicios';
$layoutEntry = 'admin/servicios';
$layoutMenu  = menuAdmin('servicios');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Servicios</h2>
    <p class="text-muted mb-4">Gestión de servicios disponibles para reservas</p>

    <div class="mb-3">
        <button class="btn btn-primary" id="btn-nuevo-servicio">
            <i class="bi bi-plus-circle me-1"></i> Nuevo servicio
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="tabla-servicios">
                <p class="text-muted p-4">Cargando...</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal crear / editar -->
<div class="modal fade" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-servicio-titulo">Nuevo servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-servicio" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" maxlength="150" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" maxlength="1000"></textarea>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Precio base (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="precio_base" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Capacidad <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="capacidad" min="1" step="1" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_categoria" id="select-categoria" required>
                                <option value="">Selecciona...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal confirmar eliminación -->
<div class="modal fade" id="modalEliminarServicio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Eliminar servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p>¿Seguro que quieres eliminar el servicio <strong id="nombre-servicio-eliminar"></strong>?</p>
                <p class="text-muted small">No se puede eliminar si tiene reservas asociadas.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-servicio">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
