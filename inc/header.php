<?php
require_once __DIR__ . '/sesion.php';

$clienteLogueado = false;
$nombreCliente = '';

if (session_status() === PHP_SESSION_ACTIVE || isset($_COOKIE[session_name()])) {
    iniciarSesion();
    $clienteLogueado = isset($_SESSION['cliente']) && !empty($_SESSION['cliente']);
    $nombreCliente = $clienteLogueado ? (string) ($_SESSION['cliente']['nombre'] ?? '') : '';
}
?>

<header class="header">    

    <!-- Logo/Título -->
    <img class="lin1" src="/cwu/public/assets/img/titulo.png"/>

    <!-- Navegación -->
    <nav>
        <?php

        /* Mostrar enlace de inicio solo para roles que no sean admin ni empleado */
        $rolNav = (int) ($_SESSION['cliente']['id_rol'] ?? 0);
        if ($rolNav !== 1 && $rolNav !== 2):
        ?>
        <a href="/cwu/index.php" class="nav-link">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <?php endif; ?>


        <!-- <a href="/cwu/index.php">Inicio</a> | -->
        <?php if ($clienteLogueado): ?>

            <?php

                $rol = (int) ($_SESSION['cliente']['id_rol'] ?? 0);
                
                $areaHref = match($rol) {
                    1       => '/cwu/Vistas/admin/DashboardView.php',
                    2       => '/cwu/Vistas/empleado/AgendaView.php',
                    default => '/cwu/Vistas/cliente/InicioView.php',
                };

            ?>

            <a href="<?= $areaHref ?>" class="nav-link">
                <i class="bi bi-grid"></i> Mi área
            </a>

            <div class="dropdown">
                <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                    Hola, <?php echo htmlspecialchars($nombreCliente); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="/cwu/Vistas/perfil/PerfilView.php">
                            <i class="bi bi-person me-2"></i> Mi perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="btn-logout">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
            <script>
                document.getElementById('btn-logout').addEventListener('click', function(e) {
                    e.preventDefault();

                    const fd = new FormData();
                    fd.append('action', 'cerrarSesion');

                    fetch('/cwu/Controladores/UsuarioControlador.php', { method: 'POST', body: fd })
                        .then(res => res.json())
                        .then(data => { 
                            if (data.redirect) 
                                location.href = data.redirect   ;
                        })
                });
            </script>

        <?php else: ?>

            <a href="/cwu/Vistas/auth/AccesoView.php" class="nav-link">
                <i class="bi bi-box-arrow-in-right"></i> Acceso
            </a>

            <a href="/cwu/Vistas/auth/RegistroView.php" class="nav-link">
                <i class="bi bi-person-plus"></i> Crear cuenta
            </a>

            <!-- <a href="/cwu/Vistas/RegistroView.php">Registro</a> |
            <a href="/cwu/Vistas/AccesoView.php">Acceso</a> -->

        <?php endif; ?>
    </nav>

</header>  