<?php

class SolicitudEvento {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function crear(int $idUsuario, int $idEmpresa, array $datos): int {
        $stmt = $this->conexion->prepare("
            INSERT INTO solicitud_evento
                (fecha_solicitud, fecha_evento, tipo_evento, nombre_protagonista,
                 num_participantes, sala, realidad_virtual, tarta, observaciones, id_usuario, id_empresa)
            VALUES
                (CURDATE(), :fecha_evento, :tipo_evento, :nombre_protagonista,
                 :num_participantes, :sala, :realidad_virtual, :tarta, :observaciones, :id_usuario, :id_empresa)
        ");
        $stmt->execute([
            ':fecha_evento' => $datos['fecha_evento'],
            ':tipo_evento' => $datos['tipo_evento'],
            ':nombre_protagonista' => $datos['nombre_protagonista'] ?? null,
            ':num_participantes' => $datos['num_participantes'],
            ':sala' => $datos['sala'] ?? null,
            ':realidad_virtual' => $datos['realidad_virtual'] ?? null,
            ':tarta' => $datos['tarta'] ?? null,
            ':observaciones' => $datos['observaciones'] ?? null,
            ':id_usuario' => $idUsuario,
            ':id_empresa' => $idEmpresa,
        ]);
        return (int) $this->conexion->lastInsertId();
    }

    public function solicitarRevision(int $id, int $idUsuario, string $motivo): bool {
        $stmt = $this->conexion->prepare("
            UPDATE solicitud_evento SET estado = 'pendiente', motivo_revision = :motivo,
            importe_presupuesto = NULL, notas_presupuesto = NULL, fecha_limite_presupuesto = NULL
            WHERE id = :id AND id_usuario = :id_usuario AND estado = 'rechazada'
            AND (fecha_limite_presupuesto IS NULL OR fecha_limite_presupuesto >= CURDATE())
        ");
        $stmt->execute([':motivo' => $motivo ?: null, ':id' => $id, ':id_usuario' => $idUsuario]);
        return $stmt->rowCount() > 0;
    }

    public function responderPresupuesto(int $id, int $idUsuario, string $estado): bool|string {
        $stmt = $this->conexion->prepare("SELECT estado, fecha_limite_presupuesto FROM solicitud_evento WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->execute([':id' => $id, ':id_usuario' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || $row['estado'] !== 'presupuestada') return false;

        if ($row['fecha_limite_presupuesto'] && new \DateTime($row['fecha_limite_presupuesto']) < new \DateTime('today')) {
            return 'El plazo para responder este presupuesto ha vencido.';
        }

        $this->conexion->prepare("UPDATE solicitud_evento SET estado = :estado WHERE id = :id")
            ->execute([':estado' => $estado, ':id' => $id]);
        return true;
    }

    public function obtenerPorUsuario(int $idUsuario): array {
        $this->conexion->prepare("
            UPDATE solicitud_evento SET estado = 'rechazada'
            WHERE id_usuario = :id_usuario AND estado = 'presupuestada'
              AND fecha_limite_presupuesto IS NOT NULL AND fecha_limite_presupuesto < CURDATE()
        ")->execute([':id_usuario' => $idUsuario]);

        $stmt = $this->conexion->prepare("
            SELECT s.*, c.nombre AS tipo FROM solicitud_evento s
            JOIN categoria c ON s.tipo_evento = c.id
            WHERE s.id_usuario = :id_usuario ORDER BY s.fecha_solicitud DESC
        ");
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAdmin(?string $estado = null): array {
        $sql = "
            SELECT se.id, se.fecha_solicitud, se.fecha_evento, se.num_participantes,
                   se.sala, se.realidad_virtual, se.tarta, se.nombre_protagonista, se.estado,
                   se.id_usuario, se.motivo_revision,
                   c.nombre AS tipo, u.nombre AS cliente, u.apellidos, u.telefono, u.email
            FROM solicitud_evento se
            JOIN categoria c ON c.id = se.tipo_evento
            JOIN usuario u ON u.id = se.id_usuario
        ";
        $params = [];
        if ($estado) { $sql .= " WHERE se.estado = :estado"; $params[':estado'] = $estado; }
        $sql .= " ORDER BY se.fecha_solicitud DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstadoAdmin(int $id, string $estado, float $importe = 0, ?string $notas = null, ?string $fechaLimite = null): void {
        if ($estado === 'presupuestada') {
            $this->conexion->prepare("
                UPDATE solicitud_evento SET estado = :estado, importe_presupuesto = :importe,
                notas_presupuesto = :notas, fecha_limite_presupuesto = :fecha_limite WHERE id = :id
            ")->execute([':estado' => $estado, ':importe' => $importe, ':notas' => $notas, ':fecha_limite' => $fechaLimite, ':id' => $id]);
        } else {
            $this->conexion->prepare("UPDATE solicitud_evento SET estado = :estado WHERE id = :id")
                ->execute([':estado' => $estado, ':id' => $id]);
        }
    }

    public function notificarCliente(int $id, string $estado): void {
        $textos = [
            'presupuestada' => "Tienes un presupuesto listo para tu solicitud del %s (%s). Revísalo en Mis solicitudes.",
            'aceptada' => "Tu solicitud del %s (%s) ha sido aceptada. En breve recibirás los detalles de tu reserva.",
            'rechazada' => "Tu solicitud del %s (%s) ha sido rechazada.",
        ];
        if (!isset($textos[$estado])) return;

        $stmt = $this->conexion->prepare("SELECT se.id_usuario, se.fecha_evento, c.nombre AS tipo FROM solicitud_evento se JOIN categoria c ON c.id = se.tipo_evento WHERE se.id = :id");
        $stmt->execute([':id' => $id]);
        $sol = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sol) return;

        $mensaje = sprintf($textos[$estado], date('d/m/Y', strtotime($sol['fecha_evento'])), $sol['tipo']);
        $this->conexion->prepare("INSERT INTO notificacion (id_usuario, mensaje) VALUES (:uid, :msg)")
            ->execute([':uid' => $sol['id_usuario'], ':msg' => $mensaje]);
    }

    public function marcarComoReservada(int $id): void {
        $this->conexion->prepare("UPDATE solicitud_evento SET estado = 'reservada' WHERE id = :id")
            ->execute([':id' => $id]);
    }

    public function historialPorCliente(int $idUsuario): array {
        $stmt = $this->conexion->prepare("
            SELECT se.id, se.fecha_evento, se.estado, c.nombre AS tipo
            FROM solicitud_evento se
            JOIN categoria c ON c.id = se.tipo_evento
            WHERE se.id_usuario = :id
            ORDER BY se.fecha_evento DESC
            LIMIT 5
        ");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function solicitudesCalendario(string $start, string $end): array {
        $stmt = $this->conexion->prepare("
            SELECT se.id, se.fecha_evento, se.num_participantes,
                   c.nombre AS tipo_evento, u.nombre AS cliente, u.apellidos
            FROM solicitud_evento se
            JOIN categoria c ON c.id = se.tipo_evento
            JOIN usuario u ON u.id = se.id_usuario
            WHERE se.fecha_evento BETWEEN :start AND :end AND se.estado = 'pendiente'
        ");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPendientes(): int {
        return (int) $this->conexion->query("SELECT COUNT(*) FROM solicitud_evento WHERE estado = 'pendiente'")->fetchColumn();
    }

    public function ultimasPendientes(int $limit = 5): array {
        $stmt = $this->conexion->prepare("
            SELECT se.id, se.fecha_solicitud, se.fecha_evento, se.num_participantes, se.estado,
                   c.nombre AS tipo, u.nombre AS cliente, u.apellidos
            FROM solicitud_evento se
            JOIN categoria c ON c.id = se.tipo_evento
            JOIN usuario u ON u.id = se.id_usuario
            WHERE se.estado = 'pendiente'
            ORDER BY se.fecha_solicitud DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
