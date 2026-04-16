<?php


class Usuario {

    private PDO $conexion;

    public function __construct(PDO $conexion){
        $this->conexion = $conexion;
    }

    public function mostrarDatos() {

        $sql = "SELECT nif FROM Usuario";
        $stmt = $this->conexion->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $usuarios;
    }

    public function comprobarAcceso(string $nif, string $contra) {

        $sql = "SELECT * FROM Usuario WHERE nif = :nif";
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':nif', $nif);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) return null;

        $hash = (string) $usuario['password_hash'];
        if (!password_verify($contra, $hash)) return null;

        return $usuario;



    }

    public function existeNif(string $nif): bool {
        $sql = "SELECT 1 FROM Usuario WHERE nif = :nif LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':nif' => $nif]);
        return (bool) $stmt->fetchColumn();
    }

    public function existeEmail(string $email): bool {
        $sql = "SELECT 1 FROM Usuario WHERE email = :email LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function crearUsuario(string $nif, string $nombre, string $apellidos, string $movil1, string $movil2, string $email, string $password, string $direccion, string $comoNosConocio): int {
        $sql = "INSERT INTO Usuario (nif, nombre, apellidos, telefono, otro_telefono, email, password_hash, direccion, como_conoce, id_rol, id_empresa) VALUES (:nif, :nombre, :apellidos, :movil1, :movil2, :email, :password, :direccion, :comoNosConocio, :id_rol, :id_empresa)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':nif' => $nif,
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':movil1' => $movil1,
            ':movil2' => $movil2,
            ':email' => $email,
            ':password' => $password,
            ':direccion' => $direccion,
            ':comoNosConocio' => $comoNosConocio,
            ':id_rol' => 2, // Asignamos el rol de cliente por defecto
            ':id_empresa' => 1 // Asignamos la empresa por defecto (puedes cambiar esto según tu lógica de negocio)
        ]);

        return (int) $this->conexion->lastInsertId();
    }

}