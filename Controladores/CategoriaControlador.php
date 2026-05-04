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
    'listar'  => listar(),
    'crear'   => crear(),
    'editar'  => editar(),
    'eliminar'=> eliminar(),
    default   => responderError(400, 'Acción no válida.')
};

function listar(): void {
    $con  = conexionPDO();
    $stmt = $con->query("
        SELECT c.id, c.nombre,
               (SELECT COUNT(*) FROM Solicitud_Evento se WHERE se.tipo_evento = c.id) AS num_solicitudes
        FROM Categoria c
        ORDER BY c.nombre ASC
    ");
    echo json_encode(['ok' => true, 'categorias' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

function crear(): void {
    $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
    if (!$nombre) responderError(400, 'El nombre es obligatorio');

    $con  = conexionPDO();
    $dup  = $con->prepare("SELECT id FROM Categoria WHERE nombre = :nombre");
    $dup->execute([':nombre' => $nombre]);
    if ($dup->fetch()) responderError(409, 'Ya existe una categoría con ese nombre');

    $con->prepare("INSERT INTO Categoria (nombre) VALUES (:nombre)")->execute([':nombre' => $nombre]);
    echo json_encode(['ok' => true, 'mensaje' => 'Categoría creada']);
    exit;
}

function editar(): void {
    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
    if (!$id || !$nombre) responderError(400, 'Faltan datos obligatorios');

    $con = conexionPDO();
    $dup = $con->prepare("SELECT id FROM Categoria WHERE nombre = :nombre AND id != :id");
    $dup->execute([':nombre' => $nombre, ':id' => $id]);
    if ($dup->fetch()) responderError(409, 'Ya existe una categoría con ese nombre');

    $con->prepare("UPDATE Categoria SET nombre = :nombre WHERE id = :id")->execute([':nombre' => $nombre, ':id' => $id]);
    echo json_encode(['ok' => true, 'mensaje' => 'Categoría actualizada']);
    exit;
}

function eliminar(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $con   = conexionPDO();
    $check = $con->prepare("SELECT COUNT(*) FROM Solicitud_Evento WHERE tipo_evento = :id");
    $check->execute([':id' => $id]);
    if ((int) $check->fetchColumn() > 0) responderError(409, 'No se puede eliminar: tiene solicitudes asociadas');

    $con->prepare("DELETE FROM Categoria WHERE id = :id")->execute([':id' => $id]);
    echo json_encode(['ok' => true, 'mensaje' => 'Categoría eliminada']);
    exit;
}

