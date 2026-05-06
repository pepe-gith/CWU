<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include '../../inc/vite.php'; vite_assets(); ?>

<title>Cumpleaños</title>
</head>
<body>

    <!-- Header -->
    <?php include('../../inc/header.php') ?>

    <!-- MOTIVOS -->
    <section class="cards">
        <div class="card">
            <img src="/cwu/public/assets/img/thr.png">
            <h5>Una celebración única</h5>
            <p>Haz que ese día especial sea inolvidable. Organizamos cada detalle para que el protagonista
               y sus invitados disfruten de una experiencia completamente diferente a cualquier otra fiesta.</p>
        </div>
        <div class="card">
            <img src="/cwu/public/assets/img/thr1.png">
            <h5>Todo personalizado</h5>
            <p>Adaptamos la celebración a tus gustos: temática, decoración, actividades y más.
               Cuéntanos tu idea y nosotros la hacemos realidad para que el cumpleañero se sienta el rey o la reina del día.</p>
        </div>
        <div class="card">
            <img src="/cwu/public/assets/img/thr2.png">
            <h5>Sin preocupaciones</h5>
            <p>Olvídate del estrés de organizar. Nuestro equipo se encarga de todo: montaje, animación y desmontaje.
               Tú solo tienes que disfrutar con los tuyos.</p>
        </div>
    </section>
    <!-- END MOTIVOS -->

    <!-- BANNER -->
    <section class="banner">
        <div class="banner-content">
            <img class="header-img" src="/cwu/public/assets/img/feliz.png" width="588" height="245" />
        </div>
    </section>
    <!-- END BANNER -->

    <!-- DESCRIPCIÓN -->
    <section class="descSala">
        <h3 class="section-title">CUMPLEAÑOS</h3>
        <div class="descSala-card">
            <h5>Para todas las edades</h5>
            <p>Celebra tu cumpleaños con una experiencia que nadie olvidará. Disponemos de diferentes opciones:
               desde una fiesta temática con escape room incluido hasta una celebración más tranquila con juegos,
               animación y merienda. Nos adaptamos al número de invitados y a la edad del protagonista para
               garantizar la diversión de todos los asistentes.</p>
            <a href="/cwu/Vistas/auth/RegistroView.php" class="btn-primary-full">¡Reserva tu celebración!</a>
        </div>
    </section>
    <!-- END DESCRIPCIÓN -->

    <!-- Footer -->
    <?php include('../../inc/footer.php') ?>
</body>
</html>
