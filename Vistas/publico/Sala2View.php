<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php include '../../inc/vite.php'; vite_assets(); ?>

<title>La Biblioteca</title>
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
          <image class="header-img" src="/cwu/public/assets/img/libreria.jpg" width="588" heigt="245" />
        </div>
    </section>

    <!-- Descripción -->
    <section class="descSala">
        <h3 class="section-title">LA BIBLIOTECA</h3>
        <div class="descSala-card">
            <h5>Menores a partir de 8 años</h5>
            <p>Victoria es una librera que lleva años custodiando un escarabajo maldito encerrado en un libro antiguo.
               El bicho se está escapando y si lo consigue las letras de todos los libros desaparecerán. Tienes una hora
               para separar el escarabajo del libro antes de que sea demasiado tarde. ¿Podrás hacerlo?</p>
            <a href="/cwu/Vistas/auth/RegistroView.php" class="btn-primary-full">Para vivir la aventura REGISTRATE YA</a>  
        </div>
    </section>

    <!-- Footer -->
    <?php include('../../inc/footer.php') ?>
</body>

</html>