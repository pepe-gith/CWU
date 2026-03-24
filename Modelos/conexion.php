<?php
require_once '../config.php';

function conexionMysqli(){
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if (!$connect) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($connect, "utf8mb4");
    return $connect;
};

// Mantiene compatibilidad con el codigo legacy que usa mysqli.
function conexion() {
    return conexionMysqli();
}

function conexionPDO(): PDO {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexion: " . $e->getMessage());
    }
}

?>