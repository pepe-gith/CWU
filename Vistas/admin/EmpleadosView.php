<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Empleados';
$layoutEntry = 'admin/empleados';
$layoutMenu  = menuAdmin('empleados');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Empleados</h2>
    <p class="text-muted mb-4">Gestiona los empleados y sus datos laborales</p>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" id="btn-nuevo-empleado">
            <i class="bi bi-plus-circle me-1"></i> Nuevo empleado
        </button>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="mostrar-inactivos">
            <label class="form-check-label text-muted" for="mostrar-inactivos">Mostrar inactivos</label>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="tabla-empleados">
                <p class="text-muted p-4">Cargando...</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-empleado-titulo">Nuevo empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-empleado" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Usuario <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_usuario" id="select-usuario-empleado" required>
                            <option value="">Selecciona un usuario...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <input type="text" class="form-control" name="especialidad" placeholder="Ej: Animación infantil, Escape room...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio por hora (€) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="precio_por_hora" min="0" step="0.01" required>
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
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
