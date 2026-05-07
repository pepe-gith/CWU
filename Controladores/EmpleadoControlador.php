<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Empleado.php");
require_once("../inc/helpers.php");
require_once("../inc/sesion.php");

iniciarSesion();

header('Content-Type: application/json');

if (empty($_SESSION['cliente']['id_rol']) || (int)$_SESSION['cliente']['id_rol'] !== 2) {
    responderError(403, 'Acceso denegado');
}

$idUsuario = (int) $_SESSION['cliente']['id'];

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'agenda'  => agenda($idUsuario),
    'historial' => historial($idUsuario),
    'responderAsignacion' => responderAsignacion($idUsuario),
    'obtenerPerfilEmpleado' => obtenerPerfilEmpleado($idUsuario),
    'guardarPerfilEmpleado' => guardarPerfilEmpleado($idUsuario),
    default => responderError(400, 'Acción no válida.')
};

function obtenerPerfilEmpleado(int $idUsuario): void {
    $modelo = new Empleado(conexionPDO());
    $data   = $modelo->obtenerPerfil($idUsuario);
    if (!$data) responderError(404, 'No tienes perfil de empleado.');
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}

function guardarPerfilEmpleado(int $idUsuario): void {
    $especialidad = trim((string) filter_input(INPUT_POST, 'especialidad',   FILTER_UNSAFE_RAW));
    $precio       = filter_input(INPUT_POST, 'precio_por_hora', FILTER_VALIDATE_FLOAT);
    if ($precio === false || $precio < 0) responderError(400, 'Precio no válido.');

    $modelo = new Empleado(conexionPDO());
    $modelo->guardarPerfil($idUsuario, $especialidad ?: null, $precio);
    echo json_encode(['ok' => true, 'mensaje' => 'Datos laborales actualizados']);
    exit;
}

function agenda(int $idUsuario): void {
    $modelo  = new Empleado(conexionPDO());
    $idEmpleado = $modelo->obtenerIdPorUsuario($idUsuario);
    if (!$idEmpleado) responderError(404, 'No tienes perfil de empleado.');
    echo json_encode(['ok' => true, 'data' => $modelo->agenda($idEmpleado)]);
    exit;
}

function historial(int $idUsuario): void {
    $modelo     = new Empleado(conexionPDO());
    $idEmpleado = $modelo->obtenerIdPorUsuario($idUsuario);
    if (!$idEmpleado) responderError(404, 'No tienes perfil de empleado.');
    echo json_encode(['ok' => true, 'data' => $modelo->historial($idEmpleado)]);
    exit;
}

function responderAsignacion(int $idUsuario): void {
    $id  = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_UNSAFE_RAW));
    if (!$id || !in_array($estado, ['aceptada', 'rechazada'])) responderError(400, 'Datos no válidos');

    $modelo     = new Empleado(conexionPDO());
    $idEmpleado = $modelo->obtenerIdPorUsuario($idUsuario);
    if (!$idEmpleado) responderError(403, 'No tienes perfil de empleado.');

    if (!$modelo->verificarAsignacion($id, $idEmpleado)) responderError(403, 'No tienes permiso para modificar esta asignación.');

    $modelo->actualizarAsignacion($id, $estado);
    if ($estado === 'rechazada') $modelo->notificarRechazoAdmins($id);

    echo json_encode(['ok' => true, 'mensaje' => 'Respuesta registrada']);
    exit;
}
