<?php

class Categoria {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function mostrarCategorias(): array {
        $stmt = $this->conexion->query("SELECT id, nombre, requiere_sala_vr, requiere_tarta FROM categoria ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarConConteo(): array {
        $stmt = $this->conexion->query("
            SELECT c.id, c.nombre, c.requiere_sala_vr, c.requiere_tarta,
                   (SELECT COUNT(*)
                    FROM solicitud_evento se
                    WHERE se.tipo_evento = c.id) AS num_solicitudes
            FROM categoria c
            ORDER BY c.nombre ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeNombre(string $nombre, ?int $excluirId = null): bool {
        if ($excluirId) {
            $stmt = $this->conexion->prepare("SELECT id FROM categoria WHERE nombre = :nombre AND id != :id");
            $stmt->execute([':nombre' => $nombre, ':id' => $excluirId]);
        } else {
            $stmt = $this->conexion->prepare("SELECT id FROM categoria WHERE nombre = :nombre");
            $stmt->execute([':nombre' => $nombre]);
        }
        return (bool) $stmt->fetch();
    }

    public function crear(string $nombre, int $requiereSalaVr, int $requiereTarta): void {
        $this->conexion->prepare("INSERT INTO categoria (nombre, requiere_sala_vr, requiere_tarta) VALUES (:nombre, :requiere_sala_vr, :requiere_tarta)")
            ->execute([':nombre' => $nombre, ':requiere_sala_vr' => $requiereSalaVr, ':requiere_tarta' => $requiereTarta]);
    }

    public function editar(int $id, string $nombre, int $requiereSalaVr, int $requiereTarta): void {
        $this->conexion->prepare("UPDATE categoria SET nombre = :nombre, requiere_sala_vr = :requiere_sala_vr, requiere_tarta = :requiere_tarta WHERE id = :id")
            ->execute([':nombre' => $nombre, ':requiere_sala_vr' => $requiereSalaVr, ':requiere_tarta' => $requiereTarta, ':id' => $id]);
    }

    public function tieneSolicitudes(int $id): bool {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM solicitud_evento WHERE tipo_evento = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function eliminar(int $id): void {
        $this->conexion->prepare("DELETE FROM categoria WHERE id = :id")->execute([':id' => $id]);
    }
}
