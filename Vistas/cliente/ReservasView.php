<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['cliente'])) {
    header('Location: /cwu/Vistas/auth/AccesoView.php');
    exit;
}

require_once '../../inc/helpers.php';
$layoutTitle = 'Mis reservas';
$layoutEntry = 'cliente/reservas';
$layoutMenu  = menuCliente('reservas');

ob_start();
?>
<div class="area-page">

    <h2 class="mb-1">Mis reservas</h2>
    <p class="text-muted mb-4">Aquí puedes ver todas tus reservas confirmadas y pendientes</p>

    <div id="lista-reservas">
        <p class="text-muted">Cargando...</p>
    </div>

</div>
<?php
$content = ob_get_clean();
include '../../inc/layout_area.php';
