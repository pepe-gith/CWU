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
    <p class="text-muted mb-4">Gestiona los datos laborales de los empleados</p>

    <div class="d-flex justify-content-end mb-3">
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

<!-- Modal editar empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-empleado-titulo">Editar empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-empleado" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <input type="text" class="form-control" name="especialidad" placeholder="Ej: Animación infantil, Escape room...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio por hora (€)</label>
                        <input type="number" class="form-control" name="precio_por_hora" min="0" step="0.01">
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
