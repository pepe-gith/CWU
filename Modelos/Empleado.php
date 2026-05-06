<?php

class Empleado {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerIdPorUsuario(int $idUsuario): ?int {
        $stmt = $this->conexion->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
        $stmt->execute([':id' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function obtenerPerfil(int $idUsuario): ?array {
        $stmt = $this->conexion->prepare("SELECT especialidad, precio_por_hora FROM Empleado WHERE id_usuario = :id");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function guardarPerfil(int $idUsuario, ?string $especialidad, float $precio): void {
        $this->conexion->prepare("UPDATE Empleado SET especialidad = :esp, precio_por_hora = :precio WHERE id_usuario = :id")
            ->execute([':esp' => $especialidad, ':precio' => $precio, ':id' => $idUsuario]);
    }

    public function agenda(int $idEmpleado): array {
        $stmt = $this->conexion->prepare("
            SELECT a.id, a.rol_evento, a.estado,
                   r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes,
                   s.nombre AS servicio, u.nombre AS cliente, u.apellidos AS cliente_apellidos
            FROM Asignacion_Empleado a
            JOIN Reserva r ON r.id = a.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id = r.id_usuario
            WHERE a.id_empleado = :id_emp AND r.estado != 'cancelada'
            ORDER BY r.fecha_evento ASC
        ");
        $stmt->execute([':id_emp' => $idEmpleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function historial(int $idEmpleado): array {
        $stmt = $this->conexion->prepare("
            SELECT a.id, a.rol_evento, a.estado, r.estado AS estado_reserva,
                   r.fecha_evento, r.hora_inicio, r.hora_fin,
                   s.nombre AS servicio, u.nombre AS cliente, u.apellidos AS cliente_apellidos
            FROM Asignacion_Empleado a
            JOIN Reserva r ON r.id = a.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id = r.id_usuario
            WHERE a.id_empleado = :id_emp AND (r.fecha_evento < CURDATE() OR r.estado = 'cancelada')
            ORDER BY r.fecha_evento DESC
            LIMIT 50
        ");
        $stmt->execute([':id_emp' => $idEmpleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarAsignacion(int $idAsignacion, int $idEmpleado): bool {
        $stmt = $this->conexion->prepare("SELECT id FROM Asignacion_Empleado WHERE id = :id AND id_empleado = :emp");
        $stmt->execute([':id' => $idAsignacion, ':emp' => $idEmpleado]);
        return (bool) $stmt->fetch();
    }

    public function actualizarAsignacion(int $idAsignacion, string $estado): void {
        $this->conexion->prepare("UPDATE Asignacion_Empleado SET estado = :estado WHERE id = :id")
            ->execute([':estado' => $estado, ':id' => $idAsignacion]);
    }

    public function notificarRechazoAdmins(int $idAsignacion): void {
        $this->conexion->prepare("
            INSERT INTO Notificacion (id_usuario, mensaje)
            SELECT u.id, CONCAT(emp.nombre, ' ', emp.apellidos, ' ha rechazado la asignación de la reserva del ', DATE_FORMAT(r.fecha_evento, '%d/%m/%Y'), ' (', s.nombre, ').')
            FROM Asignacion_Empleado ae
            JOIN Empleado e ON e.id = ae.id_empleado
            JOIN Usuario emp ON emp.id = e.id_usuario
            JOIN Reserva r ON r.id = ae.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id_rol = 1
            WHERE ae.id = :id
        ")->execute([':id' => $idAsignacion]);
    }

    public function listarParaAdmin(bool $soloActivos = true, ?int $excluirReserva = null): array {
        $where = $soloActivos ? "WHERE u.activo = 1" : "WHERE u.activo = 0";
        $params = [];

        if ($excluirReserva) {
            $where .= " AND e.id NOT IN (SELECT id_empleado FROM Asignacion_Empleado WHERE id_reserva = :excluir)";
            $params[':excluir'] = $excluirReserva;
        }

        $stmt = $this->conexion->prepare("
            SELECT e.id, e.especialidad, e.precio_por_hora,
                   u.nombre, u.apellidos, u.email, u.telefono, u.activo
            FROM Empleado e
            JOIN Usuario u ON u.id = e.id_usuario
            $where
            ORDER BY u.nombre ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar(int $id, ?string $especialidad, float $precio): void {
        $this->conexion->prepare("UPDATE Empleado SET especialidad=:esp, precio_por_hora=:precio WHERE id=:id")
            ->execute([':esp' => $especialidad, ':precio' => $precio, ':id' => $id]);
    }

    public function crear(int $idUsuario, int $idEmpresa, float $precio, ?string $especialidad = null): void {
        $this->conexion->prepare("INSERT INTO Empleado (especialidad, precio_por_hora, id_usuario, id_empresa) VALUES (:esp, :precio, :id_usuario, :id_empresa)")
            ->execute([':esp' => $especialidad, ':precio' => $precio, ':id_usuario' => $idUsuario, ':id_empresa' => $idEmpresa]);
    }

    public function crearSiNoExiste(int $idUsuario, int $idEmpresa): void {
        $stmt = $this->conexion->prepare("SELECT id FROM Empleado WHERE id_usuario = :id");
        $stmt->execute([':id' => $idUsuario]);
        if (!$stmt->fetch()) {
            $this->crear($idUsuario, $idEmpresa, 0.0);
        }
    }

    public function reservasFuturas(int $idEmpleado): array {
        $stmt = $this->conexion->prepare("
            SELECT r.id, r.fecha_evento, s.nombre AS servicio
            FROM Asignacion_Empleado ae
            JOIN Reserva r ON r.id = ae.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            WHERE ae.id_empleado = :emp AND r.fecha_evento >= CURDATE()
            ORDER BY r.fecha_evento ASC
        ");
        $stmt->execute([':emp' => $idEmpleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarAsignacionesFuturas(int $idEmpleado): void {
        $this->conexion->prepare("
            DELETE ae FROM Asignacion_Empleado ae
            JOIN Reserva r ON r.id = ae.id_reserva
            WHERE ae.id_empleado = :emp AND r.fecha_evento >= CURDATE()
        ")->execute([':emp' => $idEmpleado]);
    }

    public function historialPorEmpleado(int $idEmpleado): array {
        $stmt = $this->conexion->prepare("
            SELECT a.estado, r.fecha_evento, r.hora_inicio, r.hora_fin, s.nombre AS servicio
            FROM Asignacion_Empleado a
            JOIN Reserva r ON r.id = a.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            WHERE a.id_empleado = :emp
            ORDER BY r.fecha_evento DESC
            LIMIT 5
        ");
        $stmt->execute([':emp' => $idEmpleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
