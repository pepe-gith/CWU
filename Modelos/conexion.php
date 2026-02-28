<?php
require_once '../config.php';

function conexion(){
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if (!$connect) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($connect, "utf8mb4");
    return $connect;
};

?>