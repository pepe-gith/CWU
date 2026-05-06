
-- Primero se insertan estas porque se neceesita para otras tablas
INSERT INTO Empresa (nombre_empresa, cif, telefono, email, direccion)
VALUES ('Aventura Kids SL', 'B12345678', '600123123', 'info@aventurakids.com', 'Calle Mayor 10, Madrid');

INSERT INTO Rol (nombre_rol) VALUES
('admin'),
('empleado'),
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

INSERT INTO Empleado (precio_por_hora, especialidad, id_usuario, id_empresa)
VALUES
(15.00, 'Animación infantil', 2, 1);

INSERT INTO Reserva (fecha_reserva, fecha_evento, hora_inicio, hora_fin, num_asistentes, estado, observaciones, id_usuario, id_servicio, id_empresa)
VALUES
('2026-03-23', '2026-04-05', '17:00:00', '19:00:00', 10, 'confirmada', 'Cumpleaños de Ana', 3, 2, 1);

INSERT INTO Pago_Cliente (monto, fecha, metodo, estado, id_reserva)
VALUES
(200.00, '2026-03-23', 'tarjeta', 'pagado', 1);

INSERT INTO Asignacion_Empleado (rol_evento, id_reserva, id_empleado)
VALUES
('Monitor principal', 1, 1);


