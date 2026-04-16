<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../../inc/vite.php'; vite_assets('cliente/solEvento'); ?>
    <title>Solicitar Evento</title>
</head>
<body>

    <?php include('../../inc/header.php') ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">

                <h1 class="mb-1">Solicitar presupuesto</h1>
                <p class="text-muted mb-4">Cliente: <strong><?php echo htmlspecialchars($_SESSION['cliente']['NIF'] ?? '') ?></strong></p>

                <form action="/cwu/Controladores/crearCliente.php" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Tipo de evento</label>
                        <select class="form-select" name="tipo_evento" required>
                            <option value="" selected disabled>Elige tipo de evento</option>
                            <option value="1">Cumpleaños</option>
                            <option value="2">Escape Room</option>
                            <option value="3">Fiesta</option>
                            <option value="4">Reunión Familiar</option>
                            <option value="5">Evento Empresa</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre y edad del protagonista</label>
                        <input type="text" class="form-control" name="nombrepro" id="nombrepro"
                            pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð -]"
                            title="Solo puedes introducir letras"
                            placeholder="Ej: Ana, 8 años">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total de participantes <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="partic" id="partic"
                            min="1" max="16" placeholder="Máximo 16" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Escoge el día <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2">Los días no disponibles aparecen deshabilitados</p>
                        <input type="hidden" name="fecha_evento" id="fecha_evento">
                        <div id="cal" class="mx-auto"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sala Escape Room</label>
                        <select class="form-select" name="sala">
                            <option value="" selected disabled>Selecciona sala</option>
                            <option value="1">Clínica</option>
                            <option value="2">Librería</option>
                            <option value="3">Clínica y Librería</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Realidad Virtual</label>
                        <select class="form-select" name="rv">
                            <option value="1">Con Realidad Virtual</option>
                            <option value="2">Sin Realidad Virtual</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tarta</label>
                        <select class="form-select" name="tarta">
                            <option value="1">Con tarta (gratis en cumpleaños)</option>
                            <option value="2">Sin tarta</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Enviar solicitud</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <?php include('../../inc/footer.php') ?>

</body>
</html>
