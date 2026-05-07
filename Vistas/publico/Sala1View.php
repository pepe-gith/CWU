<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include '../../inc/vite.php'; vite_assets(); ?>

<title>La Clínica</title>
</head>
<body>
    
    <!-- Header -->
    <?php include('../../inc/header.php') ?>
    
    <!-- Motivos -->
    <section class="cards">
        <div class="card">
            <img src="/cwu/public/assets/img/thr.png">
            <h5>Diversión Asegurada</h5>
            <p>Es importante salir de la rutina diaria y divertirse con otras personas. Disfrutarás del ambiente de cada sala
               que te transportará a una época o lugar diferente y no querrás que acabe.</p>
        </div>
        <div class="card">
            <img src="/cwu/public/assets/img/thr1.png">
            <h5>Trabaja en Equipo</h5>
            <p>Fomenta el trabajo en equipo. Tod@s tenéis que interactuar, para poder escapar de la sala debéis organizaros,
               comunicaros y ayudaros a resolver la clave y superar las misiones con éxito.</p>
        </div>
        <div class="card">
            <img src="/cwu/public/assets/img/thr2.png">
            <h5>Ejercita tu mente</h5>
            <p>Hay que darle al coco. Habrán acertijos que te son familiares o que, gracias a tus habilidades, se te dan bien. 
              Hallarás pruebas de todo tipo, confia en tu ingenio, creatividad y lógica.</p>
        </div>
    </section>

    <!-- Banner -->    
    <section class="banner">
        <div class="banner-content">
          <image class="header-img" src="/cwu/public/assets/img/clinica.jpg" width="588" heigt="245" />
        </div>
    </section>

    <!-- Descripción -->
    <section class="descSala">
        <h3 class="section-title">LA CLÍNICA</h3>
        <div class="descSala-card">
            <h5>Adultos y Familias</h5>
            <p>"La Clínica" del Dr Náser Felino está siendo investigado por el CDC, uno de sus compañeros le ha denunciado
               por realizar supuestamente experimentos altamente peligrosos. el doctor que ha dado el soplo a las autoridades ha desaparecido, 
               nuestro trabajo como agentes será encontrar al doctor desaparecido y averiguar que está ocurriendo en esta clínica.</p>
            <a href="/cwu/Vistas/auth/RegistroView.php" class="btn-primary-full">Para vivir la aventura REGISTRATE YA</a>  
        </div>
    </section>

    <!-- Footer -->
    <?php include('../../inc/footer.php') ?>
</body>
</html>