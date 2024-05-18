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
    <link  rel="stylesheet" href="/cwu/CSS/style.css">
    <title>Registro</title>
</head>
<body>
    
  <div class="cabecera">
    <img class="lin1" src="/cwu/CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/controladores/logout.php">Salir</a>
    </nav>
  </div>  
    <div class="users-form" id="users-form">
    <?php  printf("cliente es " . var_dump($_SESSION['cliente']) . " a ver si va\n");
           printf($mensa);
     ?>
        <form action="/cwu/controladores/insertarCliente.php" method="POST">
            <h1>Solicitar presupuesto Evento</h1>
            <input type="text" name="NIF" id="NIF" placeholder="NIF" title="CAMPO OBLIGATORIO - 8 números y la letra que corresponda en mayúscula" pattern="[0-9]{8}[A-Z]{1}" required>
            <input type="text" name="nombrecli" id="nombrecli" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð -]" title="Solo puedes introducir letras" placeholder="Nombre">
            <input type="text" name="apellidos" id="apellidos" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]" title="Solo letras" placeholder="Apellidos">
            <input type="text" name="movil1" id="movil1" pattern="[0-9]{9}" title="CAMPO OBLIGATORIO - Solo 9 números" placeholder="Teléfono móvil" required>
            <input type="text" name="movil2" id="movil2" pattern="[0-9]{9}" title="Solo 9 números" placeholder="Otro teléfono">
            <input type="email" name="corre1" id="corre1" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title="CAMPO OBLIGATORIO" placeholder="e-mail" required>
            <input type="email" name="corre2" id="corre2" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" placeholder="Otro e-mail">
            <input type="password" name="contra" id="contra" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="OBLIGATORIO Al menos un número, una letra mayúscula, una minúscula, y como mínimo 8 carácteres" placeholder="Contraseña" required>
            <input type="text" name="direccion" id="direccion" pattern="[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]" title="Letras, números" placeholder="Dirección">
            <input type="text" name="como" id="como" pattern="[a-zA-ZñÑ.,0-9\s]{4-8}" title="Letras, números. De 4 a 8 carácteres" placeholder="Como nos has conocido">

            <input type="submit" value="Enviar" onclick=" inserta()" value="Enviar"/>
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
                <?php endwhile;
                      $conexion -> close();
                ?>
            </tbody>
        </table>
    </div>    
    <!--
    <script src="../js/jquery.js"></script>
    <script src="../js/anyade_cli.js"></script>
                -->
</body>
</html>