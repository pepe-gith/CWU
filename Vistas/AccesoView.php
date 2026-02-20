<?php
include("C:/xampp/htdocs/cwu/conexion.php");

//Conecta y obtiene todos los registros de la BD
$con = conexion();
$sql = "SELECT * FROM cliente";
$query = mysqli_query($con, $sql);

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="/cwu/CSS/style.css">
    <title>Registro</title>
</head>
<body>
   
  <div class="cabecera">
    <img class="lin1" src="/cwu/CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/index.php">Inicio</a>
    </nav>
  </div>  
  <!--Muestra formulario de "Acceso" para comprobar si el usuario está registrado-->
    <div class="users-form" id="users-form">
        <form action="/cwu/controladores/comprobarAcceso.php" method="POST">
            <h1>Acceso</h1>
            <input type="text" name="NIF" id="NIF" title="CAMPO OBLIGATORIO" pattern="[0-9A-Z]{1-9}" placeholder="NIF - ID" required>
            <input type="password" name="contra" id="contra" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="OBLIGATORIO Al menos un número, una letra mayúscula, una minúscula, y como mínimo 8 carácteres" placeholder="Contraseña" required>
          
            <input type="submit" value="Comprobar" onclick=" inserta()" value="Comprobar"/>
        </form>
    </div>
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
<!--
                    <th><a href="update.php?id=<?php echo($row['id']) ?>" class="users-table--edit">Editar</a></th>
                    <th><a class="users-table--delete" href="delete_user.php?id=<?php echo($row['id']) ?>">Eliminar</a></th>
                -->                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>    
    <!--
    <script src="../js/jquery.js"></script>
    <script src="../js/anyade_cli.js"></script>
                -->
</body>
</html>