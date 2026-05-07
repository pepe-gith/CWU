<?php

function iniciarSesion(): void {
    if (session_status() !== PHP_SESSION_NONE) return;

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);

    session_start();
    validarSesionUsuario();
}

function validarSesionUsuario(): void {
    if (empty($_SESSION['cliente']['id'])) return;

    require_once __DIR__ . '/../Modelos/conexion.php';
    try {
        $stmt = conexionPDO()->prepare("SELECT 1 FROM Usuario WHERE id = :id AND activo = 1 LIMIT 1");
        $stmt->execute([':id' => (int) $_SESSION['cliente']['id']]);
        if ($stmt->fetchColumn()) return;
    } catch (\Throwable $e) {
        return;
    }

    cerrarSesionForzada();
}

function cerrarSesionForzada(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', $p['secure'], $p['httponly']);
    }
    session_destroy();
}
