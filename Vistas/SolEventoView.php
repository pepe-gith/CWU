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

    <link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../libs/bootstrap-icons/font/bootstrap-icons.min.css">


    <link rel="stylesheet" href="../CSS/calenda.css">
    <link  rel="stylesheet" href="../CSS/style.css">
    <title>Solicitar Evento</title>
    <script src="../js/calendario.js" defer></script>

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
    <!--
    <div class="users-table">        
        <h2>Usuarios registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>NIF</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Movil</th>
                    <th>Movil 2</th>
                    <th>Email</th>
                    <th>Email2</th>
                    <th>Password</th>
                    <th>Dirección</th>
                    <th>Como nos conociste</th>

                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)): ?>
                <tr> 
                    <th><?php echo($row['NIF']) ?></th>
                    <th><?php echo($row['nombrecli']) ?></th>
                    <th><?php echo($row['apellidos']) ?></th>
                    <th><?php echo($row['movil1']) ?></th>
                    <th><?php echo($row['movil2']) ?></th>
                    <th><?php echo($row['corre1']) ?></th>
                    <th><?php echo($row['corre2']) ?></th>
                    <th><?php echo($row['contra']) ?></th>
                    <th><?php echo($row['direccion']) ?></th>
                    <th><?php echo($row['como_conoce']) ?></th>

                    <th><a href="actualizarUsuario.php?id=<?php echo($row['id']) ?>" class="users-table--edit">Editar</a></th>
                    <th><a class="users-table--delete" href="eliminarUsuario.php?id=<?php echo($row['id']) ?>">Eliminar</a></th>
                              </tr>
                <?php endwhile;
                      $con -> close();
                ?>
            </tbody>
        </table>
    </div>    
    
    <script src="../js/jquery.js"></script>
    <script src="../js/anyade_cli.js"></script>
                -->
  <script src="../libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>