<?php
//conecta con la Base de datos
include("conexion.php");

//Conecta la BD
$con= conexion();

//función para comprobar si la letra del Nif introducido es la correcta
function compruebaNif($tira) {
  $tira = strtoupper($tira);
  for ($i = 0; $i < 9; $i ++){
    $num[$i] = substr($tira, $i, 1);
  }
  if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($tira, 0, 8) % 23, 1))
    return true; 
  else return false;
}

$NIF = $_POST['NIF'];
if (compruebaNif($NIF)){
    $nombrecli = $_POST['nombrecli'];
    $apellidos = $_POST['apellidos'];
    $movil1 = $_POST['movil1'];
    $movil2 = $_POST['movil2'];
    $corre1 = $_POST['corre1'];
    $corre2 = $_POST['corre2'];
    $contra = $_POST['contra'];
    $direccion = $_POST['direccion'];
    $como = $_POST['como'];

    $sql1 = "SELECT * FROM cliente WHERE NIF LIKE '$NIF'";
    $query1 = mysqli_query($con, $sql1);
    //printf("numero de filas %d.\n", $query1->num_rows);
    $filas = $query1->num_rows;

    if ($filas == 0){
        $sql2 = "INSERT INTO cliente VALUES ('$NIF', '$nombrecli', '$apellidos', '$movil1', '$movil2',
        '$corre1', '$corre2', '$contra', '$direccion', '$como' )"; 
        $query2 = mysqli_query($con, $sql2);
    } 
    //cierra la conexión con la Base de Datos
    $query1 = null;
    $query2 = null;
    $conexion = null;
} else $filas = 2;
?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link  rel="stylesheet" href="/cwu/CSS/style.css">
    <title>Indica resultado de la inserción de nuevo Cliente</title>
</head>
<body>
   <h2 style="color:purple"> <?php if ($filas == 2) printf ("ERROR NIF INCORRECTO - número no corresponde con letra"); ?> </h2> 
   <h2 style="color:red"> <?php if ($filas == 1) printf ("ERROR NIF " . $NIF . " ya fue dado de alta"); ?> </h2>
   <h2 style="color:green"> <?php if ($filas == 0) printf ("El cliente con NIF " . $NIF . " ha sido dado de alta corréctamente"); ?> </h2>
    <a href="/cwu/index.php"><button type="button" class="btn btn-primary btn-lg">Regresar a Inicio</button></a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>