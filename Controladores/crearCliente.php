<?php
//conecta con la Base de datos
include_once("../Modelos/conexion.php");

//Conecta la BD
$con= conexion();

// Optemos los datos 

$NIF = htmlspecialchars($_POST['nif']);
$contra = htmlspecialchars($_POST['contra']);




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


//función para comprobar si la letra del Nif introducido es la correcta
function compruebaNif($tira) {
  $tira = strtoupper($tira);
  for ($i = 0; $i < 9; $i ++){
    $num[$i] = substr($tira, $i, 1);
  }
  if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', (int)substr($tira, 0, 8) % 23, 1))
    return true; 
  else return false;
}

?>