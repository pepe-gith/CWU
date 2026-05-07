<?php
require_once __DIR__ . '/../../inc/sesion.php';
iniciarSesion();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 1) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$tituloPagina = 'Dashboard';
$entradaVite = 'admin/dashboard';
$menuLateral  = menuAdmin('dashboard');

ob_start();
$nombre = htmlspecialchars($_SESSION['cliente']['nombre'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>
<div class="area-page">

    <h2 class="mb-1">Dashboard</h2>
    <p class="text-muted mb-4">Hola, <?php echo $nombre ?>. Aquí tienes el resumen de hoy.</p>

    <!-- KPIs -->
    <div class="row g-3 mb-5" id="kpis">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="admin-kpi-value" id="kpi-solicitudes">—</div>
                        <div class="admin-kpi-label">Solicitudes pendientes</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon bg-success-subtle text-success">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="admin-kpi-value" id="kpi-reservas">—</div>
                        <div class="admin-kpi-label">Reservas próximas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="admin-kpi-value" id="kpi-clientes">—</div>
                        <div class="admin-kpi-label">Clientes registrados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon bg-info-subtle text-info">
                        <i class="bi bi-currency-euro"></i>
                    </div>
                    <div>
                        <div class="admin-kpi-value" id="kpi-ingresos">—</div>
                        <div class="admin-kpi-label">Ingresos este mes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta reservas sin empleado -->
    <div id="alerta-sin-empleado" class="d-none mb-4"></div>

    <!-- Accesos rápidos -->
    <div class="d-flex flex-wrap gap-2 mb-5">
        <a href="/cwu/Vistas/admin/SolicitudesView.php" class="btn btn-outline-warning">
            <i class="bi bi-inbox me-1"></i> Gestionar solicitudes
        </a>
        <a href="/cwu/Vistas/admin/ReservasView.php" class="btn btn-outline-success">
            <i class="bi bi-calendar-check me-1"></i> Ver reservas
        </a>
        <a href="/cwu/Vistas/admin/UsuariosView.php" class="btn btn-outline-primary">
            <i class="bi bi-people me-1"></i> Usuarios
        </a>
    </div>

    <div class="row g-4">

        <!-- Últimas solicitudes pendientes -->
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Solicitudes pendientes</h5>
                        <a href="/cwu/Vistas/admin/SolicitudesView.php" class="btn btn-sm btn-outline-primary">Ver todas →</a>
                    </div>
                    <div id="tabla-solicitudes">
                        <p class="text-muted">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximas reservas -->
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Próximas reservas</h5>
                        <a href="/cwu/Vistas/admin/ReservasView.php" class="btn btn-sm btn-outline-primary">Ver todas →</a>
                    </div>
                    <div id="tabla-reservas">
                        <p class="text-muted">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
