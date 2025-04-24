<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="/cwu/CSS/index.css" >
    <title>Document</title>
</head>
<body>
    
<!-- incluyo el fichero cabecera en la página principal -->
    <?php include('../header.php') ?>
    <!-- MOTIVOS -->
    <section class="cards">
        <div class="card">
            <img src="/cwu/css/img/thr.png">
            <h5>Diversión Asegurada</h5>
            <p>Es importante salir de la rutina diaria y divertirse con otras personas. Disfrutarás del ambiente de cada sala
               que te transportará a una época o lugar diferente y no querrás que acabe.</p>
        </div>
        <div class="card">
            <img src="/cwu/css/img/thr1.png">
            <h5>Trabaja en Equipo</h5>
            <p>Fomenta el trabajo en equipo. Tod@s tenéis que interactuar, para poder escapar de la sala debéis organizaros,
               comunicaros y ayudaros a resolver la clave y superar las misiones con éxito.</p>
        </div>
        <div class="card">
            <img src="/cwu/css/img/thr2.png">
            <h5>Ejercita tu mente</h5>
            <p>Hay que darle al coco. Habrán acertijos que te son familiares o que, gracias a tus habilidades, se te dan bien. 
              Hallarás pruebas de todo tipo, confia en tu ingenio, creatividad y lógica.</p>
        </div>
    </section>
    <!-- END MOTIVOS -->        
    <!-- BANNER -->    
    <section class="banner">
        <div class="banner-content">
          <image class="header-img" src="/cwu/css/img/libreria.jpg" width="588" heigt="245" />
        </div>
    </section>
    <!-- END BANNER -->    
    <!-- DESCRIPCIÓN SALA -->
    <section class="descSala">
        <h3 class="section-title">LA BIBLIOTECA</h3>
        <div class="descSala-card">
            <h5>Menores a partir de 8 años</h5>
            <p>"Victoria Valiente, la librera aventurera, descubre un antiguo escarabajo maldito que consume las letras de libros.
                 Tras décadas de lucha, sella el libro y atrapa al escarabajo, pero la maldición persiste. Con el tiempo, el insecto
                 intenta escapar, consumiendo las últimas letras. Victoria, ahora anciana, busca equipos de aventureros para separar
                 el libro mágico del escarabajo y salvar su librería y el mundo de la maldición. El tiempo apremia, con solo una hora
                 antes de que el libro pierda su última letra y la maldición se desate</p>
            <a href="/cwu/Vistas/RegistroView.php" class="btn-primary-full">Para vivir la aventura REGISTRATE YA</a>  
        </div>
    </section>
    <!-- END DESCRIPCIÓN SALA -->
</body>
  <!-- incluyo el fichero footer en la página principal -->
  <?php include('../footer.php') ?>
</html>