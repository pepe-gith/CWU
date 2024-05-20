<?php
include("C:/xampp/htdocs/cwu/conexion.php");

//Conecta y obtiene todos los registros de la BD
$con = conexion();
$sql = "SELECT * FROM cliente";
$query = mysqli_query($con, $sql);

session_start();   //Inicia sesión

//Si no ha iniciado sesion de 'cliente' salir a INICIO
if(!isset($_SESSION['cliente'])or empty($_SESSION['cliente']))
 header('location:/cwu/index.php');
 else  $mensa ='NO entra en el IF';
    

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="/cwu/CSS/calenda.css">
    <link  rel="stylesheet" href="/cwu/CSS/style.css">
    <title>Registro</title>
    <script src="/cwu/js/calendario.js" defer></script>

</head>
<body>
    
  <div class="cabecera">
    <img class="lin1" src="/cwu/CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/controladores/logout.php">Salir</a>
    </nav>
  </div>
  
  
    <div class="users-form" id="users-form">
        <form action="/cwu/controladores/insertarCliente.php" style="width: 100%" method="POST">
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
            <input type="text" name="partic" id="partic" pattern="[0-9]{2}" title="Máximo 16 participantes" placeholder="Nº Participantes con atención especial (Celiacos, Alergias, etc)">           
            <h3 style="text-align: center; color: green">ESCOGE DÍA</h3>  
            <h5>(disponibles verde, no en rojo y día actual morado)</h5>  
            <div class="wrapper">
              <header>
                <p class="current-date"></p>
                <div class="icons">
                  <span id="prev" class="material-symbols-rounded">chevron_left</span>
                  <span id="next" class="material-symbols-rounded">chevron_right</span>
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

                    <th><a href="update.php?id=<?php echo($row['id']) ?>" class="users-table--edit">Editar</a></th>
                    <th><a class="users-table--delete" href="delete_user.php?id=<?php echo($row['id']) ?>">Eliminar</a></th>
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
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>