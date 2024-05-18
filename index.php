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
<video src="/cwu/css/Img/confeti.mp4" autoplay loop muted></video>
<!--cabecera-->
  <div class="cajon">    
  
    <img class="lin1" src="/cwu/CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/index.php">Inicio</a> |
        <a href="/cwu/Vistas/RegistroView.php">Registro</a> |
        <a href="/cwu/Vistas/AccesoView.php">Acceso</a> 
    </nav>
    <div class="contenedor">
      <div class="caja">  
        <a href="/cwu/Vistas/SalasView.php">  <img class="opc" src="/cwu/CSS/Img/escape.png" height="200" > </a>
      </div>
      <div class="caja">  
        <a href="/cwu/Vistas/CumpleView.php">  <img class="opc" src="/cwu/CSS/Img/feliz.png" height="200" > </a>
      </div>                
      <div class="caja">  
        <a href="/cwu/Vistas/FiestaView.php">  <img class="opc" src="/cwu/CSS/Img/fiesta.png" height="200" > </a>
      </div>      
    </div>    
  </div>  
  <!--footer-->
 <footer class="w-100 bg-info d-flex align-items justify-content-center position-absolute bottom-0 end-0">
    <p class="px-lg-2 d-inline-block bg-info">Celebrate with US &copy; Todos los derechos reservados</p>
    <div id="iconos">
      <a href="https://www.facebook.com/"><i class="bi bi-facebook"></i></a>
      <a href=" https://chat.whatsapp.com/"><i class="bi bi-whatsapp"></i></a>
      <a href="https://t.me/"><i class="bi bi-telegram"></i></a>
    </div>

 </footer>
</body>
</html>
