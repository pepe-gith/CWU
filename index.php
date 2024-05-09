<?php
include("connection.php");

//Conecta y obtiene todos los registros de la BD
$con= connection();
$sql = "SELECT * FROM users";
$query = mysqli_query($con, $sql);

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="CSS/style.css" >
    <title>Usuarios CRUD</title>
</head>
<nav>
  <a href="/Vistas/Inicio.php">HTML</a> |
  <a href="/css/">CSS</a> |
  <a href="/js/">JavaScript</a> |
  <a href="/python/">Python</a>
</nav>
<body>
    <div class="users-form">
        <form action="insert_user.php" method="POST">
            <h1>Crear Usuario</h1>

            <input type="text" name="name" placeholder="Nombre">
            <input type="text" name="lastname" placeholder="Apellido">
            <input type="text" name="username" placeholder="Usuario">
            <input type="text" name="password" placeholder="Contraseña">
            <input type="text" name="email" placeholder="Email">

            <input type="submit" value="Agregar  Usuario">
        </form>
    </div>
    <div class="users-table">        
        <h2>Usuarios registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Email</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)): ?>
                <tr> 
                    <th><?php echo($row['id']) ?></th>
                    <th><?php echo($row['name']) ?></th>
                    <th><?php echo($row['lastname']) ?></th>
                    <th><?php echo($row['username']) ?></th>
                    <th><?php echo($row['password']) ?></th>
                    <th><?php echo($row['email']) ?></th>

                    <th><a href="update.php?id=<?php echo($row['id']) ?>" class="users-table--edit">Editar</a></th>
                    <th><a class="users-table--delete" href="delete_user.php?id=<?php echo($row['id']) ?>">Eliminar</a></th>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>    
</body>
</html>