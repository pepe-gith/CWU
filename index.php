<?php include('./config.php') ?>
<!-- Esta es la página principal de la Web -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5.3 -->
    <link href="/cwu/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="/cwu/libs/bootstrap-icons/font/bootstrap-icons.min.css">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="/cwu/CSS/style.css">
    <link rel="stylesheet" href="/cwu/CSS/index.css">
    <!-- <link rel="stylesheet" href="/cwu/CSS/index-mejorado.css"> -->

    <title>CWU - Celebrate with US</title>
</head>
<body>
    <!-- Vídeo de fondo -->
    <video class="video-fondo" src="./CSS/Img/confeti.mp4" autoplay loop muted></video>

    <!-- Header -->
    <?php include('./inc/header.php') ?>


    <main>
        <section id="opciones" class="opciones-section">
            <div class="container-fluid">
                <!-- <h2 class="section-title">Explora nuestras secciones</h2> -->

                <div class="cards-container">

                    <!-- Tarjeta 1: La Clínica -->
                    <div class="card-opcion">

                        <a class="card-link" href="./Vistas/Sala1View.php"> 

                            <div class="card-image">
                                <img src="./CSS/Img/SalaClinica.png" alt="La Clínica" class="card-img">
                                <div class="card-overlay"></div>
                            </div>

                            <div class="card-content">
                                <h3 class="card-title">La Clínica</h3>
                                <p class="card-description">Servicios de salud y bienestar especializados</p>
                                <span class="card-arrow">Explorar →</span>
                            </div>

                            <!-- <img class="opc" src="./CSS/Img/SalaClinica.png" width="50%">  -->
                        </a>

                    </div>

                    <!-- Tarjeta 2: ¡Feliz Cumpleaños! -->
                    <div class="card-opcion">
                        <a href="./Vistas/CumpleView.php" class="card-link">
                            <div class="card-image">
                                <img src="./CSS/Img/feliz.png" alt="¡Feliz Cumpleaños!" class="card-img">
                                <div class="card-overlay"></div>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title">¡Feliz Cumpleaños!</h3>
                                <p class="card-description">Planes y celebraciones para tu día especial</p>
                                <span class="card-arrow">Explorar →</span>
                            </div>
                        </a>
                    </div>
 
                    <!-- Tarjeta 3: La Biblioteca -->
                    <div class="card-opcion">
                        <a href="./Vistas/Sala2View.php" class="card-link">
                            <div class="card-image">
                                <img src="./CSS/Img/SalaLibreria.png" alt="La Biblioteca" class="card-img">
                                <div class="card-overlay"></div>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title">La Biblioteca</h3>
                                <p class="card-description">Ideas, recursos e inspiración para tus eventos</p>
                                <span class="card-arrow">Explorar →</span>
                            </div>
                        </a>
                    </div>

                </div>

            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include('./inc/footer.php') ?>

    <script src="/cwu/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
