<?php
$clienteLogueado = false;
$nombreCliente = '';

if (session_status() === PHP_SESSION_ACTIVE || isset($_COOKIE[session_name()])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $clienteLogueado = isset($_SESSION['cliente']) && !empty($_SESSION['cliente']);
    $nombreCliente = $clienteLogueado ? (string) ($_SESSION['cliente']['nombre'] ?? '') : '';
}
?>

<section class="header">    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link  rel="stylesheet" href="/cwu/CSS/index.css" >
    <img class="lin1" src="/cwu/CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/index.php">Inicio</a> |
        <?php if ($clienteLogueado): ?>
            <?php if ($nombreCliente !== ''): ?>
                <span>Hola, <?php echo htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8'); ?></span> |
            <?php endif; ?>
            <a href="/cwu/Vistas/SolEventoView.php">Mi area</a> |
            <a href="/cwu/Controladores/logout.php">Salir</a>
        <?php else: ?>
            <a href="/cwu/Vistas/RegistroView.php">Registro</a> |
            <a href="/cwu/Vistas/AccesoView.php">Acceso</a>
        <?php endif; ?>
    </nav>
</section>  