<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente']) || (int)($_SESSION['cliente']['id_rol'] ?? 0) !== 2) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Mi agenda';
$layoutEntry = 'empleado/agenda';
$layoutMenu  = menuEmpleado('agenda');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Mi agenda</h2>
    <p class="text-muted mb-4">Eventos asignados — acepta o rechaza cada asignación</p>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="tabla-agenda">
                <p class="text-muted p-4">Cargando...</p>
            </div>
        </div>
    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
