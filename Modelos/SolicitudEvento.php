
<?php

class SolicitudEvento {

    private PDO $conexion;

    public function __construct(PDO $conexion){
        $this->conexion = $conexion;
    }

    public function crear(int $idUsuario, int $idEmpresa, array $datos): int {
        $sql = "INSERT INTO Solicitud_Evento
                (fecha_solicitud, fecha_evento, tipo_evento, nombre_protagonista,
                 num_participantes, sala, realidad_virtual, tarta, id_usuario, id_empresa)
                VALUES
                (CURDATE(), :fecha_evento, :tipo_evento, :nombre_protagonista,
                 :num_participantes, :sala, :realidad_virtual, :tarta, :id_usuario, :id_empresa)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':fecha_evento'        => $datos['fecha_evento'],
            ':tipo_evento'         => $datos['tipo_evento'],
            ':nombre_protagonista' => $datos['nombre_protagonista'] ?? null,
            ':num_participantes'   => $datos['num_participantes'],
            ':sala'                => $datos['sala'] ?? null,
            ':realidad_virtual'    => $datos['realidad_virtual'] ?? null,
            ':tarta'               => $datos['tarta'] ?? null,
            ':id_usuario'          => $idUsuario,
            ':id_empresa'          => $idEmpresa,
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function obtenerPorUsuario(int $idUsuario): array {
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