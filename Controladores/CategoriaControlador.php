<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Categoria.php");
require_once("../inc/helpers.php");

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (empty($_SESSION['cliente']['id_rol']) || (int)$_SESSION['cliente']['id_rol'] !== 1) {
    responderError(403, 'Acceso denegado');
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'listar'   => listar(),
    'crear'    => crear(),
    'editar'   => editar(),
    'eliminar' => eliminar(),
    default    => responderError(400, 'Acción no válida.')
};

function listar(): void {
    $modelo = new Categoria(conexionPDO());
    echo json_encode(['ok' => true, 'categorias' => $modelo->listarConConteo()]);
    exit;
}

function crear(): void {
    $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
    if (!$nombre) responderError(400, 'El nombre es obligatorio');

    $modelo = new Categoria(conexionPDO());
    if ($modelo->existeNombre($nombre)) responderError(409, 'Ya existe una categoría con ese nombre');

    $modelo->crear($nombre);
    echo json_encode(['ok' => true, 'mensaje' => 'Categoría creada']);
    exit;
}

function editar(): void {
    $id     = (int) filter_input(INPUT_POST, 'id',     FILTER_SANITIZE_NUMBER_INT);
    $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
    if (!$id || !$nombre) responderError(400, 'Faltan datos obligatorios');

    $modelo = new Categoria(conexionPDO());
    if ($modelo->existeNombre($nombre, $id)) responderError(409, 'Ya existe una categoría con ese nombre');

    $modelo->editar($id, $nombre);
    echo json_encode(['ok' => true, 'mensaje' => 'Categoría actualizada']);
    exit;
}

function eliminar(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $modelo = new Categoria(conexionPDO());
    if ($modelo->tieneSolicitudes($id)) responderError(409, 'No se puede eliminar: tiene solicitudes asociadas');

    $modelo->eliminar($id);
    echo json_encode(['ok' => true, 'mensaje' => 'Categoría eliminada']);
    exit;
}
