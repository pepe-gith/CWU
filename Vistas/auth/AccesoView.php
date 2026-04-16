<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../../inc/vite.php'; vite_assets('auth/acceso'); ?>
    <title>Acceso</title>
</head>
<body>

    <?php include('../../inc/header.php') ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">

                <h1 class="mb-4">Acceso</h1>

                <div id="error-msg" class="alert alert-danger d-none" role="alert"></div>

                <form id="formComprobarAcceso">
                    <div class="mb-3">
                        <label for="nif" class="form-label">NIF</label>
                        <input type="text" class="form-control" name="nif" id="nif" placeholder="12345678A" required>
                    </div>
                    <div class="mb-4">
                        <label for="contra" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="contra" id="contra" placeholder="Contraseña" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                    </div>
                </form>

                <p class="text-center mt-3 text-muted">
                    ¿No tienes cuenta? <a href="/cwu/Vistas/auth/RegistroView.php">Regístrate</a>
                </p>

            </div>
        </div>
    </main>

    <?php include('../../inc/footer.php') ?>

</body>
</html>
