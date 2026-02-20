<!-- Esta es la página principal de la Web -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link  rel="stylesheet" href="/cwu/CSS/index.css" >
    <title>CWU_PaginaPrincipal</title>
</head>
<body>
<!-- incluyo el fichero video de confeti en página principal -->
    <video src="/cwu/css/Img/confeti.mp4" autoplay loop muted></video>
<!-- incluyo el fichero cabecera en la página principal -->
    <?php include('./header.php') ?>
<div class="contenedor">
  <!-- incluyo opciones de página principal -->      
  <div class="caja">  
      <a href="/cwu/Vistas/Sala1View.php">  <img class="opc" src="/cwu/CSS/Img/SalaClinica.png" width="50%"> </a>
      </div>
      <div class="caja">  
      <a href="/cwu/Vistas/CumpleView.php">  <img class="opc" src="/cwu/CSS/Img/feliz.png"> </a>
      </div>                
      <div class="caja">  
      <a href="/cwu/Vistas/Sala2View.php">  <img class="opc" src="/cwu/CSS/Img/SalaLibreria.png"> </a>
      </div>      
  </div>    
</div>
</body>
<!-- incluyo el fichero footer en la página principal -->
<?php include('./footer.php') ?>
</html>
