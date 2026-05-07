<?php

function responderError(int $codigoError, string $mensaje): never {
    http_response_code($codigoError);
    echo json_encode(['ok' => false, 'error' => $mensaje]);
    exit;
}

function menuCliente(string $menu): array {
    return [
        [
            'label' => 'Resumen',         
            'href' => '/cwu/Vistas/cliente/InicioView.php',      
            'icon' => 'bi-house-door',    
            'active' => $menu === 'resumen'
        ],
        [
            'label' => 'Nueva solicitud', 
            'href' => '/cwu/Vistas/cliente/SolEventoView.php',   
            'icon' => 'bi-plus-circle',   
            'active' => $menu === 'solEvento'
        ],
        [
            'label' => 'Mis solicitudes', 
            'href' => '/cwu/Vistas/cliente/SolicitudesView.php', 
            'icon' => 'bi-list-check',    
            'active' => $menu === 'solicitudes'
        ],
        [
            'label' => 'Mis reservas',    
            'href' => '/cwu/Vistas/cliente/ReservasView.php',    
            'icon' => 'bi-calendar-check',
            'active' => $menu === 'reservas', 
            'notif' => true
        ],
    ];
}

function menuAdmin(string $menu): array {
    return [
        [
            'label' => 'Dashboard',    
            'href' => '/cwu/Vistas/admin/DashboardView.php',    
            'icon' => 'bi-speedometer2',    
            'active' => $menu === 'dashboard'
        ],
        [
            'label' => 'Solicitudes',  
            'href' => '/cwu/Vistas/admin/SolicitudesView.php',  
            'icon' => 'bi-inbox',           
            'active' => $menu === 'solicitudes'
        ],
        [
            'label' => 'Reservas',     
            'href' => '/cwu/Vistas/admin/ReservasView.php',     
            'icon' => 'bi-calendar-check',  
            'active' => $menu === 'reservas', 
            'notif' => true
        ],
        [
            'label' => 'Usuarios',     
            'href' => '/cwu/Vistas/admin/UsuariosView.php',     
            'icon' => 'bi-people',          
            'active' => $menu === 'usuarios'
        ],
        [
            'label' => 'Empleados',    
            'href' => '/cwu/Vistas/admin/EmpleadosView.php',    
            'icon' => 'bi-person-badge',    
            'active' => $menu === 'empleados'
        ],
        [
            'label' => 'Servicios',    
            'href' => '/cwu/Vistas/admin/ServiciosView.php',    
            'icon' => 'bi-box-seam',        
            'active' => $menu === 'servicios'
        ],
        [
            'label' => 'Categorías',   
            'href' => '/cwu/Vistas/admin/CategoriasView.php',   
            'icon' => 'bi-tags',            
            'active' => $menu === 'categorias'
        ],
        [
            'label' => 'Calendario',   
            'href' => '/cwu/Vistas/admin/CalendarioView.php',   
            'icon' => 'bi-calendar3',       
            'active' => $menu === 'calendario'
        ],
    ];
}

function menuEmpleado(string $menu): array {
    return [
        [
            'label' => 'Mi agenda', 
            'href' => '/cwu/Vistas/empleado/AgendaView.php',    
            'icon' => 'bi-calendar-event', 
            'active' => $menu === 'agenda',   
            'notif' => true
        ],
        [
            'label' => 'Historial', 
            'href' => '/cwu/Vistas/empleado/HistorialView.php', 
            'icon' => 'bi-clock-history',  
            'active' => $menu === 'historial'
        ],
    ];
}
