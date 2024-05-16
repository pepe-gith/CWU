<?php
//conecta con la Base de datos
include("conectaBD.php");

//si recibe la información "NIF" y no es null
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

    $query1 = "SELECT * FROM cliente WHERE NIF LIKE '$NIF'";
    $result1 = mysqli_query($conexion, $query1);
    printf("numero de filas-> %d.\n", $result1->num_rows);
    $filas = $result1->num_rows;
    
    if($filas==0){

    $query2 = "INSERT INTO cliente (NIF, nombrecli, apellidos, movil1, movil2, corre1, corre2, contra, direccion, como_conoce) VALUES
     ('$NIF', '$nombrecli', '$apellidos', '$movil1', '$movil2', '$corre1', '$corre2', '$contra', '$direccion', '$como')"; 
    $result2 = mysqli_query($conexion, $query2);
    
    //Si la inserción no es erronea
    if(!$result2){
        die("Error en el alta del cliente").mysqli_error($conexion);
    } else {
        echo json_encode("Cliente dado de alta correctamente");
    } 
    } else die("ERROR - NIF ya dado de alta").mysqli_error($conexion);
} else {
     die("ERROR alguno/s de los campos obligatorios no son correctos").mysqli_error($conexion);
  } 
  //cierra la conexión con la Base de Datos
  $result1 = null;
  $result2 = null;
  $conexion = null;
?> 
