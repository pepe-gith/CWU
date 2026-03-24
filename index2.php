<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="CSS/style.css" >
    <script src="./js/mostrarUsuarios.js"></script>
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
        <form action="Controladores/crearUsuario.php" method="POST">
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
        <table id="tablaUsuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Usuario</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaUsuarios">
                <!-- Se rellena via JS -->
            </tbody>
        </table>
    </div>    
</body>
</html>