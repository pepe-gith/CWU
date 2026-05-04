<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Usuarios';
$layoutEntry = 'admin/usuarios';
$layoutMenu  = menuAdmin('usuarios');

ob_start();
?>
<script>window.SESSION_ID = <?= (int) $_SESSION['cliente']['id'] ?></script>
<div class="area-page">

    <h2 class="mb-1">Usuarios</h2>
    <p class="text-muted mb-4">Gestiona los clientes y empleados registrados</p>

    <div class="mb-3">
        <button class="btn btn-primary" id="btn-nuevo-usuario">
            <i class="bi bi-person-plus me-1"></i> Nuevo usuario
        </button>
    </div>

    <!-- Filtros -->
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center" id="filtros-rol">
        <button class="btn btn-sm btn-primary btn-todos active">Todos</button>
        <div class="ms-auto">
            <input type="search" id="buscador" class="form-control form-control-sm" placeholder="Buscar por nombre, NIF o email..." style="min-width:260px">
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="tabla-usuarios">
                <p class="text-muted p-4">Cargando...</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-usuario-titulo">Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="tabs-usuario">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-datos">Datos</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-acceso">Acceso</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-historial">Historial</button></li>
                </ul>
                <div class="tab-content">

                    <!-- Pestaña Datos -->
                    <div class="tab-pane fade show active" id="tab-datos">
                        <form id="form-editar-usuario" novalidate>
                            <input type="hidden" name="id">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="apellidos" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">NIF</label>
                                    <p class="form-control-plaintext" id="datos-nif"></p>
                                </div>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-primary">Guardar datos</button>
                            </div>
                        </form>
                    </div>

                    <!-- Pestaña Acceso -->
                    <div class="tab-pane fade" id="tab-acceso">
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" id="select-rol-modal"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Estado</label>
                            <span id="badge-activo"></span>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-primary" id="btn-cambiar-rol">Guardar rol</button>
                            <button class="btn btn-outline-danger" id="btn-toggle-activo"></button>
                        </div>
                    </div>

                    <!-- Pestaña Historial -->
                    <div class="tab-pane fade" id="tab-historial">
                        <div id="historial-contenido">
                            <p class="text-muted">Cargando...</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal nuevo usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-nuevo-usuario" novalidate>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos">
                        </div>
                        <div class="col-6">
                            <label class="form-label">NIF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nif" id="nuevo-nif" required>
                            <div class="invalid-feedback">NIF no válido.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="nuevo-email" required>
                            <div class="invalid-feedback">Email no válido.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="nuevo-password" required>
                            <div class="invalid-feedback">Mínimo 8 caracteres, una mayúscula, una minúscula y un número.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirm" id="nuevo-password-confirm" required>
                            <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_rol" id="select-rol-nuevo" required></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal confirmar cambio de rol de cliente -->
<div class="modal fade" id="modalConfirmarCambioRol" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Cambiar rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-2" id="texto-confirmar-rol"></p>
                <p class="mb-0 small text-muted">El historial quedará en la base de datos pero el usuario perderá acceso al área de cliente.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btn-confirmar-cambio-rol">Confirmar cambio</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal confirmar desactivación empleado -->
<div class="modal fade" id="modalConfirmarDesactivar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Desactivar empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-3">Este empleado tiene asignaciones en reservas futuras. Si lo desactivas, <strong>se eliminarán automáticamente:</strong></p>
                <ul id="lista-reservas-afectadas" class="list-group list-group-flush mb-0"></ul>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-desactivar">Desactivar de todas formas</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
