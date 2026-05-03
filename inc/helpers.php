<?php

function responderError(int $code, string $mensaje): never {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $mensaje]);
    exit;
}

function menuCliente(string $active): array {
    return [
        ['label' => 'Resumen',         'href' => '/cwu/Vistas/cliente/InicioView.php',      'icon' => 'bi-house-door',    'active' => $active === 'resumen'],
        ['label' => 'Nueva solicitud', 'href' => '/cwu/Vistas/cliente/SolEventoView.php',   'icon' => 'bi-plus-circle',   'active' => $active === 'solEvento'],
        ['label' => 'Mis solicitudes', 'href' => '/cwu/Vistas/cliente/SolicitudesView.php', 'icon' => 'bi-list-check',    'active' => $active === 'solicitudes'],
    ];
}

function menuAdmin(string $active): array {
    return [
        ['label' => 'Dashboard',    'href' => '/cwu/Vistas/admin/DashboardView.php',    'icon' => 'bi-speedometer2',    'active' => $active === 'dashboard'],
        ['label' => 'Solicitudes',  'href' => '/cwu/Vistas/admin/SolicitudesView.php',  'icon' => 'bi-inbox',           'active' => $active === 'solicitudes'],
        ['label' => 'Reservas',     'href' => '/cwu/Vistas/admin/ReservasView.php',     'icon' => 'bi-calendar-check',  'active' => $active === 'reservas'],
        ['label' => 'Usuarios',     'href' => '/cwu/Vistas/admin/UsuariosView.php',     'icon' => 'bi-people',          'active' => $active === 'usuarios'],
        ['label' => 'Monitores',    'href' => '/cwu/Vistas/admin/MonitoresView.php',    'icon' => 'bi-person-badge',    'active' => $active === 'monitores'],
        ['label' => 'Categorías',   'href' => '/cwu/Vistas/admin/CategoriasView.php',   'icon' => 'bi-tags',            'active' => $active === 'categorias'],
        ['label' => 'Calendario',   'href' => '/cwu/Vistas/admin/CalendarioView.php',   'icon' => 'bi-calendar3',       'active' => $active === 'calendario'],
    ];
}

function menuMonitor(string $active): array {
    return [
        ['label' => 'Mi agenda',    'href' => '/cwu/Vistas/monitor/AgendaView.php',    'icon' => 'bi-calendar-event',  'active' => $active === 'agenda'],
        ['label' => 'Historial',    'href' => '/cwu/Vistas/monitor/HistorialView.php', 'icon' => 'bi-clock-history',   'active' => $active === 'historial'],
    ];
}
