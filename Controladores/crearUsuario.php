<?php
require_once("../Modelos/Conexion.php");
require_once("../Modelos/Usuario.php");

header('Content-Type: application/json');

$nif       = trim((string) ($_POST['nif']      ?? ''));
$nombre    = trim((string) ($_POST['nombre']   ?? ''));
$apellidos = trim((string) ($_POST['apellidos'] ?? ''));
$movil1    = trim((string) ($_POST['movil1']   ?? ''));
$movil2    = trim((string) ($_POST['movil2']   ?? ''));
$email1    = trim((string) ($_POST['email1']   ?? ''));
$password  = trim((string) ($_POST['password'] ?? ''));
$direccion = trim((string) ($_POST['direccion'] ?? ''));
$como      = trim((string) ($_POST['como']     ?? ''));

// Validar campos obligatorios
if (!$nif || !$nombre || !$apellidos || !$movil1 || !$email1 || !$password || !$direccion) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Faltan datos obligatorios.']);
    exit;
}

// Validar formato NIF
if (!preg_match('/^[0-9]{8}[A-Z]$/', $nif)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El formato del NIF no es válido.']);
    exit;
}

// Validar email
if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El email no es válido.']);
    exit;
}

$con = conexionPDO();
$modeloUsuario = new Usuario($con);

if ($modeloUsuario->existeNif($nif)) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'error' => 'Ya existe una cuenta con ese NIF.']);
    exit;
}

if ($modeloUsuario->existeEmail($email1)) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'error' => 'Ya existe una cuenta con ese email.']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$modeloUsuario->crearUsuario($nif, $nombre, $apellidos, $movil1, $movil2, $email1, $passwordHash, $direccion, $como);

http_response_code(201);
echo json_encode(['ok' => true, 'mensaje' => 'Usuario creado correctamente']);
exit;
