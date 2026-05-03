<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/SolicitudEvento.php");
require_once("../inc/helpers.php");

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'crear'               => crear(),
    'mostrar'             => mostrar(),
    'responderPresupuesto'=> responderPresupuesto(),
    'solicitarRevision'   => solicitarRevision(),
    'misReservas'         => misReservas(),
    default               => responderError(400, 'Acción no válida.')
};

function crear(): void {
    if (empty($_SESSION['cliente']['id'])) {
        responderError(401, 'No autenticado.');
    }

    $idUsuario = (int) $_SESSION['cliente']['id'];
    $idEmpresa = (int) ($_SESSION['cliente']['id_empresa'] ?? 0);

    if (!$idEmpresa) {
        responderError(500, 'No se pudo determinar la empresa del usuario.');
    }

    $fechaEvento     = trim($_POST['fecha_evento']    ?? '');
    $tipoEvento      = trim($_POST['tipo_evento']     ?? '');
    $numParticipantes = trim($_POST['num_participantes'] ?? '');

    if (!$fechaEvento || !$tipoEvento || !$numParticipantes) {
        responderError(400, 'Faltan datos obligatorios.');
    }

    // Validar formato de fecha (YYYY-MM-DD)
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

function responderPresupuesto(): void {
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado.');

    $id       = (int) filter_input(INPUT_POST, 'id',       FILTER_SANITIZE_NUMBER_INT);
    $respuesta= trim((string) filter_input(INPUT_POST, 'respuesta', FILTER_UNSAFE_RAW));

    if (!$id || !in_array($respuesta, ['aceptada', 'rechazada'])) {
        responderError(400, 'Datos no válidos.');
    }

    $con    = conexionPDO();
    $modelo = new SolicitudEvento($con);
    $ok = $modelo->responderPresupuesto($id, (int) $_SESSION['cliente']['id'], $respuesta);

    if ($ok === false)  responderError(403, 'No puedes modificar esta solicitud.');
    if (is_string($ok)) responderError(400, $ok);

    echo json_encode(['ok' => true, 'mensaje' => 'Solicitud ' . $respuesta]);
    exit;
}

function solicitarRevision(): void {
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado.');

    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $motivo = trim((string) filter_input(INPUT_POST, 'motivo', FILTER_UNSAFE_RAW));

    if (!$id) responderError(400, 'Datos no válidos.');

    $con    = conexionPDO();
    $modelo = new SolicitudEvento($con);
    $ok     = $modelo->solicitarRevision($id, (int) $_SESSION['cliente']['id'], $motivo);

    if (!$ok) responderError(403, 'No puedes modificar esta solicitud.');

    echo json_encode(['ok' => true, 'mensaje' => 'Revisión solicitada correctamente']);
    exit;
}

function misReservas(): void {
    if (empty($_SESSION['cliente']['id'])) responderError(401, 'No autenticado.');

    $con  = conexionPDO();
    $stmt = $con->prepare("
        SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes,
               r.estado, r.observaciones, s.nombre AS servicio
        FROM Reserva r
        JOIN Servicio s ON s.id = r.id_servicio
        WHERE r.id_usuario = :id
        ORDER BY r.fecha_evento DESC
    ");
    $stmt->execute([':id' => (int) $_SESSION['cliente']['id']]);

    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
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

