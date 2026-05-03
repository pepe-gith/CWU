<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Usuario.php");
require_once("../inc/helpers.php");

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'iniciarSession' => iniciarSession(),
    'cerrarSesion'    => cerrarSession(),
    default           => responderError(400, 'Acción no válida.')
};

function iniciarSession(): void {

    // Obtenemos los datos
    $nif = trim((string) filter_input(INPUT_POST, 'nif', FILTER_UNSAFE_RAW));
    $contra = (string) filter_input(INPUT_POST, 'contra', FILTER_UNSAFE_RAW);

    if( !$nif || !$contra ) {
        responderError(400, 'Falta datos');
    }

    $con = conexionPDO();
    $modeloUsuario = new Usuario($con);
    $usuario = $modeloUsuario->comprobarAcceso($nif, $contra);

    if(!$usuario) {
        responderError(401, 'Credenciales incorrectas');
    }

    session_regenerate_id(true);

    $_SESSION['cliente'] = [
        'id'         => $usuario['id']         ?? null,
        'NIF'        => $usuario['nif']        ?? null,
        'nombre'     => $usuario['nombre']     ?? null,
        'email'      => $usuario['email']      ?? null,
        'id_empresa' => $usuario['id_empresa'] ?? null,
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
}


function cerrarSession(): void {
    $_SESSION = [];
    session_destroy();

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Sesión cerrada',
        'redirect' => '/cwu/index.php'
    ]);
    exit;
}