
<?php

class SolicitudEvento {

    private PDO $conexion;

    public function __construct(PDO $conexion){
        $this->conexion = $conexion;
    }

    public function crear(int $idUsuario, int $idEmpresa, array $datos): int {
        $sql = "INSERT INTO Solicitud_Evento
                (fecha_solicitud, fecha_evento, tipo_evento, nombre_protagonista,
                 num_participantes, sala, realidad_virtual, tarta, observaciones, id_usuario, id_empresa)
                VALUES
                (CURDATE(), :fecha_evento, :tipo_evento, :nombre_protagonista,
                 :num_participantes, :sala, :realidad_virtual, :tarta, :observaciones, :id_usuario, :id_empresa)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':fecha_evento'        => $datos['fecha_evento'],
            ':tipo_evento'         => $datos['tipo_evento'],
            ':nombre_protagonista' => $datos['nombre_protagonista'] ?? null,
            ':num_participantes'   => $datos['num_participantes'],
            ':sala'                => $datos['sala'] ?? null,
            ':realidad_virtual'    => $datos['realidad_virtual'] ?? null,
            ':tarta'               => $datos['tarta'] ?? null,
            ':observaciones'       => $datos['observaciones'] ?? null,
            ':id_usuario'          => $idUsuario,
            ':id_empresa'          => $idEmpresa,
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function solicitarRevision(int $id, int $idUsuario, string $motivo): bool {
        $sql  = "UPDATE Solicitud_Evento SET estado = 'pendiente', motivo_revision = :motivo,
                 importe_presupuesto = NULL, notas_presupuesto = NULL, fecha_limite_presupuesto = NULL
                 WHERE id = :id AND id_usuario = :id_usuario AND estado = 'rechazada'
                 AND (fecha_limite_presupuesto IS NULL OR fecha_limite_presupuesto >= CURDATE())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':motivo' => $motivo ?: null, ':id' => $id, ':id_usuario' => $idUsuario]);
        return $stmt->rowCount() > 0;
    }

    public function responderPresupuesto(int $id, int $idUsuario, string $estado): bool|string {
        $sql  = "SELECT estado, fecha_limite_presupuesto FROM Solicitud_Evento WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id, ':id_usuario' => $idUsuario]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || $row['estado'] !== 'presupuestada') return false;

        if ($row['fecha_limite_presupuesto'] && new \DateTime($row['fecha_limite_presupuesto']) < new \DateTime('today')) {
            return 'El plazo para responder este presupuesto ha vencido.';
        }

        $sql2 = "UPDATE Solicitud_Evento SET estado = :estado WHERE id = :id";
        $stmt2= $this->conexion->prepare($sql2);
        $stmt2->execute([':estado' => $estado, ':id' => $id]);
        return true;
    }

    public function obtenerPorUsuario(int $idUsuario): array {
        $this->conexion->prepare("
            UPDATE Solicitud_Evento
            SET estado = 'rechazada'
            WHERE id_usuario = :id_usuario
              AND estado = 'presupuestada'
              AND fecha_limite_presupuesto IS NOT NULL
              AND fecha_limite_presupuesto < CURDATE()
        ")->execute([':id_usuario' => $idUsuario]);

        $sql = "SELECT s.*, c.nombre AS tipo
                FROM Solicitud_Evento s
                JOIN Categoria c ON s.tipo_evento = c.id
                WHERE s.id_usuario = :id_usuario
                ORDER BY s.fecha_solicitud DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}