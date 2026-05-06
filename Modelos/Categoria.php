<?php

class Categoria {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function mostrarCategorias(): array {
        $stmt = $this->conexion->query("SELECT id, nombre FROM Categoria ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarConConteo(): array {
        $stmt = $this->conexion->query("
            SELECT c.id, c.nombre,
                   (SELECT COUNT(*) 
                    FROM Solicitud_Evento se 
                    WHERE se.tipo_evento = c.id) AS num_solicitudes
            FROM Categoria c
            ORDER BY c.nombre ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeNombre(string $nombre, ?int $excluirId = null): bool {
        if ($excluirId) {
            $stmt = $this->conexion->prepare("SELECT id FROM Categoria WHERE nombre = :nombre AND id != :id");
            $stmt->execute([':nombre' => $nombre, ':id' => $excluirId]);
        } else {
            $stmt = $this->conexion->prepare("SELECT id FROM Categoria WHERE nombre = :nombre");
            $stmt->execute([':nombre' => $nombre]);
        }
        return (bool) $stmt->fetch();
    }

    public function crear(string $nombre): void {
        $this->conexion->prepare("INSERT INTO Categoria (nombre) VALUES (:nombre)")
            ->execute([':nombre' => $nombre]);
    }

    public function editar(int $id, string $nombre): void {
        $this->conexion->prepare("UPDATE Categoria SET nombre = :nombre WHERE id = :id")
            ->execute([':nombre' => $nombre, ':id' => $id]);
    }

    public function tieneSolicitudes(int $id): bool {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM Solicitud_Evento WHERE tipo_evento = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function eliminar(int $id): void {
        $this->conexion->prepare("DELETE FROM Categoria WHERE id = :id")->execute([':id' => $id]);
    }
}
