<?php
include("connection.php");

//Conecta la BD
$con= connection();

$id = null;
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

$sql = "INSERT INTO users VALUES ('$id', '$name', '$lastname', '$username', '$password','$email')"; 
$query = mysqli_query($con, $sql);

if($query){
    //redirecciona a index.php
    Header("Location: index.php");
};    
?> 