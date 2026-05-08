<?php

class Usuario {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function comprobarAcceso(string $nif, string $contra): ?array {
        $stmt = $this->conexion->prepare("SELECT * FROM usuario WHERE nif = :nif AND activo = 1");
        $stmt->execute([':nif' => $nif]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario || !password_verify($contra, (string) $usuario['password_hash'])) return null;
        return $usuario;
    }

    public function existeNif(string $nif): bool {
        $stmt = $this->conexion->prepare("SELECT 1 FROM usuario WHERE nif = :nif LIMIT 1");
        $stmt->execute([':nif' => $nif]);
        return (bool) $stmt->fetchColumn();
    }

    public function existeEmail(string $email): bool {
        $stmt = $this->conexion->prepare("SELECT 1 FROM usuario WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function emailEnUsoPoroOtro(string $email, int $idPropio): bool {
        $stmt = $this->conexion->prepare("SELECT 1 FROM usuario WHERE email = :email AND id != :id LIMIT 1");
        $stmt->execute([':email' => $email, ':id' => $idPropio]);
        return (bool) $stmt->fetchColumn();
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->conexion->prepare("SELECT id, nif, nombre, apellidos, telefono, otro_telefono, email, direccion, como_conoce, id_rol FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function actualizarPerfil(int $id, string $nombre, string $apellidos, string $telefono, string $otroTelefono, string $email, string $direccion): bool {
        $stmt = $this->conexion->prepare("UPDATE usuario SET nombre=:nombre, apellidos=:apellidos, telefono=:telefono, otro_telefono=:otro_telefono, email=:email, direccion=:direccion WHERE id=:id");
        return $stmt->execute([':id' => $id, ':nombre' => $nombre, ':apellidos' => $apellidos, ':telefono' => $telefono, ':otro_telefono' => $otroTelefono, ':email' => $email, ':direccion' => $direccion]);
    }

    public function cambiarPassword(int $id, string $actual, string $nueva): bool|string {
        $stmt = $this->conexion->prepare("SELECT password_hash FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($actual, (string) $row['password_hash'])) return 'La contraseña actual no es correcta.';
        $this->conexion->prepare("UPDATE usuario SET password_hash=:hash WHERE id=:id")
            ->execute([':hash' => password_hash($nueva, PASSWORD_DEFAULT), ':id' => $id]);
        return true;
    }

    public function crearUsuario(string $nif, string $nombre, string $apellidos, string $movil1, string $movil2, string $email, string $password, string $direccion, string $comoNosConocio): int {
        $stmt = $this->conexion->prepare("INSERT INTO usuario (nif, nombre, apellidos, telefono, otro_telefono, email, password_hash, direccion, como_conoce, id_rol, id_empresa) VALUES (:nif, :nombre, :apellidos, :movil1, :movil2, :email, :password, :direccion, :comoNosConocio, 3, 1)");
        $stmt->execute([':nif' => $nif, ':nombre' => $nombre, ':apellidos' => $apellidos, ':movil1' => $movil1, ':movil2' => $movil2, ':email' => $email, ':password' => $password, ':direccion' => $direccion, ':comoNosConocio' => $comoNosConocio]);
        return (int) $this->conexion->lastInsertId();
    }

    public function listarAdmin(?int $rol = null, string $buscar = ''): array {
        $where  = [];
        $params = [];
        if ($rol) { $where[] = "u.id_rol = :rol"; $params[':rol'] = $rol; }
        if ($buscar) {
            $where[] = "(u.nombre LIKE :b OR u.apellidos LIKE :b2 OR u.nif LIKE :b3 OR u.email LIKE :b4)";
            $params[':b'] = $params[':b2'] = $params[':b3'] = $params[':b4'] = "%$buscar%";
        }
        $sql = "SELECT u.id, u.nif, u.nombre, u.apellidos, u.email, u.telefono, u.id_rol, u.activo FROM usuario u"
             . ($where ? " WHERE " . implode(" AND ", $where) : "")
             . " ORDER BY u.nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRol(int $id): int {
        $stmt = $this->conexion->prepare("SELECT id_rol FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    public function getRoles(): array {
        return $this->conexion->query("SELECT id, nombre_rol FROM rol ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarRol(int $id, int $rol): void {
        $this->conexion->prepare("UPDATE usuario SET id_rol = :rol WHERE id = :id")
            ->execute([':rol' => $rol, ':id' => $id]);
    }

    public function getActivo(int $id): bool {
        $stmt = $this->conexion->prepare("SELECT activo FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    public function toggleActivo(int $id): bool {
        $this->conexion->prepare("UPDATE usuario SET activo = NOT activo WHERE id = :id")->execute([':id' => $id]);
        return $this->getActivo($id);
    }

    public function desactivar(int $id): void {
        $this->conexion->prepare("UPDATE usuario SET activo = 0 WHERE id = :id")->execute([':id' => $id]);
    }

    public function totalClientes(): int {
        return (int) $this->conexion->query("SELECT COUNT(*) FROM usuario WHERE id_rol = 3")->fetchColumn();
    }

    public function crearAdmin(string $nif, string $nombre, ?string $apellidos, ?string $telefono, string $email, string $hash, int $idRol, int $idEmpresa): int {
        $stmt = $this->conexion->prepare("INSERT INTO usuario (nif, nombre, apellidos, telefono, email, password_hash, id_rol, id_empresa, activo) VALUES (:nif, :nombre, :apellidos, :telefono, :email, :hash, :rol, :empresa, 1)");
        $stmt->execute([':nif' => $nif, ':nombre' => $nombre, ':apellidos' => $apellidos, ':telefono' => $telefono, ':email' => $email, ':hash' => $hash, ':rol' => $idRol, ':empresa' => $idEmpresa]);
        return (int) $this->conexion->lastInsertId();
    }

    public function tieneActividadComoCliente(int $id): array {
        $stmtSol = $this->conexion->prepare("SELECT COUNT(*) FROM solicitud_evento WHERE id_usuario = :id AND estado IN ('pendiente','presupuestada','aceptada')");
        $stmtSol->execute([':id' => $id]);

        $stmtRes = $this->conexion->prepare("SELECT COUNT(*) FROM reserva WHERE id_usuario = :id AND estado = 'confirmada' AND fecha_evento >= CURDATE()");
        $stmtRes->execute([':id' => $id]);

        return ['solicitudes' => (int) $stmtSol->fetchColumn(), 'reservas' => (int) $stmtRes->fetchColumn()];
    }

    public function editarAdmin(int $id, string $nombre, string $apellidos, string $email, string $telefono): void {
        $this->conexion->prepare("UPDATE usuario SET nombre=:nombre, apellidos=:apellidos, email=:email, telefono=:telefono WHERE id=:id")
            ->execute([':nombre' => $nombre, ':apellidos' => $apellidos, ':email' => $email, ':telefono' => $telefono, ':id' => $id]);
    }
}
