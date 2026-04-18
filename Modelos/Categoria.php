<?php

class Categoria {

    private PDO $conexion;

    public function __construct(PDO $conexion){
        $this->conexion = $conexion;
    }


    public function mostrarCategorias(): array {
        $stmt = $this->conexion->query("SELECT id, nombre FROM Categoria ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}