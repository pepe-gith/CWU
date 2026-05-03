<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Nueva solicitud';
$layoutEntry = 'cliente/solEvento';
$layoutMenu  = menuCliente('solEvento');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Solicitar presupuesto</h2>
    <p class="text-muted mb-4">Cliente: <strong><?php echo htmlspecialchars($_SESSION['cliente']['NIF'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></p>

    <div class="row">
        <div class="col-12 col-lg-8">

            <div id="error-msg" class="alert alert-danger d-none"></div>
            <div id="success-msg" class="alert alert-success d-none"></div>

            <form id="form-event" method="POST" novalidate>

                <div class="mb-3">
                    <label class="form-label">Tipo de evento <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo_evento" id="tipo_evento" required>
                        <option value="" selected disabled>Cargando...</option>
                    </select>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre y edad del protagonista <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombre_protagonista" id="nombre_protagonista"
                        placeholder="Ej: Ana, 8 años" required>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total de participantes <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="num_participantes" id="num_participantes"
                        min="1" max="16" placeholder="Máximo 16" required>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Escoge el día <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-2">Los días no disponibles aparecen deshabilitados</p>
                    <input type="hidden" name="fecha_evento" id="fecha_evento" required>
                    <div id="cal" class="mx-auto"></div>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sala Escape Room</label>
                    <select class="form-select" name="sala" id="sala" required>
                        <option value="" selected disabled>Selecciona sala</option>
                        <option value="1">Clínica</option>
                        <option value="2">Librería</option>
                        <option value="3">Clínica y Librería</option>
                    </select>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Realidad Virtual</label>
                    <select class="form-select" name="realidad_virtual" id="realidad_virtual" required>
                        <option value="" selected disabled>Selecciona opción</option>
                        <option value="1">Con Realidad Virtual</option>
                        <option value="2">Sin Realidad Virtual</option>
                    </select>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Tarta</label>
                    <select class="form-select" name="tarta" id="tarta" required>
                        <option value="" selected disabled>Selecciona opción</option>
                        <option value="1">Con tarta (gratis en cumpleaños)</option>
                        <option value="2">Sin tarta</option>
                    </select>
                    <div class="invalid-feedback">Campo obligatorio.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Observaciones <span class="text-muted small">(opcional)</span></label>
                    <textarea class="form-control" name="observaciones" rows="3"
                        placeholder="Alergias, necesidades especiales, decoración..."></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Enviar solicitud</button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
