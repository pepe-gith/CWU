<?php
include("conecta.php");

//Conecta la BD
$con= conexion();

$nif = $_POST['nif'];
$nombrecli = $_POST['nombrecli'];
$apellidos = $_POST['apellidos'];
$movil1 = $_POST['movil1'];
$movil2 = $_POST['movil2'];
$corre1 = $_POST['corre1'];
$corre2 = $_POST['corre2'];
$contra = $_POST['contra'];
$direccion = $_POST['direccion'];
$como = $_POST['como'];

$sql1 = "SELECT nif FROM cliente WHERE nif LIKE '$nif'";
$query1 = mysqli_query($con, $sql1);
//printf("numero de filas %d.\n", $query1->num_rows);
$filas = $query1->num_rows;

if ($filas == 0){
    $sql2 = "INSERT INTO cliente VALUES ('$nif', '$nombrecli', '$apellidos', '$movil1', '$movil2',
    '$corre1', '$corre2', '$contra', '$direccion', '$como' )"; 
    $query2 = mysqli_query($con, $sql2);

    /*if($query2){
        
        printf("El cliente ha sido dado de alta corréctamente");
        //redirecciona a index.php
        Header("Location: /cwu/index0.php");
        
    };*/
} 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <h1> <?php if ($filas > 0) printf ("ERROR el nif " . $nif . " ya fue dado de alta"); ?> </h1>
   <h1> <?php if ($filas == 0) printf ("El cliente con nif " . $nif . " ha sido dado de alta corréctamente"); ?> </h1>
    <a href="/cwu/index0.php"><button>Regresar a Inicio</button></a>
</body>
</html>