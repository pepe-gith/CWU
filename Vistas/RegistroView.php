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
    
  <div class="cajon">
    <img class="lin1" src="/cwu/CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/index0.php">Inicio</a>
    </nav>
    <body>
    <div class="users-form">
        <form action="/cwu/controladores/insertarCliente.php" method="POST">
            <h1>Registrar Cliente</h1>
            <input type="text" name="nif" placeholder="NIF" required>
            <input type="text" name="nombrecli" placeholder="Nombre">
            <input type="text" name="apellidos" placeholder="Apellidos">
            <input type="text" name="movil1" placeholder="Teléfono móvil" required>
            <input type="text" name="movil2" placeholder="Otro teléfono">
            <input type="email" name="corre1" placeholder="e-mail" required>
            <input type="email" name="corre2" placeholder="Otro e-mail">
            <input type="password" name="contra" placeholder="Contraseña">
            <input type="text" name="direccion" placeholder="Dirección">
            <input type="text" name="como" placeholder="Como nos has conocido">

            <input type="submit" value="Enviar">
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
</body>  </div>  
</body>
</html>