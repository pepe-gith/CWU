<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';

$idRol = (int) ($_SESSION['cliente']['id_rol'] ?? 3);
$layoutMenu = match($idRol) {
    1       => menuAdmin('perfil'),
    2       => menuEmpleado('perfil'),
    default => menuCliente('perfil'),
};

$layoutTitle = 'Mi perfil';
$layoutEntry = 'perfil/perfil';

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Mi perfil</h2>
    <p class="text-muted mb-5">Gestiona tus datos personales y contraseña</p>

    <div id="perfil-alert"></div>

    <div class="row g-4">

        <!-- Datos personales -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Datos personales</h5>
                    <form id="form-perfil" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIF</label>
                                <input type="text" class="form-control" id="nif" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Otro teléfono</label>
                                <input type="tel" class="form-control" id="otro_telefono" name="otro_telefono">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="btn-guardar">
                                <span class="spinner-border spinner-border-sm d-none me-1" id="spinner-perfil"></span>
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cambiar contraseña -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Cambiar contraseña</h5>
                    <form id="form-password" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Contraseña actual <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_actual" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_nueva" required minlength="8">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirmar nueva contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmar" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100" id="btn-password">
                            <span class="spinner-border spinner-border-sm d-none me-1" id="spinner-password"></span>
                            Actualizar contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
