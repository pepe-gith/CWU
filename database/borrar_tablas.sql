SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Ajusta el nombre de BD si procede
USE gestion_eventos;

-- Desactiva comprobaciones de claves foraneas para permitir borrado en bloque
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Compra_Suministro;
DROP TABLE IF EXISTS Asignacion_Empleado;
DROP TABLE IF EXISTS Pago_Cliente;
DROP TABLE IF EXISTS Reserva;
DROP TABLE IF EXISTS Producto;
DROP TABLE IF EXISTS Empleado;
DROP TABLE IF EXISTS Servicio;
DROP TABLE IF EXISTS Proveedor;
DROP TABLE IF EXISTS Usuario;
DROP TABLE IF EXISTS Categoria;
DROP TABLE IF EXISTS Rol;
DROP TABLE IF EXISTS Empresa;

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;
