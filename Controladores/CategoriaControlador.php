<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Categoria.php");
require_once("../inc/helpers.php");

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'listar' => listar(),
    default   => responderError(400, 'Acción no válida.')
};

function listar(): void {
    $con = conexionPDO();
    $modelo = new Categoria($con);
    $categorias = $modelo->mostrarCategorias();

    echo json_encode(['ok' => true, 'categorias' => $categorias]);
    exit;
}

