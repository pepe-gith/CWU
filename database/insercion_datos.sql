
-- Primero se insertan estas porque se neceesita para otras tablas
INSERT INTO Empresa (nombre_empresa, cif, telefono, email, direccion)
VALUES ('Aventura Kids SL', 'B12345678', '600123123', 'info@aventurakids.com', 'Calle Mayor 10, Madrid');

INSERT INTO Rol (nombre_rol) VALUES
('admin'),
('monitor'),
('cliente');

INSERT INTO Categoria (nombre) VALUES
('Extraescolar'),
('Escape Room'),
('Cumpleaños'),
('Eventos');

---

INSERT INTO Usuario (nif, nombre, apellidos, telefono, otro_telefono, email, password_hash, direccion, como_conoce, id_rol, id_empresa)
VALUES
('12345678A', 'Laura', 'Gómez', '600111111', NULL, 'admin@aventurakids.com', 'hash_admin', 'Madrid', 'web', 1, 1),
('87654321B', 'Carlos', 'Pérez', '600222222', NULL, 'monitor1@aventurakids.com', 'hash_monitor', 'Madrid', 'instagram', 2, 1),
('11223344C', 'Marta', 'López', '600333333', NULL, 'cliente1@gmail.com', 'hash_cliente', 'Madrid', 'amigo', 3, 1);

INSERT INTO Servicio (nombre, descripcion, precio_base, capacidad, id_categoria, id_empresa)
VALUES
('Escape Room Piratas', 'Juego temático para grupos infantiles', 150.00, 12, 2, 1),
('Cumpleaños Básico', 'Celebración de cumpleaños con monitor', 200.00, 15, 3, 1);

INSERT INTO Proveedor (nombre_empresa, cif_nif, telefono, tipo_suministro, id_empresa)
VALUES
('FiestasPro', 'A99887766', '911223344', 'Decoración y material', 1);

INSERT INTO Producto (nombre, cantidad_stock, stock_min, precio_compra, id_proveedor, id_empresa)
VALUES
('Globos de colores', 100, 20, 0.15, 1, 1),
('Pintura facial', 30, 5, 3.50, 1, 1);

INSERT INTO Empleado (seguridad_social, cuenta_bancaria, precio_por_hora, especialidad, id_usuario, id_empresa)
VALUES
('SS123456789', 'ES7620770024003102575766', 15.00, 'Animación infantil', 2, 1);

INSERT INTO Reserva (fecha_reserva, fecha_evento, hora_inicio, hora_fin, num_asistentes, estado, observaciones, id_usuario, id_servicio, id_empresa)
VALUES
('2026-03-23', '2026-04-05', '17:00:00', '19:00:00', 10, 'confirmada', 'Cumpleaños de Ana', 3, 2, 1);

INSERT INTO Pago_Cliente (monto, fecha, metodo, estado, id_reserva)
VALUES
(200.00, '2026-03-23', 'tarjeta', 'pagado', 1);

INSERT INTO Asignacion_Monitor (hora_inicio, hora_fin, rol_evento, observaciones, id_reserva, id_empleado)
VALUES
('16:30:00', '19:30:00', 'Monitor principal', 'Llegar 30 min antes', 1, 1);

INSERT INTO Compra_Suministro (cantidad_comprada, fecha, importe_total, estado_pago_proveedor, id_producto, id_empresa)
VALUES
(50, '2026-03-20', 7.50, 'pagado', 1, 1);

