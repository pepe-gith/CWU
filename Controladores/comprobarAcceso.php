<?php
//conecta con la Base de datos
include("conexion.php");

//Conecta la BD
$con = conexion();

if (isset($_POST["NIF"])){
    $NIF = $_POST['NIF'];
    $password = $_POST['contra'];

    $sql1 = "SELECT '$NIF' FROM cliente WHERE NIF LIKE '$NIF'";
    $query1 = mysqli_query($con, $sql1);
    //printf("numero de filas %d.\n", $query1->num_rows);
    $filas = $query1->num_rows;
    //Si el usuario si que esta registrado
    if ($filas == 1){
        $sql2 = "SELECT * FROM cliente WHERE NIF LIKE '$NIF' AND contra LIKE '$password'";
        $query2 = mysqli_query($con, $sql2);
       
        //si se encuentra el NIF y la contraseña introducidas pasa control a SolEventosView (Menu Cliente)
        $rdo = $query2->num_rows;
        printf("comprobado despues %d.\n", $rdo);
        if($rdo == 1) {
        //tomo en $datos el resultado de la consulta y lo guardo como variable de sesion
          session_start();
          $datos = $query2 ->fetch_assoc();
          $_SESSION['cliente'] = $datos;
          header('location: /cwu/Vistas/SolEventoView.php');
        } else $filas = 2;
    } 
    //cierra la conexión con la Base de Datos
    $con -> close();
} else { 
  //tratamiento de empleado y administrador
  $filas = 3; }
?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link  rel="stylesheet" href="/cwu/CSS/style.css">
    <title>Indica resultado de la comprobación de Acceso</title>
</head>
<body> <!--mensajes de error si el usuario no está registrado o la contraseña no es correcta-->
   <h2 style="color:purple"> <?php if ($filas == 2) printf ("ERROR CONTRASEÑA INCORRECTA"); ?> </h2> 
   <h2 style="color:red"> <?php if ($filas == 0) printf ("ERROR NIF " . $NIF . " no ha sido registrado"); ?> </h2>
   <h2 style="color:green"> <?php if ($filas > 3) printf ("ERROR DE CONEXION - inténtalo de nuevo"); ?> </h2>
    <a href="/cwu/index.php"><button type="button" class="btn btn-primary btn-lg">Regresar a Inicio</button></a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>