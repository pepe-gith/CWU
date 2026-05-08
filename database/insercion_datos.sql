
-- Primero se insertan estas porque se neceesita para otras tablas
INSERT INTO empresa (nombre_empresa, cif, telefono, email, direccion)
VALUES ('Aventura Kids SL', 'B12345678', '600123123', 'info@aventurakids.com', 'Calle Mayor 10, Madrid');

INSERT INTO rol (nombre_rol) VALUES
('admin'),
('empleado'),
('cliente');

INSERT INTO categoria (nombre, requiere_sala_vr, requiere_tarta) VALUES
('Extraescolar', 0, 0),
('Escape Room',  1, 0),
('Cumpleaños',   0, 1),
('Eventos',      0, 0);

---

-- Contraseñas de prueba (bcrypt PASSWORD_DEFAULT):
--   admin@aventurakids.com    → admin1234
--   monitor1@aventurakids.com → empleado1234
--   cliente1@gmail.com        → cliente1234
INSERT INTO usuario (nif, nombre, apellidos, telefono, otro_telefono, email, password_hash, direccion, como_conoce, id_rol, id_empresa)
VALUES
('12345678Z', 'Laura', 'Gómez', '600111111', NULL, 'admin@aventurakids.com',    '$2y$10$pDZHK9SHkx37i9mhbcLtpecvG.GiIw6I2spOijJpa35.774LUIbzC', 'Madrid', 'web',       1, 1),
('87654321X', 'Carlos', 'Pérez', '600222222', NULL, 'monitor1@aventurakids.com', '$2y$10$m0yBaeZh6gXtZTAHKfhb.uozfZWdpYJiPblSPsFNKkwsJXvdx6dHC', 'Madrid', 'instagram', 2, 1),
('11223344B', 'Marta', 'López', '600333333', NULL, 'cliente1@gmail.com',         '$2y$10$Ya4egCEm3K0s1b/CLmk4feRm5Y0YitZCQx2dwBCglRoLf9u8RxV2a', 'Madrid', 'amigo',     3, 1);

INSERT INTO servicio (nombre, descripcion, precio_base, capacidad, id_categoria, id_empresa)
VALUES
('Escape Room Piratas', 'Juego temático para grupos infantiles', 150.00, 12, 2, 1),
('Cumpleaños Básico', 'Celebración de cumpleaños con monitor', 200.00, 15, 3, 1);

INSERT INTO empleado (precio_por_hora, especialidad, id_usuario, id_empresa)
VALUES
(15.00, 'Animación infantil', 2, 1);

INSERT INTO reserva (fecha_reserva, fecha_evento, hora_inicio, hora_fin, num_asistentes, estado, observaciones, id_usuario, id_servicio, id_empresa)
VALUES
('2026-03-23', '2026-04-05', '17:00:00', '19:00:00', 10, 'confirmada', 'Cumpleaños de Ana', 3, 2, 1);

INSERT INTO pago_cliente (monto, fecha, metodo, estado, referencia, id_reserva)
VALUES
(200.00, '2026-03-23', 'tarjeta', 'confirmado', NULL, 1);

INSERT INTO asignacion_empleado (rol_evento, id_reserva, id_empleado)
VALUES
('Monitor principal', 1, 1);


