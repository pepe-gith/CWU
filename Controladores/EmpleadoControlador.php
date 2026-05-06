<?php
require_once("../Modelos/conexion.php");
require_once("../inc/helpers.php");

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (empty($_SESSION['cliente']['id_rol']) || (int)$_SESSION['cliente']['id_rol'] !== 2) {
    responderError(403, 'Acceso denegado');
}

$idUsuario = (int) $_SESSION['cliente']['id'];

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'agenda'                => agenda($idUsuario),
    'historial'             => historial($idUsuario),
    'responderAsignacion'   => responderAsignacion($idUsuario),
    'obtenerPerfilEmpleado' => obtenerPerfilEmpleado($idUsuario),
    'guardarPerfilEmpleado' => guardarPerfilEmpleado($idUsuario),
    default                 => responderError(400, 'Acción no válida.')
};

function obtenerPerfilEmpleado(int $idUsuario): void {
    $con  = conexionPDO();
    $stmt = $con->prepare("SELECT especialidad, precio_por_hora FROM Empleado WHERE id_usuario = :id");
    $stmt->execute([':id' => $idUsuario]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data) responderError(404, 'No tienes perfil de empleado.');
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}

function guardarPerfilEmpleado(int $idUsuario): void {
    $especialidad   = trim((string) filter_input(INPUT_POST, 'especialidad',   FILTER_UNSAFE_RAW));
    $precio         = filter_input(INPUT_POST, 'precio_por_hora', FILTER_VALIDATE_FLOAT);

    if ($precio === false || $precio < 0) responderError(400, 'Precio no válido.');

    $con  = conexionPDO();
    $stmt = $con->prepare("UPDATE Empleado SET especialidad = :esp, precio_por_hora = :precio WHERE id_usuario = :id");
    $stmt->execute([':esp' => $especialidad ?: null, ':precio' => $precio, ':id' => $idUsuario]);

    echo json_encode(['ok' => true, 'mensaje' => 'Datos laborales actualizados']);
    exit;
}

function agenda(int $idUsuario): void {
    $con  = conexionPDO();

    $emp = $con->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
    $emp->execute([':id' => $idUsuario]);
    $empleado = $emp->fetch(PDO::FETCH_ASSOC);
    if (!$empleado) responderError(404, 'No tienes perfil de empleado.');

    $stmt = $con->prepare("
        SELECT a.id, a.rol_evento, a.estado,
               r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes,
               s.nombre AS servicio, u.nombre AS cliente, u.apellidos AS cliente_apellidos
        FROM Asignacion_Empleado a
        JOIN Reserva r ON r.id = a.id_reserva
        JOIN Servicio s ON s.id = r.id_servicio
        JOIN Usuario u ON u.id = r.id_usuario
        WHERE a.id_empleado = :id_emp AND r.estado != 'cancelada'
        ORDER BY r.fecha_evento ASC
    ");
    $stmt->execute([':id_emp' => $empleado['id']]);
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function historial(int $idUsuario): void {
    $con = conexionPDO();

    $emp = $con->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
    $emp->execute([':id' => $idUsuario]);
    $empleado = $emp->fetch(PDO::FETCH_ASSOC);
    if (!$empleado) responderError(404, 'No tienes perfil de empleado.');

    $stmt = $con->prepare("
        SELECT a.id, a.rol_evento, a.estado, r.estado AS estado_reserva,
               r.fecha_evento, r.hora_inicio, r.hora_fin,
               s.nombre AS servicio, u.nombre AS cliente, u.apellidos AS cliente_apellidos
        FROM Asignacion_Empleado a
        JOIN Reserva r ON r.id = a.id_reserva
        JOIN Servicio s ON s.id = r.id_servicio
        JOIN Usuario u ON u.id = r.id_usuario
        WHERE a.id_empleado = :id_emp AND (r.fecha_evento < CURDATE() OR r.estado = 'cancelada')
        ORDER BY r.fecha_evento DESC
        LIMIT 50
    ");
    $stmt->execute([':id_emp' => $empleado['id']]);
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function responderAsignacion(int $idUsuario): void {
    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_UNSAFE_RAW));

    if (!$id || !in_array($estado, ['aceptada', 'rechazada'])) {
        responderError(400, 'Datos no válidos');
    }

    $con = conexionPDO();

    $emp = $con->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
    $emp->execute([':id' => $idUsuario]);
    $empleado = $emp->fetch(PDO::FETCH_ASSOC);
    if (!$empleado) responderError(403, 'No tienes perfil de empleado.');

    $check = $con->prepare("SELECT id FROM Asignacion_Empleado WHERE id = :id AND id_empleado = :emp");
    $check->execute([':id' => $id, ':emp' => $empleado['id']]);
    if (!$check->fetch()) responderError(403, 'No tienes permiso para modificar esta asignación.');

    $stmt = $con->prepare("UPDATE Asignacion_Empleado SET estado = :estado WHERE id = :id");
    $stmt->execute([':estado' => $estado, ':id' => $id]);

    if ($estado === 'rechazada') {
        $con->prepare("
            INSERT INTO Notificacion (id_usuario, mensaje)
            SELECT u.id, CONCAT(emp.nombre, ' ', emp.apellidos, ' ha rechazado la asignación de la reserva del ', DATE_FORMAT(r.fecha_evento, '%d/%m/%Y'), ' (', s.nombre, ').')
            FROM Asignacion_Empleado ae
            JOIN Empleado e ON e.id = ae.id_empleado
            JOIN Usuario emp ON emp.id = e.id_usuario
            JOIN Reserva r ON r.id = ae.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id_rol = 1
            WHERE ae.id = :id
        ")->execute([':id' => $id]);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Respuesta registrada']);
    exit;
}
