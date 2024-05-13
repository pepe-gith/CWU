<?php
include("conectaBD.php");

if(isset($_POST["NIF"])){ 
    $NIF = $_POST["NIF"];
    $nombrecli = $_POST["nombrecli"];
    $apellidos = $_POST["apellidos"];
    $movil1 = $_POST["movil1"];
    $movil2 = $_POST["movil2"];
    $corre1 = $_POST["corre1"];
    $corre2 = $_POST["corre2"];
    $contra = $_POST["contra"];
    $direccion = $_POST["direccion"];
    $como = $_POST["como"];

    $query = "INSERT INTO cliente (NIF, nombrecli, apellidos, movil1, movil2, corre1, corre2, contra, direccion, como) VALUES
     ('$NIF', '$nombrecli', '$apellidos', '$movil1', '$movil2', '$corre1', '$corre2', '$contra', '$direccion', '$como')"; 
    $result = mysqli_query($conexion, $query);
    echo json_encode("He entrado en el if x el SIIIIII");
    if(!$result){
        die("Error en el alta del cliente").mysqli_error($conexion);
    }
    echo json_encode("Cliente dado de alta correctamente"); 
} else echo json_encode("ERROR");

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <h1> He pasado por el insertarCliente pero no se inserta </h1>
  
    <a href="/cwu/index.php"><button>Regresar a Inicio</button></a>
</body>
</html>