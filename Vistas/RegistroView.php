<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5.3 -->
    <link href="/cwu/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="/cwu/libs/bootstrap-icons/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link  rel="stylesheet" href="/cwu/CSS/index.css" >
    <link  rel="stylesheet" href="../CSS/style.css">
    
    <script src="/cwu/js/anyade_cli.js" defer></script>
    <script src="/cwu/js/mostrarUsuarios.js" defer></script>
    <title>Registro</title>
</head>
<body>

  <div class="cabecera">
    <img class="lin1" src="../CSS/Img/titulo.png"/>
    <nav>
        <a href="/cwu/index.php">Inicio</a>
    </nav>
  </div>  
    <div class="users-form" id="users-form">
        <form id="formRegistro" method="POST" autocomplete="on">
            <h1>Registrar Cliente</h1>
            <input type="text" name="nif" id="nif" placeholder="NIF" title="CAMPO OBLIGATORIO - 8 números y la letra que corresponda en mayúscula" pattern="[0-9]{8}[A-Z]{1}" maxlength="9" required>
            <input type="text" name="nombre" id="nombre" title="Solo puedes introducir letras" placeholder="Nombre" maxlength="100" required>
            <input type="text" name="apellidos" id="apellidos" title="Solo letras" placeholder="Apellidos" maxlength="150" required>
            <input type="tel" name="movil1" id="movil1" pattern="[0-9]{9}" maxlength="9" title="CAMPO OBLIGATORIO - Solo 9 números" placeholder="Teléfono móvil" required>
            <input type="tel" name="movil2" id="movil2" pattern="[0-9]{9}" maxlength="9" title="Solo 9 números" placeholder="Otro teléfono">
            <input type="email" name="email1" id="email1" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title="CAMPO OBLIGATORIO" placeholder="E-mail" maxlength="100" required>
            <input type="password" name="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" minlength="8" title="OBLIGATORIO Al menos un número, una letra mayúscula, una minúscula, y como mínimo 8 carácteres" placeholder="Contraseña" required>
            <input type="text" name="direccion" id="direccion" title="Letras, números" placeholder="Dirección" maxlength="255" required>
            <input type="text" name="como" id="como" title="Letras y números" placeholder="Cómo nos has conocido" maxlength="100">

            <input type="submit" value="Enviar"/>
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

    <script src="/cwu/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>