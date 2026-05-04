<?php
session_start();
if (!empty($_SESSION['cliente'])) {
    $rol = (int)($_SESSION['cliente']['id_rol'] ?? 3);
    $redirect = match($rol) {
        1 => '/cwu/Vistas/admin/DashboardView.php',
        2 => '/cwu/Vistas/empleado/AgendaView.php',
        default => '/cwu/Vistas/cliente/InicioView.php',
    };
    header("Location: $redirect");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../../inc/vite.php'; vite_assets('auth/registro'); ?>
    <title>Registro</title>
</head>
<body>

    <?php include('../../inc/header.php') ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">

                <h1 class="mb-4">Crear cuenta</h1>

                <div id="error-msg" class="alert alert-danger d-none" role="alert"></div>
                <div id="success-msg" class="alert alert-success d-none" role="alert"></div>

                <form id="formRegistro" autocomplete="on" novalidate>

                    <div class="mb-3">
                        <label for="nif" class="form-label">NIF 
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="nif" id="nif"
                            placeholder="12345678A" pattern="[0-9]{8}[A-Z]{1}" maxlength="9" required>
                        <div class="invalid-feedback">Campo obligatorio.</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                placeholder="Nombre" maxlength="100" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>
                        <div class="col">
                            <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" id="apellidos"
                                placeholder="Apellidos" maxlength="150" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="movil1" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="movil1" id="movil1"
                                placeholder="612345678" pattern="[0-9]{9}" maxlength="9" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>
                        <div class="col">
                            <label for="movil2" class="form-label">Otro teléfono</label>
                            <input type="tel" class="form-control" name="movil2" id="movil2"
                                placeholder="612345678" pattern="[0-9]{9}" maxlength="9">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email1" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email1" id="email1"
                            placeholder="correo@ejemplo.com" maxlength="100" required>
                        <div class="invalid-feedback">Campo obligatorio.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="Contraseña" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            minlength="8" required>
                        <div class="form-text">Mínimo 8 caracteres, una mayúscula y un número.</div>
                        <div class="invalid-feedback">La contraseña no cumple los requisitos.</div>
                        <div class="valid-feedback">Contraseña válida</div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="direccion" id="direccion"
                            placeholder="Calle, número, ciudad" maxlength="255" required>
                        <div class="invalid-feedback">Campo obligatorio.</div>
                    </div>

                    <div class="mb-4">
                        <label for="como" class="form-label">¿Cómo nos has conocido?</label>
                        <input type="text" class="form-control" name="como" id="como"
                            placeholder="Redes sociales, amigos..." maxlength="100">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                    </div>

                </form>

                <p class="text-center mt-3 text-muted">
                    ¿Ya tienes cuenta? <a href="/cwu/Vistas/auth/AccesoView.php">Inicia sesión</a>
                </p>

            </div>
        </div>
    </main>

    <?php include('../../inc/footer.php') ?>

</body>
</html>
