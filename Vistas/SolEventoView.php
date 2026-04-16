<?php
$clienteLogueado = false;
$nombreCliente = '';

if (session_status() === PHP_SESSION_ACTIVE || isset($_COOKIE[session_name()])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $clienteLogueado = isset($_SESSION['cliente']) && !empty($_SESSION['cliente']);
    // $nombreCliente = $clienteLogueado ? (string) ($_SESSION['cliente']['nombre'] ?? '') : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include '../inc/vite.php'; vite_assets(); ?>
    <link rel="stylesheet" href="../CSS/style.css">
    <title>Solicitar Evento</title>
    <style>
        body {
            align-items: center;
            justify-content: center;
        }
        .flatpickr-calendar {
            width: 100%;
            box-shadow: none;
            border: 2px solid #aaa;
            border-radius: 0;
        }
        .flatpickr-days,
        .dayContainer {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
        }
        .flatpickr-day {
            max-width: none;
            flex-basis: calc(100% / 7);
        }
    </style>

    

</head>
<body>
  
  <div class="cabecera">
    <img class="lin1" src="../CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/Controladores/logout.php">Salir</a> |
        <a href="./MisEventosView.php">Mis Eventos</a>
    </nav>
  </div>
  
  
    <div class="users-form" id="users-form">
        <form action="/cwu/Controladores/crearCliente.php" style="width: 100%" method="POST">
            <h1>Solicitar presupuesto Evento</h1>
            <h2>Cliente: <?php echo($_SESSION['cliente']['NIF']) ?></h2>      
  
        <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
          <option selected>Elige tipo de evento</option>
          <option value="1">Cumpleaños</option>
          <option value="2">Escape Room</option>
          <option value="3">Fiesta</option>
          <option value="4">Reunión Familiar</option>
          <option value="5">Evento Empresa</option>
        </select>    

            <input type="text" name="nombrepro" id="nombrepro" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð -]" 
            title="Solo puedes introducir letras" placeholder="Nombre y Edad del protagonista, (texto a visionarse en pantalla)">
            <input type="text" name="partic" id="partic" pattern="[0-9]{2}" title="Máximo 16 participantes" placeholder="Total de participantes" required> 
            <h3 style="text-align: center; color: green">ESCOGE DÍA</h3>
            <h5>(días no disponibles aparecen deshabilitados)</h5>
            <input type="hidden" name="fecha_evento" id="fecha_evento">
            <div id="cal"></div>

            <!-- Calendario anterior (conservado para comparar)
            <h5>(días no disponibles en rojo y día actual morado)</h5>
            <div class="wrapper">
              <header>
                <p class="current-date"></p>
                <div class="icons">
                  <i id="prev" class="bi bi-chevron-left"></i>
                  <i id="next" class="bi bi-chevron-right"></i>
                </div>
              </header>
              <div class="calendar">
                <ul class="weeks">
                  <li>Dom</li>
                  <li>Lun</li>
                  <li>Mar</li>
                  <li>Mie</li>
                  <li>Jue</li>
                  <li>Vie</li>
                  <li>Sab</li>
                </ul>
                <ul class="days"></ul>
              </div>
            </div>
            -->

        <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
          <option selected>Selecciona Escape Room</option>
          <option value="1">CLÍNICA</option>
          <option value="2">LIBRERÍA</option>
          <option value="3">CLÍNICA y LIBRERÍA</option>
        </select>                

        <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
          <option value="1">REALIDAD VIRTUAL</option>
          <option value="2">SIN RV</option>
        </select>                


        <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
          <option value="1">TARTA (se incluye "GRATIS" para los Cumples)</option>
          <option value="2">TARTA NO</option>
        </select>                

            <input type="submit" value="Enviar" onclick=" inserta()" value="Enviar"/>
        </form>

    </div>

</body>
</html>