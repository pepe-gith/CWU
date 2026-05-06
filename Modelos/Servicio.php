<?php

class Servicio {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function listar(): array {
        $stmt = $this->conexion->query("
            SELECT s.id, s.nombre, s.descripcion, s.precio_base, s.capacidad,
                   s.id_categoria, c.nombre AS categoria,
                   (SELECT COUNT(*) FROM Reserva r WHERE r.id_servicio = s.id) AS num_reservas
            FROM Servicio s
            JOIN Categoria c ON c.id = s.id_categoria
            ORDER BY c.nombre, s.nombre
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre, ?string $descripcion, float $precio, int $capacidad, int $idCategoria, int $idEmpresa): void {
        $this->conexion->prepare("
            INSERT INTO Servicio (nombre, descripcion, precio_base, capacidad, id_categoria, id_empresa)
            VALUES (:nombre, :descripcion, :precio, :capacidad, :id_cat, :id_empresa)
        ")->execute([
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':precio'      => $precio,
            ':capacidad'   => $capacidad,
            ':id_cat'      => $idCategoria,
            ':id_empresa'  => $idEmpresa,
        ]);
    }

    public function editar(int $id, string $nombre, ?string $descripcion, float $precio, int $capacidad, int $idCategoria): void {
        $this->conexion->prepare("
            UPDATE Servicio
            SET nombre = :nombre, descripcion = :descripcion, precio_base = :precio,
                capacidad = :capacidad, id_categoria = :id_cat
            WHERE id = :id
        ")->execute([
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':precio'      => $precio,
            ':capacidad'   => $capacidad,
            ':id_cat'      => $idCategoria,
            ':id'          => $id,
        ]);
    }

    public function tieneReservas(int $id): bool {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM Reserva WHERE id_servicio = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function eliminar(int $id): void {
        $this->conexion->prepare("DELETE FROM Servicio WHERE id = :id")->execute([':id' => $id]);
    }
}
