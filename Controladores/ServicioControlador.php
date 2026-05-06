<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Servicio.php");
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
    $con      = conexionPDO();
    $modelo   = new Servicio($con);
    $catModel = new Categoria($con);

    echo json_encode([
        'ok'         => true,
        'servicios'  => $modelo->listar(),
        'categorias' => $catModel->mostrarCategorias(),
    ]);
    exit;
}

function crear(): void {
    $nombre      = trim((string) filter_input(INPUT_POST, 'nombre',      FILTER_UNSAFE_RAW));
    $descripcion = trim((string) filter_input(INPUT_POST, 'descripcion', FILTER_UNSAFE_RAW));
    $precio      = filter_input(INPUT_POST, 'precio_base', FILTER_VALIDATE_FLOAT);
    $capacidad   = filter_input(INPUT_POST, 'capacidad',   FILTER_VALIDATE_INT);
    $idCategoria = (int) filter_input(INPUT_POST, 'id_categoria', FILTER_SANITIZE_NUMBER_INT);

    if (!$nombre)                              responderError(400, 'El nombre es obligatorio');
    if ($precio === false || $precio <= 0)     responderError(400, 'El precio debe ser mayor que 0');
    if ($capacidad === false || $capacidad <= 0) responderError(400, 'La capacidad debe ser mayor que 0');
    if (!$idCategoria)                         responderError(400, 'Debes seleccionar una categoría');

    $modelo = new Servicio(conexionPDO());
    $modelo->crear($nombre, $descripcion ?: null, $precio, $capacidad, $idCategoria, (int) $_SESSION['cliente']['id_empresa']);

    echo json_encode(['ok' => true, 'mensaje' => 'Servicio creado']);
    exit;
}

function editar(): void {
    $id          = (int) filter_input(INPUT_POST, 'id',          FILTER_SANITIZE_NUMBER_INT);
    $nombre      = trim((string) filter_input(INPUT_POST, 'nombre',      FILTER_UNSAFE_RAW));
    $descripcion = trim((string) filter_input(INPUT_POST, 'descripcion', FILTER_UNSAFE_RAW));
    $precio      = filter_input(INPUT_POST, 'precio_base', FILTER_VALIDATE_FLOAT);
    $capacidad   = filter_input(INPUT_POST, 'capacidad',   FILTER_VALIDATE_INT);
    $idCategoria = (int) filter_input(INPUT_POST, 'id_categoria', FILTER_SANITIZE_NUMBER_INT);

    if (!$id)                                  responderError(400, 'ID inválido');
    if (!$nombre)                              responderError(400, 'El nombre es obligatorio');
    if ($precio === false || $precio <= 0)     responderError(400, 'El precio debe ser mayor que 0');
    if ($capacidad === false || $capacidad <= 0) responderError(400, 'La capacidad debe ser mayor que 0');
    if (!$idCategoria)                         responderError(400, 'Debes seleccionar una categoría');

    $modelo = new Servicio(conexionPDO());
    $modelo->editar($id, $nombre, $descripcion ?: null, $precio, $capacidad, $idCategoria);

    echo json_encode(['ok' => true, 'mensaje' => 'Servicio actualizado']);
    exit;
}

function eliminar(): void {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) responderError(400, 'ID inválido');

    $modelo = new Servicio(conexionPDO());
    if ($modelo->tieneReservas($id)) responderError(409, 'No se puede eliminar: tiene reservas asociadas');

    $modelo->eliminar($id);
    echo json_encode(['ok' => true, 'mensaje' => 'Servicio eliminado']);
    exit;
}
