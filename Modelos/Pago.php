<?php

class Pago {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerPorReserva(int $idReserva): array {
        $stmt = $this->conexion->prepare("
            SELECT id, monto, fecha, metodo, estado, referencia
            FROM pago_cliente
            WHERE id_reserva = :id
            ORDER BY fecha DESC, id DESC
        ");
        $stmt->execute([':id' => $idReserva]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalPagado(int $idReserva): float {
        $stmt = $this->conexion->prepare("SELECT COALESCE(SUM(monto), 0) FROM pago_cliente WHERE id_reserva = :id AND estado = 'confirmado'");
        $stmt->execute([':id' => $idReserva]);
        return (float) $stmt->fetchColumn();
    }

    public function crear(int $idReserva, float $monto, string $metodo): void {
        $this->conexion->prepare("
            INSERT INTO pago_cliente (monto, fecha, metodo, estado, id_reserva)
            VALUES (:monto, CURDATE(), :metodo, 'confirmado', :id)
        ")->execute([':monto' => $monto, ':metodo' => $metodo, ':id' => $idReserva]);
    }

    public function crearPorCliente(int $idReserva, float $monto, string $metodo, ?string $referencia): void {
        $this->conexion->prepare("
            INSERT INTO pago_cliente (monto, fecha, metodo, estado, referencia, id_reserva)
            VALUES (:monto, CURDATE(), :metodo, 'pendiente', :ref, :id)
        ")->execute([':monto' => $monto, ':metodo' => $metodo, ':ref' => $referencia, ':id' => $idReserva]);
    }

    public function confirmar(int $id): void {
        $this->conexion->prepare("UPDATE pago_cliente SET estado = 'confirmado' WHERE id = :id")->execute([':id' => $id]);
    }

    public function rechazar(int $id): void {
        $this->conexion->prepare("UPDATE pago_cliente SET estado = 'rechazado' WHERE id = :id")->execute([':id' => $id]);
    }

    public function eliminar(int $id): void {
        $this->conexion->prepare("DELETE FROM pago_cliente WHERE id = :id")->execute([':id' => $id]);
    }

    public function notificarAdmins(int $idReserva): void {
        $this->conexion->prepare("
            INSERT INTO notificacion (id_usuario, mensaje)
            SELECT u.id, CONCAT('Pago pendiente de verificación para la reserva #', :id, '. Revísalo en el panel de reservas.')
            FROM usuario u WHERE u.id_rol = 1
        ")->execute([':id' => $idReserva]);
    }

    public function notificarCliente(int $idPago, string $accion): void {
        $sufijo = $accion === 'confirmado'
            ? 'confirmado correctamente.'
            : 'rechazado. Contacta con nosotros si tienes dudas.';
        $this->conexion->prepare("
            INSERT INTO notificacion (id_usuario, mensaje)
            SELECT r.id_usuario, CONCAT(
                'Tu pago de ', p.monto, ' € (', p.metodo, ') para la reserva del ',
                DATE_FORMAT(r.fecha_evento, '%d/%m/%Y'), ' ha sido ', :sufijo
            )
            FROM pago_cliente p
            JOIN reserva r ON r.id = p.id_reserva
            WHERE p.id = :id
        ")->execute([':sufijo' => $sufijo, ':id' => $idPago]);
    }
}
