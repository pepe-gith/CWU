<?php
include("connection.php");

//Conecta la BD
$con= connection();

$id = $_GET['id'];

$sql = "DELETE FROM users WHERE id='$id'"; 
$query = mysqli_query($con, $sql);

if($query){
    //redirecciona a index.php
    Header("Location: index.php");
};    
?> 