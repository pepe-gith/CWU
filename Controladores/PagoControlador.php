<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Pago.php");
require_once("../Modelos/Reserva.php");
require_once("../inc/helpers.php");
require_once("../inc/sesion.php");

iniciarSesion();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'obtener' => obtener(),
    'crear' => crearAdmin(),
    'eliminar' => eliminarPago(),
    'confirmar' => confirmarPago(),
    'rechazar' => rechazarPago(),
    'solicitarPago' => solicitarPago(),
    default => responderError(400, 'Acción no válida.')
};

function esAdmin(): bool {
    return (int)($_SESSION['cliente']['id_rol'] ?? 0) === 1;
}

function esCliente(): bool {
    return !empty($_SESSION['cliente']['id']);
}

function obtener(): void {
    if (!esAdmin()) responderError(403, 'Acceso denegado');
    $idReserva = (int) filter_input(INPUT_GET, 'id_reserva', FILTER_SANITIZE_NUMBER_INT);
    if (!$idReserva) responderError(400, 'ID de reserva inválido');

    $modelo = new Pago(conexionPDO());
    echo json_encode([
        'ok' => true,
        'pagos' => $modelo->obtenerPorReserva($idReserva),
        'total' => $modelo->totalPagado($idReserva),
    ]);
    exit;
}

function crearAdmin(): void {
    if (!esAdmin()) responderError(403, 'Acceso denegado');
    $idReserva = (int) filter_input(INPUT_POST, 'id_reserva', FILTER_SANITIZE_NUMBER_INT);
    $monto     = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
    $metodo    = trim((string) filter_input(INPUT_POST, 'metodo', FILTER_UNSAFE_RAW));

    if (!$idReserva)                     responderError(400, 'ID de reserva inválido');
    if ($monto === false || $monto <= 0) responderError(400, 'El monto debe ser mayor que 0');
    if (!in_array($metodo, ['tarjeta', 'efectivo', 'transferencia', 'bizum'])) responderError(400, 'Método de pago no válido');

    (new Pago(conexionPDO()))->crear($idReserva, $monto, $metodo);
    echo json_encode(['ok' => true, 'mensaje' => 'Pago registrado']);
    exit;
}

function eliminarPago(): void {
    if (!esAdmin()) responderError(403, 'Acceso denegado');
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    (new Pago(conexionPDO()))->eliminar($id);
    echo json_encode(['ok' => true, 'mensaje' => 'Pago eliminado']);
    exit;
}

function confirmarPago(): void {
    if (!esAdmin()) responderError(403, 'Acceso denegado');
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $modelo = new Pago(conexionPDO());
    $modelo->confirmar($id);
    $modelo->notificarCliente($id, 'confirmado');
    echo json_encode(['ok' => true, 'mensaje' => 'Pago confirmado']);
    exit;
}

function rechazarPago(): void {
    if (!esAdmin()) responderError(403, 'Acceso denegado');
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $modelo = new Pago(conexionPDO());
    $modelo->rechazar($id);
    $modelo->notificarCliente($id, 'rechazado');
    echo json_encode(['ok' => true, 'mensaje' => 'Pago rechazado']);
    exit;
}

function solicitarPago(): void {
    if (!esCliente()) responderError(401, 'No autenticado');

    $idUsuario  = (int) $_SESSION['cliente']['id'];
    $idReserva  = (int) filter_input(INPUT_POST, 'id_reserva', FILTER_SANITIZE_NUMBER_INT);
    $monto      = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
    $metodo     = trim((string) filter_input(INPUT_POST, 'metodo', FILTER_UNSAFE_RAW));
    $referencia = trim((string) filter_input(INPUT_POST, 'referencia', FILTER_UNSAFE_RAW)) ?: null;

    if (!$idReserva) responderError(400, 'ID de reserva inválido');
    if ($monto === false || $monto <= 0) responderError(400, 'El monto debe ser mayor que 0');
    if (!in_array($metodo, ['bizum', 'transferencia', 'efectivo', 'tarjeta'])) responderError(400, 'Método de pago no válido');

    $conexion = conexionPDO();
    $reserva = (new Reserva($conexion))->obtenerParaCliente($idReserva, $idUsuario);
    if (!$reserva) responderError(404, 'Reserva no encontrada');
    if ($reserva['estado'] === 'cancelada') responderError(400, 'No puedes registrar pagos en una reserva cancelada');

    $modelo = new Pago($conexion);
    $modelo->crearPorCliente($idReserva, $monto, $metodo, $referencia);
    $modelo->notificarAdmins($idReserva);

    echo json_encode(['ok' => true, 'mensaje' => 'Pago notificado. Lo verificaremos en breve.']);
    exit;
}
