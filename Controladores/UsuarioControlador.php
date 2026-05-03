<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Usuario.php");
require_once("../inc/helpers.php");

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'iniciarSession'   => iniciarSession(),
    'cerrarSesion'     => cerrarSession(),
    'obtenerPerfil'    => obtenerPerfil(),
    'actualizarPerfil' => actualizarPerfil(),
    'cambiarPassword'  => cambiarPassword(),
    default            => responderError(400, 'Acción no válida.')
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
        'id_rol'     => $usuario['id_rol']     ?? null,
    ];

    $redirect = match((int)($usuario['id_rol'] ?? 2)) {
        1       => '/cwu/Vistas/admin/DashboardView.php',
        3       => '/cwu/Vistas/monitor/AgendaView.php',
        default => '/cwu/Vistas/cliente/InicioView.php',
    };

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Acceso correcto',
        'redirect' => $redirect,
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

function obtenerPerfil(): void {
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $con = conexionPDO();
    $modelo = new Usuario($con);
    $usuario = $modelo->obtenerPorId((int) $_SESSION['cliente']['id']);

    if (!$usuario) responderError(404, 'Usuario no encontrado');

    echo json_encode(['ok' => true, 'data' => $usuario]);
    exit;
}

function actualizarPerfil(): void {
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $nombre       = trim((string) filter_input(INPUT_POST, 'nombre',        FILTER_UNSAFE_RAW));
    $apellidos    = trim((string) filter_input(INPUT_POST, 'apellidos',     FILTER_UNSAFE_RAW));
    $telefono     = trim((string) filter_input(INPUT_POST, 'telefono',      FILTER_UNSAFE_RAW));
    $otroTelefono = trim((string) filter_input(INPUT_POST, 'otro_telefono', FILTER_UNSAFE_RAW));
    $email        = trim((string) filter_input(INPUT_POST, 'email',         FILTER_SANITIZE_EMAIL));
    $direccion    = trim((string) filter_input(INPUT_POST, 'direccion',     FILTER_UNSAFE_RAW));

    if (!$nombre || !$apellidos || !$email) responderError(400, 'Faltan campos obligatorios');

    $con = conexionPDO();
    $modelo = new Usuario($con);

    if ($modelo->emailEnUsoPoroOtro($email, (int) $_SESSION['cliente']['id'])) {
        responderError(400, 'Ese email ya está en uso por otra cuenta');
    }
    $modelo->actualizarPerfil((int) $_SESSION['cliente']['id'], $nombre, $apellidos, $telefono, $otroTelefono, $email, $direccion);

    $_SESSION['cliente']['nombre'] = $nombre;
    $_SESSION['cliente']['email']  = $email;

    echo json_encode(['ok' => true, 'mensaje' => 'Perfil actualizado correctamente']);
    exit;
}

function cambiarPassword(): void {
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $actual    = (string) filter_input(INPUT_POST, 'password_actual',    FILTER_UNSAFE_RAW);
    $nueva     = (string) filter_input(INPUT_POST, 'password_nueva',     FILTER_UNSAFE_RAW);
    $confirmar = (string) filter_input(INPUT_POST, 'password_confirmar', FILTER_UNSAFE_RAW);

    if (!$actual || !$nueva || !$confirmar) responderError(400, 'Faltan campos');
    if ($nueva !== $confirmar)              responderError(400, 'Las contraseñas no coinciden');
    if (strlen($nueva) < 8)                responderError(400, 'La contraseña debe tener al menos 8 caracteres');

    $con = conexionPDO();
    $modelo = new Usuario($con);
    $resultado = $modelo->cambiarPassword((int) $_SESSION['cliente']['id'], $actual, $nueva);

    if ($resultado !== true) responderError(400, $resultado);

    echo json_encode(['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente']);
    exit;
}