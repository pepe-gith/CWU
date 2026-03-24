<?php
require_once("../Modelos/conexion.php");
require_once("../Modelos/Usuario.php");

header('Content-Type: application/json');

$con = conexionPDO();
$modeloUsuario = new Usuario($con);

$usuarios = $modeloUsuario->mostrarDatos();

echo json_encode([
    'ok' => true,
    'datos' => $usuarios
]);
exit;
