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
    <link  rel="stylesheet" href="/cwu/CSS/index.css" >

    <title>La Clínica</title>
</head>
<body>
    
    <!-- Header -->
    <?php include('../inc/header.php') ?>
    
    <!-- MOTIVOS -->
    <section class="cards">
        <div class="card">
            <img src="../CSS/Img/thr.png">
            <h5>Diversión Asegurada</h5>
            <p>Es importante salir de la rutina diaria y divertirse con otras personas. Disfrutarás del ambiente de cada sala
               que te transportará a una época o lugar diferente y no querrás que acabe.</p>
        </div>
        <div class="card">
            <img src="../CSS/Img/thr1.png">
            <h5>Trabaja en Equipo</h5>
            <p>Fomenta el trabajo en equipo. Tod@s tenéis que interactuar, para poder escapar de la sala debéis organizaros,
               comunicaros y ayudaros a resolver la clave y superar las misiones con éxito.</p>
        </div>
        <div class="card">
            <img src="../CSS/Img/thr2.png">
            <h5>Ejercita tu mente</h5>
            <p>Hay que darle al coco. Habrán acertijos que te son familiares o que, gracias a tus habilidades, se te dan bien. 
              Hallarás pruebas de todo tipo, confia en tu ingenio, creatividad y lógica.</p>
        </div>
    </section>
    <!-- END MOTIVOS -->        
    <!-- BANNER -->    
    <section class="banner">
        <div class="banner-content">
          <image class="header-img" src="../CSS/Img/clinica.jpg" width="588" heigt="245" />
        </div>
    </section>
    <!-- END BANNER -->    
    <!-- DESCRIPCIÓN SALA -->
    <section class="descSala">
        <h3 class="section-title">LA CLÍNICA</h3>
        <div class="descSala-card">
            <h5>Adultos y Familias</h5>
            <p>"La Clínica" del Dr Náser Felino está siendo investigado por el CDC, uno de sus compañeros le ha denunciado
               por realizar supuestamente experimentos altamente peligrosos. el doctor que ha dado el soplo a las autoridades ha desaparecido, 
               nuestro trabajo como agentes será encontrar al doctor desaparecido y averiguar que está ocurriendo en esta clínica.</p>
            <a href="/cwu/Vistas/RegistroView.php" class="btn-primary-full">Para vivir la aventura REGISTRATE YA</a>  
        </div>
    </section>
    <!-- END DESCRIPCIÓN SALA -->

    <!-- Footer -->
    <?php include('../inc/footer.php') ?>
    <script src="/cwu/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>