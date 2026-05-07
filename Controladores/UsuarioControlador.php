<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Usuario.php");
require_once("../inc/helpers.php");
require_once("../inc/sesion.php");

iniciarSesion();

header('Content-Type: application/json');

$accion = $_POST['action'] ?? $_GET['action'] ?? '';

match($accion) {
    'iniciarSession'          => iniciarSession(),
    'cerrarSesion'            => cerrarSession(),
    'registrar'               => registrar(),
    'obtenerPerfil'           => obtenerPerfil(),
    'actualizarPerfil'        => actualizarPerfil(),
    'cambiarPassword'         => cambiarPassword(),
    'obtenerNotificaciones'   => obtenerNotificaciones(),
    'marcarNotificacionLeida' => marcarNotificacionLeida(),
    'verificarCampo'          => verificarCampo(),
    default                   => responderError(400, 'Acción no válida.')
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
        'id' => $usuario['id'],
        'NIF' => $usuario['nif'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'id_empresa' => $usuario['id_empresa'],
        'id_rol' => $usuario['id_rol']
    ];

    $redirect = match((int)($usuario['id_rol'] ?? 3)) {
        1 => '/cwu/Vistas/admin/DashboardView.php',
        2 => '/cwu/Vistas/empleado/AgendaView.php',
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
    // Solo usuarios autenticados
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $con = conexionPDO();
    $modelo = new Usuario($con);
    $usuario = $modelo->obtenerPorId((int) $_SESSION['cliente']['id']);

    if (!$usuario) responderError(404, 'Usuario no encontrado');

    echo json_encode(['ok' => true, 'data' => $usuario]);
    exit;
}

function actualizarPerfil(): void {
    // Solo usuarios autenticados
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $nombre = trim((string) filter_input(INPUT_POST, 'nombre',        FILTER_UNSAFE_RAW));
    $apellidos = trim((string) filter_input(INPUT_POST, 'apellidos',     FILTER_UNSAFE_RAW));
    $telefono = trim((string) filter_input(INPUT_POST, 'telefono',      FILTER_UNSAFE_RAW));
    $otroTelefono = trim((string) filter_input(INPUT_POST, 'otro_telefono', FILTER_UNSAFE_RAW));
    $email = trim((string) filter_input(INPUT_POST, 'email',         FILTER_SANITIZE_EMAIL));
    $direccion = trim((string) filter_input(INPUT_POST, 'direccion',     FILTER_UNSAFE_RAW));

    if (!$nombre || !$apellidos || !$email) responderError(400, 'Faltan campos obligatorios');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) responderError(400, 'El email no es válido.');

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

function registrar(): void {
    $nif = trim((string) ($_POST['nif']       ?? ''));
    $nombre = trim((string) ($_POST['nombre']    ?? ''));
    $apellidos = trim((string) ($_POST['apellidos'] ?? ''));
    $movil1 = trim((string) ($_POST['movil1']    ?? ''));
    $movil2 = trim((string) ($_POST['movil2']    ?? ''));
    $email = trim((string) ($_POST['email1']    ?? ''));
    $password = trim((string) ($_POST['password']  ?? ''));
    $direccion = trim((string) ($_POST['direccion'] ?? ''));
    $como = trim((string) ($_POST['como']      ?? ''));

    if (!$nif || !$nombre || !$apellidos || !$movil1 || !$email || !$password || !$direccion)
        responderError(400, 'Faltan datos obligatorios.');

    if (!preg_match('/^[0-9]{8}[A-Z]$/', $nif))
        responderError(400, 'El formato del NIF no es válido.');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        responderError(400, 'El email no es válido.');

    $con    = conexionPDO();
    $modelo = new Usuario($con);

    if ($modelo->existeNif($nif))   responderError(409, 'Ya existe una cuenta con ese NIF.');
    if ($modelo->existeEmail($email)) responderError(409, 'Ya existe una cuenta con ese email.');

    $modelo->crearUsuario($nif, $nombre, $apellidos, $movil1, $movil2, $email, password_hash($password, PASSWORD_DEFAULT), $direccion, $como);

    http_response_code(201);
    echo json_encode(['ok' => true, 'mensaje' => 'Usuario creado correctamente']);
    exit;
}

function verificarCampo(): void {
    $campo = trim((string) ($_POST['campo'] ?? ''));
    $valor = trim((string) ($_POST['valor'] ?? ''));

    if (!$campo || !$valor) responderError(400, 'Faltan datos.');

    $con    = conexionPDO();
    $modelo = new Usuario($con);

    $existe = match($campo) {
        'nif'   => $modelo->existeNif($valor),
        'email' => $modelo->existeEmail($valor),
        default => null,
    };

    if ($existe === null) responderError(400, 'Campo no válido.');

    echo json_encode(['existe' => $existe]);
    exit;
}

function obtenerNotificaciones(): void {

    // Solo usuarios autenticados
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $con  = conexionPDO();
    $stmt = $con->prepare("SELECT id, mensaje, fecha FROM Notificacion WHERE id_usuario = :id AND leida = 0 ORDER BY fecha DESC");
    $stmt->execute([':id' => $_SESSION['cliente']['id']]);
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function marcarNotificacionLeida(): void {
    // Solo usuarios autenticados
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!$id) responderError(400, 'Datos no válidos');
    $con  = conexionPDO();
    $stmt = $con->prepare("UPDATE Notificacion SET leida = 1 WHERE id = :id AND id_usuario = :uid");
    $stmt->execute([':id' => $id, ':uid' => $_SESSION['cliente']['id']]);
    echo json_encode(['ok' => true]);
    exit;
}

function cambiarPassword(): void {
    // Solo usuarios autenticados
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado');

    $actual = (string) filter_input(INPUT_POST, 'password_actual',    FILTER_UNSAFE_RAW);
    $nueva = (string) filter_input(INPUT_POST, 'password_nueva',     FILTER_UNSAFE_RAW);
    $confirmar = (string) filter_input(INPUT_POST, 'password_confirmar', FILTER_UNSAFE_RAW);

    if (!$actual || !$nueva || !$confirmar) {
        responderError(400, 'Faltan campos');
    }

    if ($nueva !== $confirmar) {
        responderError(400, 'Las contraseñas no coinciden');
    }         

    if (strlen($nueva) < 8) {
        responderError(400, 'La contraseña debe tener al menos 8 caracteres');
    }

    $con = conexionPDO();
    $modelo = new Usuario($con);
    $resultado = $modelo->cambiarPassword((int) $_SESSION['cliente']['id'], $actual, $nueva);

    if ($resultado !== true) responderError(400, $resultado);

    echo json_encode(['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente']);
    exit;
}