<?php

class Reserva {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function listar(string $filtro = '', string $estado = '', string $cliente = ''): array {
        $where  = [];
        $params = [];

        if ($filtro === 'proximas')     $where[] = "r.fecha_evento >= CURDATE()";
        elseif ($filtro === 'pasadas')  $where[] = "r.fecha_evento < CURDATE()";
        if ($estado)  { $where[] = "r.estado = :estado";                               $params[':estado']  = $estado; }
        if ($cliente) { $where[] = "CONCAT(u.nombre, ' ', u.apellidos) LIKE :cliente"; $params[':cliente'] = "%$cliente%"; }

        $sql = "
            SELECT r.id, r.fecha_reserva, r.fecha_evento, r.hora_inicio, r.hora_fin,
                   r.num_asistentes, r.estado, r.observaciones,
                   r.cambio_solicitado, r.motivo_cambio, r.motivo_cancelacion,
                   r.id_servicio, s.nombre AS servicio, u.nombre AS cliente, u.apellidos, u.telefono,
                   (SELECT COUNT(*) FROM Asignacion_Empleado ae WHERE ae.id_reserva = r.id) AS num_empleados,
                   (SELECT COUNT(*) FROM Asignacion_Empleado ae WHERE ae.id_reserva = r.id AND ae.estado = 'aceptada')  AS emp_aceptadas,
                   (SELECT COUNT(*) FROM Asignacion_Empleado ae WHERE ae.id_reserva = r.id AND ae.estado = 'pendiente') AS emp_pendientes,
                   (SELECT COUNT(*) FROM Asignacion_Empleado ae WHERE ae.id_reserva = r.id AND ae.estado = 'rechazada') AS emp_rechazadas
            FROM Reserva r
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id = r.id_usuario
        ";

        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY r.fecha_evento ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstado(int $id, string $estado, ?string $motivo = null): void {
        if ($estado === 'cancelada') {
            $this->conexion->prepare("UPDATE Reserva SET estado = :estado, motivo_cancelacion = :motivo WHERE id = :id")
                ->execute([':estado' => $estado, ':motivo' => $motivo, ':id' => $id]);
        } else {
            $this->conexion->prepare("UPDATE Reserva SET estado = :estado, motivo_cancelacion = NULL WHERE id = :id")
                ->execute([':estado' => $estado, ':id' => $id]);
        }
    }

    public function notificarCancelacionCliente(int $id): void {
        $this->conexion->prepare("
            INSERT INTO Notificacion (id_usuario, mensaje)
            SELECT r.id_usuario, CONCAT('Tu reserva del ', DATE_FORMAT(r.fecha_evento, '%d/%m/%Y'), ' (', s.nombre, ') ha sido cancelada.')
            FROM Reserva r JOIN Servicio s ON s.id = r.id_servicio WHERE r.id = :id
        ")->execute([':id' => $id]);
    }

    public function notificarCancelacionEmpleados(int $id): void {
        $this->conexion->prepare("
            INSERT INTO Notificacion (id_usuario, mensaje)
            SELECT e.id_usuario, CONCAT('La reserva del ', DATE_FORMAT(r.fecha_evento, '%d/%m/%Y'), ' (', s.nombre, ') en la que estabas asignado ha sido cancelada.')
            FROM Asignacion_Empleado ae
            JOIN Empleado e ON e.id = ae.id_empleado
            JOIN Reserva r ON r.id = ae.id_reserva
            JOIN Servicio s ON s.id = r.id_servicio
            WHERE ae.id_reserva = :id
        ")->execute([':id' => $id]);
    }

    public function notificarConfirmacionCliente(int $id): void {
        $this->conexion->prepare("
            INSERT INTO Notificacion (id_usuario, mensaje)
            SELECT r.id_usuario, CONCAT('Tu reserva del ', DATE_FORMAT(r.fecha_evento, '%d/%m/%Y'), ' (', s.nombre, ') ha sido confirmada.')
            FROM Reserva r JOIN Servicio s ON s.id = r.id_servicio WHERE r.id = :id
        ")->execute([':id' => $id]);
    }

    public function editar(int $id, string $fechaEvento, string $horaInicio, string $horaFin, int $asistentes, ?string $observaciones): void {
        $this->conexion->prepare("
            UPDATE Reserva SET fecha_evento=:fecha_evento, hora_inicio=:hora_inicio, hora_fin=:hora_fin,
            num_asistentes=:asistentes, observaciones=:observaciones WHERE id=:id
        ")->execute([
            ':fecha_evento'  => $fechaEvento,
            ':hora_inicio'   => $horaInicio,
            ':hora_fin'      => $horaFin,
            ':asistentes'    => $asistentes,
            ':observaciones' => $observaciones,
            ':id'            => $id,
        ]);
    }

    public function gestionarCambio(int $id): void {
        $this->conexion->prepare("UPDATE Reserva SET cambio_solicitado = 0, motivo_cambio = NULL WHERE id = :id")
            ->execute([':id' => $id]);
    }

    public function crear(int $idUsuario, int $idServicio, string $fechaEvento, string $horaInicio, string $horaFin, int $asistentes, ?string $observaciones, int $idEmpresa): void {
        $this->conexion->prepare("
            INSERT INTO Reserva (fecha_reserva, fecha_evento, hora_inicio, hora_fin, num_asistentes, estado, observaciones, id_usuario, id_servicio, id_empresa)
            VALUES (CURDATE(), :fecha_evento, :hora_inicio, :hora_fin, :asistentes, 'pendiente', :observaciones, :id_usuario, :id_servicio, :id_empresa)
        ")->execute([
            ':fecha_evento'  => $fechaEvento,
            ':hora_inicio'   => $horaInicio,
            ':hora_fin'      => $horaFin,
            ':asistentes'    => $asistentes,
            ':observaciones' => $observaciones,
            ':id_usuario'    => $idUsuario,
            ':id_servicio'   => $idServicio,
            ':id_empresa'    => $idEmpresa,
        ]);
    }

    public function existe(int $id): bool {
        $stmt = $this->conexion->prepare("SELECT id FROM Reserva WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return (bool) $stmt->fetch();
    }

    public function obtenerAsignaciones(int $idReserva): array {
        $stmt = $this->conexion->prepare("
            SELECT a.id, a.rol_evento, a.estado, u.nombre, u.apellidos
            FROM Asignacion_Empleado a
            JOIN Empleado e ON e.id = a.id_empleado
            JOIN Usuario u ON u.id = e.id_usuario
            WHERE a.id_reserva = :id
            ORDER BY a.id ASC
        ");
        $stmt->execute([':id' => $idReserva]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeAsignacion(int $idReserva, int $idEmpleado): bool {
        $stmt = $this->conexion->prepare("SELECT id FROM Asignacion_Empleado WHERE id_reserva = :r AND id_empleado = :e");
        $stmt->execute([':r' => $idReserva, ':e' => $idEmpleado]);
        return (bool) $stmt->fetch();
    }

    public function asignarEmpleado(int $idReserva, int $idEmpleado, ?string $rol): void {
        $this->conexion->prepare("INSERT INTO Asignacion_Empleado (rol_evento, id_reserva, id_empleado) VALUES (:rol, :reserva, :empleado)")
            ->execute([':rol' => $rol, ':reserva' => $idReserva, ':empleado' => $idEmpleado]);
    }

    public function eliminarAsignacion(int $id): void {
        $this->conexion->prepare("DELETE FROM Asignacion_Empleado WHERE id = :id")->execute([':id' => $id]);
    }

    public function eventosCalendario(string $start, string $end): array {
        $stmt = $this->conexion->prepare("
            SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes, r.estado,
                   s.nombre AS servicio, u.nombre AS cliente, u.apellidos
            FROM Reserva r
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id = r.id_usuario
            WHERE r.fecha_evento BETWEEN :start AND :end
              AND r.estado IN ('confirmada','cancelada')
        ");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function empleadosPorReserva(int $idReserva): array {
        $stmt = $this->conexion->prepare("
            SELECT u.nombre, u.apellidos, a.rol_evento
            FROM Asignacion_Empleado a
            JOIN Empleado e ON e.id = a.id_empleado
            JOIN Usuario u ON u.id = e.id_usuario
            WHERE a.id_reserva = :id
        ");
        $stmt->execute([':id' => $idReserva]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function historialPorCliente(int $idUsuario): array {
        $stmt = $this->conexion->prepare("
            SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.estado, s.nombre AS servicio
            FROM Reserva r
            JOIN Servicio s ON s.id = r.id_servicio
            WHERE r.id_usuario = :id
            ORDER BY r.fecha_evento DESC
            LIMIT 5
        ");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function statsProximas(): int {
        return (int) $this->conexion->query("SELECT COUNT(*) FROM Reserva WHERE estado = 'confirmada' AND fecha_evento >= CURDATE()")->fetchColumn();
    }

    public function statsSinEmpleado(): int {
        return (int) $this->conexion->query("
            SELECT COUNT(*) FROM Reserva r
            WHERE r.estado = 'confirmada' AND r.fecha_evento >= CURDATE()
            AND NOT EXISTS (SELECT 1 FROM Asignacion_Empleado ae WHERE ae.id_reserva = r.id)
        ")->fetchColumn();
    }

    public function ingresosMes(): float {
        return (float) $this->conexion->query("
            SELECT COALESCE(SUM(monto), 0) FROM Pago_Cliente
            WHERE estado = 'confirmado' AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())
        ")->fetchColumn();
    }

    public function obtenerPorCliente(int $idUsuario): array {
        $stmt = $this->conexion->prepare("
            SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes,
                   r.estado, r.observaciones, r.motivo_cancelacion, s.nombre AS servicio,
                   (SELECT COALESCE(SUM(monto), 0) FROM Pago_Cliente WHERE id_reserva = r.id AND estado = 'confirmado') AS total_pagado
            FROM Reserva r
            JOIN Servicio s ON s.id = r.id_servicio
            WHERE r.id_usuario = :id
            ORDER BY r.fecha_evento DESC
        ");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerParaCliente(int $id, int $idUsuario): ?array {
        $stmt = $this->conexion->prepare("SELECT id, id_usuario, fecha_evento, estado FROM Reserva WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int)$row['id_usuario'] !== $idUsuario) return null;
        return $row;
    }

    public function cancelarPorCliente(int $id, ?string $motivo): void {
        $this->conexion->prepare("UPDATE Reserva SET estado = 'cancelada', motivo_cancelacion = :motivo WHERE id = :id")
            ->execute([':motivo' => $motivo, ':id' => $id]);
    }

    public function marcarCambioSolicitado(int $id, string $motivo): void {
        $this->conexion->prepare("UPDATE Reserva SET cambio_solicitado = 1, motivo_cambio = :motivo WHERE id = :id")
            ->execute([':motivo' => $motivo, ':id' => $id]);
    }

    public function proximasLista(int $limit = 5): array {
        $stmt = $this->conexion->prepare("
            SELECT r.id, r.fecha_evento, r.hora_inicio, r.hora_fin, r.num_asistentes, r.estado,
                   s.nombre AS servicio, u.nombre AS cliente, u.apellidos
            FROM Reserva r
            JOIN Servicio s ON s.id = r.id_servicio
            JOIN Usuario u ON u.id = r.id_usuario
            WHERE r.estado = 'confirmada' AND r.fecha_evento >= CURDATE()
            ORDER BY r.fecha_evento ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
