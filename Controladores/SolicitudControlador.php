<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/SolicitudEvento.php");
require_once("../inc/helpers.php");

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'crear'   => crear(),
    'mostrar' => mostrar(),
    default   => responderError(400, 'Acción no válida.')
};

function crear(): void {
    if (empty($_SESSION['cliente']['id'])) {
        responderError(401, 'No autenticado.');
    }

    $idUsuario = (int) $_SESSION['cliente']['id'];
    $idEmpresa = 1;

    $fechaEvento     = trim($_POST['fecha_evento']    ?? '');
    $tipoEvento      = trim($_POST['tipo_evento']     ?? '');
    $numParticipantes = trim($_POST['num_participantes'] ?? '');

    if (!$fechaEvento || !$tipoEvento || !$numParticipantes) {
        responderError(400, 'Faltan datos obligatorios.');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEvento)) {
        responderError(400, 'Fecha de evento no válida.');
    }

    $datos = [
        'fecha_evento'        => $fechaEvento,
        'tipo_evento'         => (int) $tipoEvento,
        'nombre_protagonista' => trim($_POST['nombre_protagonista'] ?? '') ?: null,
        'num_participantes'   => (int) $numParticipantes,
        'sala'                => isset($_POST['sala'])             ? (int) $_POST['sala']             : null,
        'realidad_virtual'    => isset($_POST['realidad_virtual']) ? (int) $_POST['realidad_virtual'] : null,
        'tarta'               => isset($_POST['tarta'])            ? (int) $_POST['tarta']            : null,
    ];

    $con = conexionPDO();
    $modelo = new SolicitudEvento($con);
    $id = $modelo->crear($idUsuario, $idEmpresa, $datos);

    http_response_code(201);
    echo json_encode(['ok' => true, 'id' => $id]);
    exit;
}

function mostrar(): void {
    if (empty($_SESSION['cliente']['id'])) {
        responderError(401, 'No autenticado.');
    }

    $idUsuario = (int) $_SESSION['cliente']['id'];

    $con = conexionPDO();
    $modelo = new SolicitudEvento($con);
    $solicitudes = $modelo->obtenerPorUsuario($idUsuario);

    echo json_encode(['ok' => true, 'data' => $solicitudes]);
    exit;
}

