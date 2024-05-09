<?php 
include("connection.php");

//Conecta la BD
$con= connection();

$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id='$id'"; 
$query = mysqli_query($con, $sql);
$row = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="CSS/style.css" >
    <title>Editar Usuario</title>
</head>
<body>
    <div class="users-form">
    <form action="edit_user.php" method="POST">
        <h1>Editar usuario </h1>
        <input type="hidden" name="id" value="<?php echo($row['id']) ?>">
        <input type="text" name="name" placeholder="Nombre" value="<?php echo($row['name']) ?>">
        <input type="text" name="lastname" placeholder="Apellido" value="<?php echo($row['lastname']) ?>">
        <input type="text" name="username" placeholder="Usuario" value="<?php echo($row['username']) ?>">
        <input type="text" name="password" placeholder="Contraseña" value="<?php echo($row['password']) ?>">
        <input type="text" name="email" placeholder="Email" value="<?php echo($row['email']) ?>">

        <input type="submit" value="Actualizar información">
    </form>
    </div>