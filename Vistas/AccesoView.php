<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="../CSS/style.css">
    <script src="../js/comprobarAcceso.js" defer></script>
    <script src="../js/mostrarUsuarios.js" defer></script>
    <title>Acceso</title>
</head>
<body>
   
  <div class="cabecera">
    <img class="lin1" src="../CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/index.php">Inicio</a>
    </nav>
  </div>  

  <!--Muestra formulario de "Acceso" para comprobar si el usuario está registrado-->
    <div class="users-form" id="users-form">
        <form id="formComprobarAcceso" method="POST">
            <h1>Acceso</h1>
            <input type="text" name="nif" id="nif" title="CAMPO OBLIGATORIO" placeholder="NIF" required>
            <input type="password" name="contra" id="contra" title="OBLIGATORIO Al menos un número, una letra mayúscula, una minúscula, y como mínimo 8 carácteres" placeholder="Contraseña" required>
            <input type="submit" value="Comprobar"/>
        </form>
    </div>

    <div class="users-table">
        <h2>Usuarios registrados</h2>
        <table id="tablaUsuarios">
            <thead>
                <tr>
                    <th>NIF</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Otro teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Cómo nos conoció</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaUsuarios">
                <!-- Se rellena via JS -->
            </tbody>
        </table>
    </div>

</body>
</html>