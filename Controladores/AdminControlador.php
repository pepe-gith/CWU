<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Usuario.php");
require_once("../Modelos/Empleado.php");
require_once("../Modelos/Reserva.php");
require_once("../Modelos/SolicitudEvento.php");
require_once("../Modelos/Servicio.php");
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
    'crearUsuario'         => crearUsuario(),
    'eventosCalendario'    => eventosCalendario(),
    default                => responderError(400, 'Acción no válida.')
};

function dashboard(): void {
    $con      = conexionPDO();
    $solModel = new SolicitudEvento($con);
    $resModel = new Reserva($con);
    $usrModel = new Usuario($con);

    echo json_encode(['ok' => true, 'data' => [
        'solicitudes_pendientes' => $solModel->contarPendientes(),
        'reservas_proximas'      => $resModel->statsProximas(),
        'total_clientes'         => $usrModel->totalClientes(),
        'ingresos_mes'           => $resModel->ingresosMes(),
        'reservas_sin_empleado'  => $resModel->statsSinEmpleado(),
        'ultimas_solicitudes'    => $solModel->ultimasPendientes(5),
        'proximas_reservas'      => $resModel->proximasLista(5),
    ]]);
    exit;
}

function solicitudes(): void {
    $modelo = new SolicitudEvento(conexionPDO());
    $estado = $_GET['estado'] ?? null;
    echo json_encode(['ok' => true, 'data' => $modelo->listarAdmin($estado ?: null)]);
    exit;
}

function cambiarEstado(): void {
    $id      = (int) filter_input(INPUT_POST, 'id',      FILTER_SANITIZE_NUMBER_INT);
    $estado  = trim((string) filter_input(INPUT_POST, 'estado',  FILTER_UNSAFE_RAW));
    $importe = filter_input(INPUT_POST, 'importe', FILTER_VALIDATE_FLOAT);

    $validos = ['pendiente', 'presupuestada', 'aceptada', 'reservada', 'rechazada'];
    if (!$id || !in_array($estado, $validos)) responderError(400, 'Datos no válidos');
    if ($estado === 'presupuestada' && ($importe === false || $importe < 0)) responderError(400, 'El importe es obligatorio para presupuestar');

    $notas       = trim((string) filter_input(INPUT_POST, 'notas_presupuesto',       FILTER_UNSAFE_RAW));
    $fechaLimite = trim((string) filter_input(INPUT_POST, 'fecha_limite_presupuesto', FILTER_UNSAFE_RAW));

    $modelo = new SolicitudEvento(conexionPDO());
    $modelo->cambiarEstadoAdmin($id, $estado, (float)($importe ?: 0), $notas ?: null, $fechaLimite ?: null);
    $modelo->notificarCliente($id, $estado);

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado']);
    exit;
}

function reservas(): void {
    $modelo = new Reserva(conexionPDO());
    echo json_encode(['ok' => true, 'data' => $modelo->listar(
        $_GET['filtro']  ?? 'proximas',
        $_GET['estado']  ?? '',
        trim($_GET['cliente'] ?? '')
    )]);
    exit;
}

function cambiarEstadoReserva(): void {
    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_UNSAFE_RAW));
    if (!$id || !in_array($estado, ['pendiente', 'confirmada', 'cancelada'])) responderError(400, 'Datos no válidos');

    $motivo = trim((string) filter_input(INPUT_POST, 'motivo', FILTER_UNSAFE_RAW)) ?: null;
    $modelo = new Reserva(conexionPDO());
    $modelo->cambiarEstado($id, $estado, $motivo);

    if ($estado === 'cancelada') {
        $modelo->notificarCancelacionCliente($id);
        $modelo->notificarCancelacionEmpleados($id);
    } elseif ($estado === 'confirmada') {
        $modelo->notificarConfirmacionCliente($id);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado']);
    exit;
}

function editarReserva(): void {
    $id          = (int) filter_input(INPUT_POST, 'id',            FILTER_SANITIZE_NUMBER_INT);
    $fechaEvento = trim((string) filter_input(INPUT_POST, 'fecha_evento',   FILTER_UNSAFE_RAW));
    $horaInicio  = trim((string) filter_input(INPUT_POST, 'hora_inicio',    FILTER_UNSAFE_RAW));
    $horaFin     = trim((string) filter_input(INPUT_POST, 'hora_fin',       FILTER_UNSAFE_RAW));
    $asistentes  = (int) filter_input(INPUT_POST, 'num_asistentes', FILTER_SANITIZE_NUMBER_INT);
    $observaciones = trim((string) filter_input(INPUT_POST, 'observaciones', FILTER_UNSAFE_RAW));

    if (!$id || !$fechaEvento || !$horaInicio || !$horaFin || !$asistentes) responderError(400, 'Faltan campos obligatorios');

    $modelo = new Reserva(conexionPDO());
    $modelo->editar($id, $fechaEvento, $horaInicio, $horaFin, $asistentes, $observaciones ?: null);
    echo json_encode(['ok' => true, 'mensaje' => 'Reserva actualizada correctamente']);
    exit;
}

function gestionarCambio(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'Datos no válidos');
    $modelo = new Reserva(conexionPDO());
    $modelo->gestionarCambio($id);
    echo json_encode(['ok' => true, 'mensaje' => 'Cambio marcado como gestionado']);
    exit;
}

function obtenerServicios(): void {
    $modelo = new Servicio(conexionPDO());
    $data   = array_map(fn($s) => ['id' => $s['id'], 'nombre' => $s['nombre'], 'precio_base' => $s['precio_base']], $modelo->listar());
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}

function crearReserva(): void {
    $idUsuario   = (int) filter_input(INPUT_POST, 'id_usuario',    FILTER_SANITIZE_NUMBER_INT);
    $idServicio  = (int) filter_input(INPUT_POST, 'id_servicio',   FILTER_SANITIZE_NUMBER_INT);
    $fechaEvento = trim((string) filter_input(INPUT_POST, 'fecha_evento',  FILTER_UNSAFE_RAW));
    $horaInicio  = trim((string) filter_input(INPUT_POST, 'hora_inicio',   FILTER_UNSAFE_RAW));
    $horaFin     = trim((string) filter_input(INPUT_POST, 'hora_fin',      FILTER_UNSAFE_RAW));
    $asistentes  = (int) filter_input(INPUT_POST, 'num_asistentes', FILTER_SANITIZE_NUMBER_INT);
    $observaciones = trim((string) filter_input(INPUT_POST, 'observaciones', FILTER_UNSAFE_RAW));

    if (!$idUsuario || !$idServicio || !$fechaEvento || !$horaInicio || !$horaFin || !$asistentes) responderError(400, 'Faltan campos obligatorios');

    $idEmpresa = (int) ($_SESSION['cliente']['id_empresa'] ?? 1);
    $modelo    = new Reserva(conexionPDO());
    $modelo->crear($idUsuario, $idServicio, $fechaEvento, $horaInicio, $horaFin, $asistentes, $observaciones ?: null, $idEmpresa);

    $idSolicitud = (int) filter_input(INPUT_POST, 'id_solicitud', FILTER_SANITIZE_NUMBER_INT);
    if ($idSolicitud) {
        (new SolicitudEvento(conexionPDO()))->marcarComoReservada($idSolicitud);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Reserva creada correctamente']);
    exit;
}

function usuarios(): void {
    $rol    = $_GET['rol'] ?? '';
    $buscar = trim($_GET['buscar'] ?? '');
    $modelo = new Usuario(conexionPDO());
    $rolInt = ($rol && in_array($rol, ['1','2','3'])) ? (int)$rol : null;
    echo json_encode(['ok' => true, 'data' => $modelo->listarAdmin($rolInt, $buscar)]);
    exit;
}

function empleados(): void {
    $modelo        = new Empleado(conexionPDO());
    $soloActivos   = !isset($_GET['inactivos']);
    $excluirReserva = (int) filter_input(INPUT_GET, 'excluir_reserva', FILTER_SANITIZE_NUMBER_INT) ?: null;
    echo json_encode(['ok' => true, 'data' => $modelo->listarParaAdmin($soloActivos, $excluirReserva)]);
    exit;
}

function guardarEmpleado(): void {
    $id           = (int) filter_input(INPUT_POST, 'id',            FILTER_SANITIZE_NUMBER_INT);
    $idUsuario    = (int) filter_input(INPUT_POST, 'id_usuario',    FILTER_SANITIZE_NUMBER_INT);
    $especialidad = trim((string) filter_input(INPUT_POST, 'especialidad', FILTER_UNSAFE_RAW));
    $precio       = filter_input(INPUT_POST, 'precio_por_hora', FILTER_VALIDATE_FLOAT);

    if ($precio === false || $precio < 0) responderError(400, 'El precio por hora es obligatorio');

    $modelo = new Empleado(conexionPDO());
    if ($id) {
        $modelo->guardar($id, $especialidad ?: null, $precio);
        echo json_encode(['ok' => true, 'mensaje' => 'Empleado actualizado']);
    } else {
        if (!$idUsuario) responderError(400, 'El usuario es obligatorio');
        $modelo->crear($idUsuario, (int)($_SESSION['cliente']['id_empresa'] ?? 1), $precio, $especialidad ?: null);
        echo json_encode(['ok' => true, 'mensaje' => 'Empleado creado correctamente']);
    }
    exit;
}

function roles(): void {
    echo json_encode(['ok' => true, 'data' => (new Usuario(conexionPDO()))->getRoles()]);
    exit;
}

function cambiarRol(): void {
    $id     = (int) filter_input(INPUT_POST, 'id',  FILTER_SANITIZE_NUMBER_INT);
    $rol    = (int) filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_NUMBER_INT);
    $forzar = !empty($_POST['forzar']);

    if (!$id || !in_array($rol, [1, 2, 3])) responderError(400, 'Datos no válidos');
    if ($id === (int) $_SESSION['cliente']['id']) responderError(403, 'No puedes cambiar tu propio rol.');

    $con      = conexionPDO();
    $usrModel = new Usuario($con);
    $empModel = new Empleado($con);

    $rolActual = $usrModel->getRol($id);

    if ($rolActual === 3 && $rol !== 3 && !$forzar) {
        $actividad = $usrModel->tieneActividadComoCliente($id);
        if ($actividad['solicitudes'] > 0 || $actividad['reservas'] > 0) {
            echo json_encode(['ok' => false, 'confirmar' => true, 'solicitudes' => $actividad['solicitudes'], 'reservas' => $actividad['reservas']]);
            exit;
        }
    }

    $usrModel->cambiarRol($id, $rol);

    if ($rol === 2) {
        $empModel->crearSiNoExiste($id, (int)($_SESSION['cliente']['id_empresa'] ?? 1));
    } else {
        $idEmpleado = $empModel->obtenerIdPorUsuario($id);
        if ($idEmpleado) $empModel->eliminarAsignacionesFuturas($idEmpleado);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Rol actualizado']);
    exit;
}

function editarUsuario(): void {
    $id        = (int) filter_input(INPUT_POST, 'id',        FILTER_SANITIZE_NUMBER_INT);
    $nombre    = trim((string) filter_input(INPUT_POST, 'nombre',    FILTER_UNSAFE_RAW));
    $apellidos = trim((string) filter_input(INPUT_POST, 'apellidos', FILTER_UNSAFE_RAW));
    $email     = trim((string) filter_input(INPUT_POST, 'email',     FILTER_SANITIZE_EMAIL));
    $telefono  = trim((string) filter_input(INPUT_POST, 'telefono',  FILTER_UNSAFE_RAW));

    if (!$id || !$nombre || !$apellidos || !$email) responderError(400, 'Faltan campos obligatorios');

    $modelo = new Usuario(conexionPDO());
    if ($modelo->emailEnUsoPoroOtro($email, $id)) responderError(400, 'Ese email ya está en uso');

    $modelo->editarAdmin($id, $nombre, $apellidos, $email, $telefono);
    echo json_encode(['ok' => true, 'mensaje' => 'Usuario actualizado']);
    exit;
}

function toggleActivo(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'Datos no válidos');
    if ($id === (int) $_SESSION['cliente']['id']) responderError(403, 'No puedes desactivarte a ti mismo');

    $con      = conexionPDO();
    $usrModel = new Usuario($con);
    $empModel = new Empleado($con);

    if ($usrModel->getActivo($id)) {
        $idEmpleado = $empModel->obtenerIdPorUsuario($id);
        if ($idEmpleado) {
            $reservas = $empModel->reservasFuturas($idEmpleado);
            if ($reservas) {
                echo json_encode(['ok' => false, 'confirmar' => true, 'reservas' => $reservas]);
                exit;
            }
        }
    }

    $activo = $usrModel->toggleActivo($id);
    echo json_encode(['ok' => true, 'activo' => $activo, 'mensaje' => $activo ? 'Usuario activado' : 'Usuario desactivado']);
    exit;
}

function confirmarDesactivar(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'Datos no válidos');
    if ($id === (int) $_SESSION['cliente']['id']) responderError(403, 'No puedes desactivarte a ti mismo');

    $con        = conexionPDO();
    $empModel   = new Empleado($con);
    $idEmpleado = $empModel->obtenerIdPorUsuario($id);
    if ($idEmpleado) $empModel->eliminarAsignacionesFuturas($idEmpleado);

    (new Usuario($con))->desactivar($id);
    echo json_encode(['ok' => true, 'activo' => false, 'mensaje' => 'Usuario desactivado y asignaciones futuras eliminadas']);
    exit;
}

function historialUsuario(): void {
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) responderError(400, 'ID requerido');

    $con      = conexionPDO();
    $usrModel = new Usuario($con);
    $rol      = $usrModel->getRol($id);

    if ($rol === 3) {
        echo json_encode([
            'ok'          => true,
            'rol'         => 'cliente',
            'solicitudes' => (new SolicitudEvento($con))->historialPorCliente($id),
            'reservas'    => (new Reserva($con))->historialPorCliente($id),
        ]);
    } elseif ($rol === 2) {
        $empModel   = new Empleado($con);
        $idEmpleado = $empModel->obtenerIdPorUsuario($id);
        echo json_encode([
            'ok'           => true,
            'rol'          => 'empleado',
            'asignaciones' => $idEmpleado ? $empModel->historialPorEmpleado($idEmpleado) : [],
        ]);
    } else {
        echo json_encode(['ok' => true, 'rol' => 'admin']);
    }
    exit;
}

function asignacionesReserva(): void {
    $id = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');
    echo json_encode(['ok' => true, 'data' => (new Reserva(conexionPDO()))->obtenerAsignaciones($id)]);
    exit;
}

function asignarEmpleado(): void {
    $idReserva  = (int) filter_input(INPUT_POST, 'id_reserva',  FILTER_SANITIZE_NUMBER_INT);
    $idEmpleado = (int) filter_input(INPUT_POST, 'id_empleado', FILTER_SANITIZE_NUMBER_INT);
    $rol        = trim((string) filter_input(INPUT_POST, 'rol_evento', FILTER_UNSAFE_RAW));
    if (!$idReserva || !$idEmpleado) responderError(400, 'Faltan datos obligatorios');

    $modelo = new Reserva(conexionPDO());
    if (!$modelo->existe($idReserva))                        responderError(404, 'Reserva no encontrada');
    if ($modelo->existeAsignacion($idReserva, $idEmpleado)) responderError(409, 'Este empleado ya está asignado a esta reserva');

    $modelo->asignarEmpleado($idReserva, $idEmpleado, $rol ?: null);
    echo json_encode(['ok' => true, 'mensaje' => 'Empleado asignado correctamente']);
    exit;
}

function eliminarAsignacion(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');
    (new Reserva(conexionPDO()))->eliminarAsignacion($id);
    echo json_encode(['ok' => true, 'mensaje' => 'Asignación eliminada']);
    exit;
}

function eventosCalendario(): void {
    $start = $_GET['start'] ?? '';
    $end   = $_GET['end']   ?? '';

    $con      = conexionPDO();
    $resModel = new Reserva($con);
    $solModel = new SolicitudEvento($con);

    $eventos = [];

    foreach ($resModel->eventosCalendario($start, $end) as $r) {
        $color    = $r['estado'] === 'confirmada' ? '#198754' : '#dc3545';
        $eventos[] = [
            'id'    => 'r-' . $r['id'],
            'title' => $r['cliente'] . ' ' . $r['apellidos'] . ' — ' . $r['servicio'],
            'start' => $r['fecha_evento'],
            'color' => $color,
            'extendedProps' => [
                'tipo'           => 'reserva',
                'cliente'        => $r['cliente'] . ' ' . $r['apellidos'],
                'servicio'       => $r['servicio'],
                'hora_inicio'    => substr($r['hora_inicio'], 0, 5),
                'hora_fin'       => substr($r['hora_fin'], 0, 5),
                'num_asistentes' => $r['num_asistentes'],
                'estado'         => $r['estado'],
                'empleados'      => $resModel->empleadosPorReserva($r['id']),
            ],
        ];
    }

    foreach ($solModel->solicitudesCalendario($start, $end) as $s) {
        $eventos[] = [
            'id'        => 's-' . $s['id'],
            'title'     => $s['cliente'] . ' ' . $s['apellidos'] . ' — ' . $s['tipo_evento'],
            'start'     => $s['fecha_evento'],
            'color'     => '#ffc107',
            'textColor' => '#000',
            'extendedProps' => [
                'tipo'             => 'solicitud',
                'cliente'          => $s['cliente'] . ' ' . $s['apellidos'],
                'tipo_evento'      => $s['tipo_evento'],
                'num_participantes'=> $s['num_participantes'],
            ],
        ];
    }

    echo json_encode(['ok' => true, 'data' => $eventos]);
    exit;
}

function crearUsuario(): void {
    $nombre    = trim((string) filter_input(INPUT_POST, 'nombre',    FILTER_UNSAFE_RAW));
    $apellidos = trim((string) filter_input(INPUT_POST, 'apellidos', FILTER_UNSAFE_RAW));
    $nif       = trim((string) filter_input(INPUT_POST, 'nif',       FILTER_UNSAFE_RAW));
    $telefono  = trim((string) filter_input(INPUT_POST, 'telefono',  FILTER_UNSAFE_RAW));
    $email     = trim((string) filter_input(INPUT_POST, 'email',     FILTER_SANITIZE_EMAIL));
    $password  = trim((string) filter_input(INPUT_POST, 'password',  FILTER_UNSAFE_RAW));
    $idRol     = (int) filter_input(INPUT_POST, 'id_rol', FILTER_SANITIZE_NUMBER_INT);

    if (!$nombre || !$nif || !$email || !$password || !$idRol) responderError(400, 'Faltan campos obligatorios');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) responderError(400, 'Email no válido');

    $con       = conexionPDO();
    $usrModel  = new Usuario($con);
    $idEmpresa = (int) ($_SESSION['cliente']['id_empresa'] ?? 1);

    try {
        $idNuevo = $usrModel->crearAdmin($nif, $nombre, $apellidos ?: null, $telefono ?: null, $email, password_hash($password, PASSWORD_BCRYPT), $idRol, $idEmpresa);
        if ($idRol === 2) {
            (new Empleado($con))->crear($idNuevo, $idEmpresa, 0.0);
        }
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario creado correctamente']);
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') responderError(409, 'El NIF o email ya están registrados');
        responderError(500, 'Error al crear el usuario');
    }
    exit;
}
