<?php
require_once("../Modelos/Conexion.php");
require_once("../Modelos/Usuario.php");

header('Content-Type: application/json');

$nif = trim((string) ($_POST['nif'] ?? ''));
$nombre = trim((string) ($_POST['nombre'] ?? ''));
$apellidos = trim((string) ($_POST['apellidos'] ?? ''));
$movil1 = trim((string) ($_POST['movil1'] ?? ''));
$movil2 = trim((string) ($_POST['movil2'] ?? ''));
$email1 = trim((string) ($_POST['email1'] ?? ''));
$password = trim((string) ($_POST['password'] ?? ''));
$direccion = trim((string) ($_POST['direccion'] ?? ''));
$comoNosConocio = trim((string) ($_POST['como'] ?? ''));


if ( $nif === '' || $nombre === '' || $apellidos === '' || $movil1 === '' || $email1 === '' || $password === '' || $direccion === '' || $comoNosConocio === '') {
    http_response_code(400);
    exit('Faltan datos obligatorios.');
}


// Validamos el formato del email
if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('El email no es valido.');
}

$con = conexionPDO();
$modeloUsuario = new Usuario($con);

if ($modeloUsuario->existeEmail($email1)) {
    http_response_code(409);
    exit('Ya existe un usuario con ese email.');
}

// Hasheamos la contraseña antes de guardarla
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$modeloUsuario->crearUsuario($nif, $nombre, $apellidos, $movil1, $movil2, $email1, $passwordHash, $direccion, $comoNosConocio);
http_response_code(201);
echo json_encode([
    'ok' => true,
    'mensaje' => 'Usuario creado correctamente'
]);
exit;
?>