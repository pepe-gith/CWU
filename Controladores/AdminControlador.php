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
    'obtenerServicios'     => obtenerServicios(),
    'crearReserva'         => crearReserva(),
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
               se.id_usuario, se.motivo_revision,
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
    $id      = (int) filter_input(INPUT_POST, 'id',      FILTER_SANITIZE_NUMBER_INT);
    $estado  = trim((string) filter_input(INPUT_POST, 'estado',  FILTER_UNSAFE_RAW));
    $importe = filter_input(INPUT_POST, 'importe', FILTER_VALIDATE_FLOAT);

    $validos = ['pendiente', 'presupuestada', 'aceptada', 'reservada', 'rechazada'];
    if (!$id || !in_array($estado, $validos)) {
        responderError(400, 'Datos no válidos');
    }

    if ($estado === 'presupuestada' && ($importe === false || $importe < 0)) {
        responderError(400, 'El importe es obligatorio para presupuestar');
    }

    $con  = conexionPDO();
    if ($estado === 'presupuestada') {
        $notas        = trim((string) filter_input(INPUT_POST, 'notas_presupuesto',       FILTER_UNSAFE_RAW));
        $fechaLimite  = trim((string) filter_input(INPUT_POST, 'fecha_limite_presupuesto', FILTER_UNSAFE_RAW));
        $stmt = $con->prepare("UPDATE Solicitud_Evento SET estado = :estado, importe_presupuesto = :importe, notas_presupuesto = :notas, fecha_limite_presupuesto = :fecha_limite WHERE id = :id");
        $stmt->execute([
            ':estado'       => $estado,
            ':importe'      => $importe,
            ':notas'        => $notas ?: null,
            ':fecha_limite' => $fechaLimite ?: null,
            ':id'           => $id,
        ]);
    } else {
        $stmt = $con->prepare("UPDATE Solicitud_Evento SET estado = :estado WHERE id = :id");
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado']);
    exit;
}

function reservas(): void {
    $con    = conexionPDO();
    $filtro = $_GET['filtro']  ?? 'proximas';
    $estado = $_GET['estado']  ?? '';
    $cliente= trim($_GET['cliente'] ?? '');

    $where  = [];
    $params = [];

    if ($filtro === 'proximas') { $where[] = "r.fecha_evento >= CURDATE()"; }
    if ($filtro === 'pasadas')  { $where[] = "r.fecha_evento < CURDATE()";  }
    if ($estado)  { $where[] = "r.estado = :estado";  $params[':estado']  = $estado;  }
    if ($cliente) { $where[] = "CONCAT(u.nombre, ' ', u.apellidos) LIKE :cliente"; $params[':cliente'] = "%$cliente%"; }

    $sql = "
        SELECT r.id, r.fecha_reserva, r.fecha_evento, r.hora_inicio, r.hora_fin,
               r.num_asistentes, r.estado, r.observaciones,
               s.nombre AS servicio, u.nombre AS cliente, u.apellidos, u.telefono
        FROM Reserva r
        JOIN Servicio s ON s.id = r.id_servicio
        JOIN Usuario u ON u.id = r.id_usuario
    ";

    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY r.fecha_evento ASC";

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

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

function obtenerServicios(): void {
    $con  = conexionPDO();
    $stmt = $con->query("SELECT id, nombre, precio_base FROM Servicio ORDER BY nombre");
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function crearReserva(): void {
    $idUsuario    = (int) filter_input(INPUT_POST, 'id_usuario',    FILTER_SANITIZE_NUMBER_INT);
    $idServicio   = (int) filter_input(INPUT_POST, 'id_servicio',   FILTER_SANITIZE_NUMBER_INT);
    $fechaEvento  = trim((string) filter_input(INPUT_POST, 'fecha_evento',  FILTER_UNSAFE_RAW));
    $horaInicio   = trim((string) filter_input(INPUT_POST, 'hora_inicio',   FILTER_UNSAFE_RAW));
    $horaFin      = trim((string) filter_input(INPUT_POST, 'hora_fin',      FILTER_UNSAFE_RAW));
    $asistentes   = (int) filter_input(INPUT_POST, 'num_asistentes', FILTER_SANITIZE_NUMBER_INT);
    $observaciones= trim((string) filter_input(INPUT_POST, 'observaciones', FILTER_UNSAFE_RAW));

    if (!$idUsuario || !$idServicio || !$fechaEvento || !$horaInicio || !$horaFin || !$asistentes) {
        responderError(400, 'Faltan campos obligatorios');
    }

    $con  = conexionPDO();
    $stmt = $con->prepare("
        INSERT INTO Reserva (fecha_reserva, fecha_evento, hora_inicio, hora_fin, num_asistentes, estado, observaciones, id_usuario, id_servicio, id_empresa)
        VALUES (CURDATE(), :fecha_evento, :hora_inicio, :hora_fin, :asistentes, 'pendiente', :observaciones, :id_usuario, :id_servicio, 1)
    ");
    $stmt->execute([
        ':fecha_evento'  => $fechaEvento,
        ':hora_inicio'   => $horaInicio,
        ':hora_fin'      => $horaFin,
        ':asistentes'    => $asistentes,
        ':observaciones' => $observaciones ?: null,
        ':id_usuario'    => $idUsuario,
        ':id_servicio'   => $idServicio,
    ]);

    $idSolicitud = (int) filter_input(INPUT_POST, 'id_solicitud', FILTER_SANITIZE_NUMBER_INT);
    if ($idSolicitud) {
        $con->prepare("UPDATE Solicitud_Evento SET estado = 'reservada' WHERE id = :id")
            ->execute([':id' => $idSolicitud]);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Reserva creada correctamente']);
    exit;
}
