<?php
require_once("../Modelos/Conexion.php");
require_once("../Modelos/Usuario.php");

header('Content-Type: application/json');

$campo = trim((string) ($_POST['campo'] ?? ''));
$valor = trim((string) ($_POST['valor'] ?? ''));

if (!$campo || !$valor) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Faltan datos.']);
    exit;
}

$con = conexionPDO();
$modeloUsuario = new Usuario($con);

switch ($campo) {
    case 'nif':
        echo json_encode(['existe' => $modeloUsuario->existeNif($valor)]);
        break;
    case 'email':
        echo json_encode(['existe' => $modeloUsuario->existeEmail($valor)]);
        break;
    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Campo no válido.']);
}
exit;
