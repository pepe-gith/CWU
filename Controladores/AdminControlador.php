<?php
require_once("../Modelos/conexion.php");
require_once("../inc/helpers.php");

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (empty($_SESSION['cliente']['id_rol']) || (int)$_SESSION['cliente']['id_rol'] !== 1) {
    responderError(403, 'Acceso denegado');
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'dashboard'            => dashboard(),
    'solicitudes'          => solicitudes(),
    'cambiarEstado'        => cambiarEstado(),
    'reservas'             => reservas(),
    'cambiarEstadoReserva' => cambiarEstadoReserva(),
    default                => responderError(400, 'Acción no válida.')
};

function dashboard(): void {
    $con = conexionPDO();

    $stats = [];

    // Solicitudes pendientes
    $stmt = $con->query("SELECT COUNT(*) FROM Solicitud_Evento WHERE estado = 'pendiente'");
    $stats['solicitudes_pendientes'] = (int) $stmt->fetchColumn();

    // Reservas próximas confirmadas
    $stmt = $con->query("SELECT COUNT(*) FROM Reserva WHERE estado = 'confirmada' AND fecha_evento >= CURDATE()");
    $stats['reservas_proximas'] = (int) $stmt->fetchColumn();

    // Total clientes
    $stmt = $con->query("SELECT COUNT(*) FROM Usuario WHERE id_rol = 3");
    $stats['total_clientes'] = (int) $stmt->fetchColumn();

    // Ingresos este mes
    $stmt = $con->query("SELECT COALESCE(SUM(monto), 0) FROM Pago_Cliente WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
    $stats['ingresos_mes'] = (float) $stmt->fetchColumn();

    // Últimas 5 solicitudes pendientes
    $stmt = $con->query("
        SELECT se.id, se.fecha_solicitud, se.fecha_evento, se.num_participantes, se.estado,
               c.nombre AS tipo, u.nombre AS cliente, u.apellidos
        FROM Solicitud_Evento se
        JOIN Categoria c ON c.id = se.tipo_evento
        JOIN Usuario u ON u.id = se.id_usuario
        WHERE se.estado = 'pendiente'
        ORDER BY se.fecha_solicitud DESC
        LIMIT 5
    ");
    $stats['ultimas_solicitudes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Próximas 5 reservas confirmadas
    $stmt = $con->query("
        SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes, r.estado,
               s.nombre AS servicio, u.nombre AS cliente, u.apellidos
        FROM Reserva r
        JOIN Servicio s ON s.id = r.id_servicio
        JOIN Usuario u ON u.id = r.id_usuario
        WHERE r.estado = 'confirmada' AND r.fecha_evento >= CURDATE()
        ORDER BY r.fecha_evento ASC
        LIMIT 5
    ");
    $stats['proximas_reservas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['ok' => true, 'data' => $stats]);
    exit;
}

function solicitudes(): void {
    $con    = conexionPDO();
    $estado = $_GET['estado'] ?? '';

    $sql = "
        SELECT se.id, se.fecha_solicitud, se.fecha_evento, se.num_participantes,
               se.sala, se.realidad_virtual, se.tarta, se.nombre_protagonista, se.estado,
               c.nombre AS tipo, u.nombre AS cliente, u.apellidos, u.telefono, u.email
        FROM Solicitud_Evento se
        JOIN Categoria c ON c.id = se.tipo_evento
        JOIN Usuario u ON u.id = se.id_usuario
    ";

    $params = [];
    if ($estado) {
        $sql .= " WHERE se.estado = :estado";
        $params[':estado'] = $estado;
    }

    $sql .= " ORDER BY se.fecha_solicitud DESC";

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function cambiarEstado(): void {
    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_UNSAFE_RAW));

    $validos = ['pendiente', 'presupuestada', 'aceptada', 'rechazada'];
    if (!$id || !in_array($estado, $validos)) {
        responderError(400, 'Datos no válidos');
    }

    $con  = conexionPDO();
    $stmt = $con->prepare("UPDATE Solicitud_Evento SET estado = :estado WHERE id = :id");
    $stmt->execute([':estado' => $estado, ':id' => $id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado']);
    exit;
}

function reservas(): void {
    $con    = conexionPDO();
    $filtro = $_GET['filtro'] ?? 'proximas';

    $sql = "
        SELECT r.id, r.fecha_reserva, r.fecha_evento, r.hora_inicio, r.hora_fin,
               r.num_asistentes, r.estado, r.observaciones,
               s.nombre AS servicio, u.nombre AS cliente, u.apellidos, u.telefono
        FROM Reserva r
        JOIN Servicio s ON s.id = r.id_servicio
        JOIN Usuario u ON u.id = r.id_usuario
    ";

    $sql .= match($filtro) {
        'pasadas'  => " WHERE r.fecha_evento < CURDATE()",
        'proximas' => " WHERE r.fecha_evento >= CURDATE()",
        default    => "",
    };

    $sql .= " ORDER BY r.fecha_evento ASC";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function cambiarEstadoReserva(): void {
    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_UNSAFE_RAW));

    $validos = ['pendiente', 'confirmada', 'cancelada'];
    if (!$id || !in_array($estado, $validos)) {
        responderError(400, 'Datos no válidos');
    }

    $con  = conexionPDO();
    $stmt = $con->prepare("UPDATE Reserva SET estado = :estado WHERE id = :id");
    $stmt->execute([':estado' => $estado, ':id' => $id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado']);
    exit;
}
