<?php


class Usuario {

    private PDO $conexion;

    public function __construct(PDO $conexion){
        $this->conexion = $conexion;
    }

    public function mostrarDatos(): array {

        $sql = "SELECT nif FROM Usuario";
        $stmt = $this->conexion->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $usuarios;
    }

    public function comprobarAcceso(string $nif, string $contra): ?array {

        $sql = "SELECT * FROM Usuario WHERE nif = :nif AND activo = 1";
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

    public function emailEnUsoPoroOtro(string $email, int $idPropio): bool {
        $sql = "SELECT 1 FROM Usuario WHERE email = :email AND id != :id LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email, ':id' => $idPropio]);
        return (bool) $stmt->fetchColumn();
    }

    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT id, nif, nombre, apellidos, telefono, otro_telefono, email, direccion, como_conoce, id_rol FROM Usuario WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function actualizarPerfil(int $id, string $nombre, string $apellidos, string $telefono, string $otroTelefono, string $email, string $direccion): bool {
        $sql = "UPDATE Usuario SET nombre=:nombre, apellidos=:apellidos, telefono=:telefono, otro_telefono=:otro_telefono, email=:email, direccion=:direccion WHERE id=:id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id'            => $id,
            ':nombre'        => $nombre,
            ':apellidos'     => $apellidos,
            ':telefono'      => $telefono,
            ':otro_telefono' => $otroTelefono,
            ':email'         => $email,
            ':direccion'     => $direccion,
        ]);
    }

    public function cambiarPassword(int $id, string $actual, string $nueva): bool|string {
        $sql = "SELECT password_hash FROM Usuario WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($actual, (string) $row['password_hash'])) {
            return 'La contraseña actual no es correcta.';
        }
        $sql2 = "UPDATE Usuario SET password_hash=:hash WHERE id=:id";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute([':hash' => password_hash($nueva, PASSWORD_DEFAULT), ':id' => $id]);
        return true;
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