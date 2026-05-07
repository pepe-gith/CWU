<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include '../../inc/vite.php'; vite_assets(); ?>

<title>Eventos y Fiestas</title>
</head>
<body>

    <!-- Header -->
    <?php include('../../inc/header.php') ?>

    <!-- Motivos -->
    <section class="cards">
        <div class="card">
            <img src="/cwu/public/assets/img/thr.png">
            <h5>Ambiente inigualable</h5>
            <p>Nuestras instalaciones están diseñadas para crear el ambiente perfecto en cualquier tipo de evento.
               Desde una pequeña reunión hasta una gran fiesta, garantizamos una experiencia que sorprenderá a todos.</p>
        </div>
        <div class="card">
            <img src="/cwu/public/assets/img/thr1.png">
            <h5>Team Building</h5>
            <p>Fortalece los lazos de tu equipo con actividades diseñadas para fomentar la colaboración y la comunicación.
               Nuestras dinámicas de grupo son perfectas para empresas que buscan motivar a sus empleados.</p>
        </div>
        <div class="card">
            <img src="/cwu/public/assets/img/thr2.png">
            <h5>Organización completa</h5>
            <p>Nos encargamos de cada detalle para que tú solo tengas que disfrutar. Coordinamos el espacio,
               la animación y las actividades para que tu evento salga a la perfección.</p>
        </div>
    </section>

    <!-- Banner -->
    <section class="banner">
        <div class="banner-content">
            <img class="header-img" src="/cwu/public/assets/img/fiesta.png" width="588" height="245" />
        </div>
    </section>

    <!-- Descripción -->
    <section class="descSala">
        <h3 class="section-title">EVENTOS Y FIESTAS</h3>
        <div class="descSala-card">
            <h5>Empresas, grupos y celebraciones</h5>
            <p>Organizamos todo tipo de eventos: despedidas de soltero/a, reuniones de empresa, celebraciones familiares
               y mucho más. Combinamos actividades de escape room, juegos de equipo y animación para crear una experiencia
               a medida. Cuéntanos qué tienes en mente y diseñamos juntos el evento perfecto para tu grupo.</p>
            <a href="/cwu/Vistas/auth/RegistroView.php" class="btn-primary-full">¡Organiza tu evento!</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include('../../inc/footer.php') ?>
</body>
</html>
