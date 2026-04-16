<?php
//conecta con la Base de datos
require_once("../Modelos/conexion.php");
require_once("../Modelos/Usuario.php");

header('Content-Type: application/json');

$con = conexionPDO();


// Obtenemos los datos
$nif = trim((string) filter_input(INPUT_POST, 'nif', FILTER_UNSAFE_RAW));
$contra = (string) filter_input(INPUT_POST, 'contra', FILTER_UNSAFE_RAW);

if( !$nif || !$contra ) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Falta datos'
    ]);
    exit;
}

$modeloUsuario = new Usuario($con);

$usuario = $modeloUsuario->comprobarAcceso($nif, $contra);

if(!$usuario) {
    http_response_code(401);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Credenciales incorrectas'
    ]);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_regenerate_id(true);

$_SESSION['cliente'] = [
    'id' => $usuario['id'] ?? null,
    'NIF' => $usuario['nif'] ?? null,
    'nombre' => $usuario['nombre'] ?? null,
    'email' => $usuario['email'] ?? null,
];

echo json_encode([
    'ok' => true,
    'mensaje' => 'Acceso correcto',
    'redirect' => '/cwu/index.php',
    'usuario' => [
        'id' => $usuario['id'] ?? null,
        'nombre' => $usuario['nombre'] ?? null,
        'email' => $usuario['email'] ?? null,
    ]
]);
exit;

