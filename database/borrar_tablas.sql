SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Ajusta el nombre de BD si procede
USE gestion_eventos;

-- Desactiva comprobaciones de claves foraneas para permitir borrado en bloque
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notificacion;
DROP TABLE IF EXISTS asignacion_empleado;
DROP TABLE IF EXISTS pago_cliente;
DROP TABLE IF EXISTS reserva;
DROP TABLE IF EXISTS solicitud_evento;
DROP TABLE IF EXISTS empleado;
DROP TABLE IF EXISTS servicio;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS rol;
DROP TABLE IF EXISTS empresa;

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;
