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

<header class="header">    

    <!-- Logo/Título -->
    <img class="lin1" src="/cwu/public/assets/img/titulo.png"/>

    <!-- Navegación -->
    <nav>
        <a href="/cwu/index.php" class="nav-link">
            <i class="bi bi-house-door"></i> Inicio
        </a>


        <!-- <a href="/cwu/index.php">Inicio</a> | -->
        <?php if ($clienteLogueado): ?>
            <?php if ($nombreCliente !== ''): ?>
                <span class="saludo-usuario">
                     <i class="bi bi-person-circle"></i>
                    Hola, <?php echo htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            <?php endif; ?>

            <a href="/cwu/Vistas/cliente/InicioView.php" class="nav-link">
                <i class="bi bi-person-circle"></i> Mi área
            </a>
            
            <a href="#" class="nav-link logout" id="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
            <script>
                document.getElementById('btn-logout').addEventListener('click', function(e) {
                    e.preventDefault()
                    const fd = new FormData()
                    fd.append('action', 'cerrarSesion')
                    fetch('/cwu/Controladores/UsuarioControlador.php', { method: 'POST', body: fd })
                        .then(res => res.json())
                        .then(data => { if (data.redirect) location.href = data.redirect })
                })
            </script>

            <!-- <a href="/cwu/Vistas/SolEventoView.php">Mi area</a> |
            <a href="/cwu/Controladores/logout.php">Salir</a> -->

        <?php else: ?>

            <a href="/cwu/Vistas/auth/RegistroView.php" class="nav-link">
                <i class="bi bi-person-plus"></i> Crear cuenta
            </a>

            <a href="/cwu/Vistas/auth/AccesoView.php" class="nav-link">
                <i class="bi bi-box-arrow-in-right"></i> Acceso
            </a>

            <!-- <a href="/cwu/Vistas/RegistroView.php">Registro</a> |
            <a href="/cwu/Vistas/AccesoView.php">Acceso</a> -->

        <?php endif; ?>
    </nav>

</header>  