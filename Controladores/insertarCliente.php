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

    $query = "INSERT INTO cliente (NIF, nombrecli, apellidos, movil1, movil2, corre1, corre2, contra, direccion, como_conoce) VALUES
     ('$NIF', '$nombrecli', '$apellidos', '$movil1', '$movil2', '$corre1', '$corre2', '$contra', '$direccion', '$como')"; 
    $result = mysqli_query($conexion, $query);
    
    if(!$result){
        die("Error en el alta del cliente").mysqli_error($conexion);
    } else  echo json_encode("Cliente dado de alta correctamente"); 
} else echo json_encode("ERROR");

?> 