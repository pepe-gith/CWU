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
    'gestionarCambio'      => gestionarCambio(),
    'editarReserva'        => editarReserva(),
    'empleados'            => empleados(),
    'guardarEmpleado'      => guardarEmpleado(),
    'usuariosEmpleado'     => usuariosEmpleado(),
    'asignacionesReserva'  => asignacionesReserva(),
    'asignarEmpleado'      => asignarEmpleado(),
    'eliminarAsignacion'   => eliminarAsignacion(),
    'obtenerServicios'     => obtenerServicios(),
    'crearReserva'         => crearReserva(),
    'usuarios'             => usuarios(),
    'cambiarRol'           => cambiarRol(),
    'roles'                => roles(),
    'editarUsuario'        => editarUsuario(),
    'toggleActivo'         => toggleActivo(),
    'confirmarDesactivar'  => confirmarDesactivar(),
    'historialUsuario'     => historialUsuario(),
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
    elseif ($filtro === 'pasadas') { $where[] = "r.fecha_evento < CURDATE()"; }
    if ($estado)  { $where[] = "r.estado = :estado";  $params[':estado']  = $estado;  }
    if ($cliente) { $where[] = "CONCAT(u.nombre, ' ', u.apellidos) LIKE :cliente"; $params[':cliente'] = "%$cliente%"; }

    $sql = "
        SELECT r.id, r.fecha_reserva, r.fecha_evento, r.hora_inicio, r.hora_fin,
               r.num_asistentes, r.estado, r.observaciones,
               r.cambio_solicitado, r.motivo_cambio, r.motivo_cancelacion,
               r.id_servicio, s.nombre AS servicio, u.nombre AS cliente, u.apellidos, u.telefono,
               (SELECT COUNT(*) FROM Asignacion_Empleado ae WHERE ae.id_reserva = r.id) AS num_empleados
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
    if ($estado === 'cancelada') {
        $motivo = trim((string) filter_input(INPUT_POST, 'motivo', FILTER_UNSAFE_RAW)) ?: null;
        $stmt = $con->prepare("UPDATE Reserva SET estado = :estado, motivo_cancelacion = :motivo WHERE id = :id");
        $stmt->execute([':estado' => $estado, ':motivo' => $motivo, ':id' => $id]);
    } else {
        $stmt = $con->prepare("UPDATE Reserva SET estado = :estado, motivo_cancelacion = NULL WHERE id = :id");
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado']);
    exit;
}

function editarReserva(): void {
    $id          = (int) filter_input(INPUT_POST, 'id',            FILTER_SANITIZE_NUMBER_INT);
    $fechaEvento = trim((string) filter_input(INPUT_POST, 'fecha_evento',  FILTER_UNSAFE_RAW));
    $horaInicio  = trim((string) filter_input(INPUT_POST, 'hora_inicio',   FILTER_UNSAFE_RAW));
    $horaFin     = trim((string) filter_input(INPUT_POST, 'hora_fin',      FILTER_UNSAFE_RAW));
    $asistentes    = (int) filter_input(INPUT_POST, 'num_asistentes', FILTER_SANITIZE_NUMBER_INT);
    $observaciones = trim((string) filter_input(INPUT_POST, 'observaciones', FILTER_UNSAFE_RAW));

    if (!$id || !$fechaEvento || !$horaInicio || !$horaFin || !$asistentes) {
        responderError(400, 'Faltan campos obligatorios');
    }

    $con = conexionPDO();
    $stmt = $con->prepare("
        UPDATE Reserva SET fecha_evento=:fecha_evento, hora_inicio=:hora_inicio, hora_fin=:hora_fin,
        num_asistentes=:asistentes, observaciones=:observaciones
        WHERE id=:id
    ");
    $stmt->execute([
        ':fecha_evento'  => $fechaEvento,
        ':hora_inicio'   => $horaInicio,
        ':hora_fin'      => $horaFin,
        ':asistentes'    => $asistentes,
        ':observaciones' => $observaciones ?: null,
        ':id'            => $id,
    ]);

    echo json_encode(['ok' => true, 'mensaje' => 'Reserva actualizada correctamente']);
    exit;
}

function gestionarCambio(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'Datos no válidos');

    $con = conexionPDO();
    $con->prepare("UPDATE Reserva SET cambio_solicitado = 0, motivo_cambio = NULL WHERE id = :id")
        ->execute([':id' => $id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Cambio marcado como gestionado']);
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

function usuarios(): void {
    $con  = conexionPDO();
    $rol  = $_GET['rol'] ?? '';
    $buscar = trim($_GET['buscar'] ?? '');

    $where  = [];
    $params = [];

    if ($rol && in_array($rol, ['1', '2', '3'])) {
        $where[] = "u.id_rol = :rol";
        $params[':rol'] = (int) $rol;
    }
    if ($buscar) {
        $where[] = "(u.nombre LIKE :b OR u.apellidos LIKE :b2 OR u.nif LIKE :b3 OR u.email LIKE :b4)";
        $params[':b']  = "%$buscar%";
        $params[':b2'] = "%$buscar%";
        $params[':b3'] = "%$buscar%";
        $params[':b4'] = "%$buscar%";
    }

    $sql = "SELECT u.id, u.nif, u.nombre, u.apellidos, u.email, u.telefono, u.id_rol, u.activo
            FROM Usuario u"
         . ($where ? " WHERE " . implode(" AND ", $where) : "")
         . " ORDER BY u.nombre ASC";

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function empleados(): void {
    $con   = conexionPDO();
    $where = isset($_GET['inactivos']) ? 'WHERE u.activo = 0' : 'WHERE u.activo = 1';
    $excluirReserva = (int) filter_input(INPUT_GET, 'excluir_reserva', FILTER_SANITIZE_NUMBER_INT);
    if ($excluirReserva) {
        $where .= ' AND e.id NOT IN (SELECT id_empleado FROM Asignacion_Empleado WHERE id_reserva = ' . $excluirReserva . ')';
    }
    $sql   = "SELECT e.id, e.especialidad, e.precio_por_hora,
                     u.nombre, u.apellidos, u.email, u.telefono, u.activo
              FROM Empleado e
              JOIN Usuario u ON u.id = e.id_usuario
              $where
              ORDER BY u.nombre ASC";
    $stmt  = $con->query($sql);
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function usuariosEmpleado(): void {
    $con  = conexionPDO();
    $stmt = $con->query("
        SELECT u.id, u.nombre, u.apellidos, u.email
        FROM Usuario u
        WHERE u.id_rol = 2
        AND u.activo = 1
        AND u.id NOT IN (SELECT id_usuario FROM Empleado)
        ORDER BY u.nombre ASC
    ");
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function guardarEmpleado(): void {
    $id           = (int) filter_input(INPUT_POST, 'id',            FILTER_SANITIZE_NUMBER_INT);
    $idUsuario    = (int) filter_input(INPUT_POST, 'id_usuario',    FILTER_SANITIZE_NUMBER_INT);
    $especialidad = trim((string) filter_input(INPUT_POST, 'especialidad', FILTER_UNSAFE_RAW));
    $precio       = filter_input(INPUT_POST, 'precio_por_hora', FILTER_VALIDATE_FLOAT);

    if (!$precio || $precio < 0) responderError(400, 'El precio por hora es obligatorio');

    $con = conexionPDO();

    if ($id) {
        $stmt = $con->prepare("UPDATE Empleado SET especialidad=:esp, precio_por_hora=:precio WHERE id=:id");
        $stmt->execute([':esp' => $especialidad ?: null, ':precio' => $precio, ':id' => $id]);
        echo json_encode(['ok' => true, 'mensaje' => 'Empleado actualizado']);
    } else {
        if (!$idUsuario) responderError(400, 'El usuario es obligatorio');
        $stmt = $con->prepare("INSERT INTO Empleado (especialidad, precio_por_hora, id_usuario, id_empresa) VALUES (:esp, :precio, :id_usuario, 1)");
        $stmt->execute([':esp' => $especialidad ?: null, ':precio' => $precio, ':id_usuario' => $idUsuario]);
        echo json_encode(['ok' => true, 'mensaje' => 'Empleado creado correctamente']);
    }
    exit;
}

function roles(): void {
    $con  = conexionPDO();
    $stmt = $con->query("SELECT id, nombre_rol FROM Rol ORDER BY id");
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function cambiarRol(): void {
    $id  = (int) filter_input(INPUT_POST, 'id',  FILTER_SANITIZE_NUMBER_INT);
    $rol = (int) filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_NUMBER_INT);

    if (!$id || !in_array($rol, [1, 2, 3])) {
        responderError(400, 'Datos no válidos');
    }

    if ($id === (int) $_SESSION['cliente']['id']) {
        responderError(403, 'No puedes cambiar tu propio rol.');
    }

    $con  = conexionPDO();
    $stmt = $con->prepare("UPDATE Usuario SET id_rol = :rol WHERE id = :id");
    $stmt->execute([':rol' => $rol, ':id' => $id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Rol actualizado']);
    exit;
}

function editarUsuario(): void {
    $id        = (int) filter_input(INPUT_POST, 'id',        FILTER_SANITIZE_NUMBER_INT);
    $nombre    = trim((string) filter_input(INPUT_POST, 'nombre',    FILTER_UNSAFE_RAW));
    $apellidos = trim((string) filter_input(INPUT_POST, 'apellidos', FILTER_UNSAFE_RAW));
    $email     = trim((string) filter_input(INPUT_POST, 'email',     FILTER_SANITIZE_EMAIL));
    $telefono  = trim((string) filter_input(INPUT_POST, 'telefono',  FILTER_UNSAFE_RAW));

    if (!$id || !$nombre || !$apellidos || !$email) {
        responderError(400, 'Faltan campos obligatorios');
    }

    $con  = conexionPDO();

    $dup = $con->prepare("SELECT 1 FROM Usuario WHERE email = :email AND id != :id LIMIT 1");
    $dup->execute([':email' => $email, ':id' => $id]);
    if ($dup->fetchColumn()) responderError(400, 'Ese email ya está en uso');

    $stmt = $con->prepare("UPDATE Usuario SET nombre=:nombre, apellidos=:apellidos, email=:email, telefono=:telefono WHERE id=:id");
    $stmt->execute([':nombre' => $nombre, ':apellidos' => $apellidos, ':email' => $email, ':telefono' => $telefono, ':id' => $id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Usuario actualizado']);
    exit;
}

function toggleActivo(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!$id) responderError(400, 'Datos no válidos');
    if ($id === (int) $_SESSION['cliente']['id']) responderError(403, 'No puedes desactivarte a ti mismo');

    $con = conexionPDO();

    // Si vamos a desactivar, comprobar si es empleado con reservas futuras asignadas
    $rowActivo = $con->prepare("SELECT activo FROM Usuario WHERE id = :id");
    $rowActivo->execute([':id' => $id]);
    $activoActual = (bool) $rowActivo->fetchColumn();

    if ($activoActual) {
        $emp = $con->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
        $emp->execute([':id' => $id]);
        $idEmpleado = $emp->fetchColumn();

        if ($idEmpleado) {
            $check = $con->prepare("
                SELECT r.id, r.fecha_evento, s.nombre AS servicio
                FROM Asignacion_Empleado ae
                JOIN Reserva r ON r.id = ae.id_reserva
                JOIN Servicio s ON s.id = r.id_servicio
                WHERE ae.id_empleado = :emp AND r.fecha_evento >= CURDATE()
                ORDER BY r.fecha_evento ASC
            ");
            $check->execute([':emp' => $idEmpleado]);
            $reservas = $check->fetchAll(PDO::FETCH_ASSOC);

            if ($reservas) {
                echo json_encode(['ok' => false, 'confirmar' => true, 'reservas' => $reservas]);
                exit;
            }
        }
    }

    $stmt = $con->prepare("UPDATE Usuario SET activo = NOT activo WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $row = $con->prepare("SELECT activo FROM Usuario WHERE id = :id");
    $row->execute([':id' => $id]);
    $activo = (bool) $row->fetchColumn();

    echo json_encode(['ok' => true, 'activo' => $activo, 'mensaje' => $activo ? 'Usuario activado' : 'Usuario desactivado']);
    exit;
}

function confirmarDesactivar(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'Datos no válidos');
    if ($id === (int) $_SESSION['cliente']['id']) responderError(403, 'No puedes desactivarte a ti mismo');

    $con = conexionPDO();

    $emp = $con->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
    $emp->execute([':id' => $id]);
    $idEmpleado = $emp->fetchColumn();

    if ($idEmpleado) {
        $del = $con->prepare("
            DELETE ae FROM Asignacion_Empleado ae
            JOIN Reserva r ON r.id = ae.id_reserva
            WHERE ae.id_empleado = :emp AND r.fecha_evento >= CURDATE()
        ");
        $del->execute([':emp' => $idEmpleado]);
    }

    $stmt = $con->prepare("UPDATE Usuario SET activo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['ok' => true, 'activo' => false, 'mensaje' => 'Usuario desactivado y asignaciones futuras eliminadas']);
    exit;
}

function historialUsuario(): void {
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) responderError(400, 'ID requerido');

    $con = conexionPDO();

    $sol = $con->prepare("
        SELECT se.id, se.fecha_evento, se.estado, c.nombre AS tipo
        FROM Solicitud_Evento se
        JOIN Categoria c ON c.id = se.tipo_evento
        WHERE se.id_usuario = :id
        ORDER BY se.fecha_evento DESC
        LIMIT 5
    ");
    $sol->execute([':id' => $id]);

    $res = $con->prepare("
        SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.estado, s.nombre AS servicio
        FROM Reserva r
        JOIN Servicio s ON s.id = r.id_servicio
        WHERE r.id_usuario = :id
        ORDER BY r.fecha_evento DESC
        LIMIT 5
    ");
    $res->execute([':id' => $id]);

    echo json_encode([
        'ok'         => true,
        'solicitudes' => $sol->fetchAll(PDO::FETCH_ASSOC),
        'reservas'    => $res->fetchAll(PDO::FETCH_ASSOC),
    ]);
    exit;
}

function asignacionesReserva(): void {
    $id  = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $con  = conexionPDO();
    $stmt = $con->prepare("
        SELECT a.id, a.rol_evento, u.nombre, u.apellidos
        FROM Asignacion_Empleado a
        JOIN Empleado e ON e.id = a.id_empleado
        JOIN Usuario u ON u.id = e.id_usuario
        WHERE a.id_reserva = :id
        ORDER BY a.id ASC
    ");
    $stmt->execute([':id' => $id]);
    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function asignarEmpleado(): void {
    $idReserva  = (int) filter_input(INPUT_POST, 'id_reserva',  FILTER_SANITIZE_NUMBER_INT);
    $idEmpleado = (int) filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_NUMBER_INT);
    $rol        = trim((string) filter_input(INPUT_POST, 'rol_evento', FILTER_UNSAFE_RAW));

    if (!$idReserva || !$idEmpleado) responderError(400, 'Faltan datos obligatorios');

    $con = conexionPDO();

    $r = $con->prepare("SELECT id FROM Reserva WHERE id = :id");
    $r->execute([':id' => $idReserva]);
    if (!$r->fetch()) responderError(404, 'Reserva no encontrada');

    $dup = $con->prepare("SELECT id FROM Asignacion_Empleado WHERE id_reserva = :r AND id_empleado = :e");
    $dup->execute([':r' => $idReserva, ':e' => $idEmpleado]);
    if ($dup->fetch()) responderError(409, 'Este empleado ya está asignado a esta reserva');

    $stmt = $con->prepare("INSERT INTO Asignacion_Empleado (rol_evento, id_reserva, id_empleado)
                           VALUES (:rol, :reserva, :empleado)");
    $stmt->execute([
        ':rol'     => $rol ?: null,
        ':reserva' => $idReserva,
        ':empleado'=> $idEmpleado,
    ]);
    echo json_encode(['ok' => true, 'mensaje' => 'Empleado asignado correctamente']);
    exit;
}

function eliminarAsignacion(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $con  = conexionPDO();
    $stmt = $con->prepare("DELETE FROM Asignacion_Empleado WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['ok' => true, 'mensaje' => 'Asignación eliminada']);
    exit;
}
