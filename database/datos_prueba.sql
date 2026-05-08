INSERT INTO solicitud_evento (fecha_solicitud, fecha_evento, tipo_evento, nombre_protagonista, num_participantes, sala, realidad_virtual, tarta, estado, id_usuario, id_empresa) VALUES
('2026-04-20', '2026-06-10', 3, 'Pablo', 15, 1, 0, 1, 'pendiente',     14, 1),
('2026-04-22', '2026-06-15', 2, NULL,     8, 0, 1, 0, 'pendiente',     15, 1),
('2026-04-25', '2026-07-01', 3, 'Emma',  12, 1, 1, 1, 'pendiente',     16, 1),
('2026-04-28', '2026-06-20', 1, NULL,    20, 0, 0, 0, 'pendiente',     17, 1),
('2026-04-10', '2026-05-25', 3, 'Mario', 10, 1, 0, 1, 'presupuestada', 18, 1),
('2026-04-12', '2026-05-30', 2, NULL,     6, 1, 1, 0, 'presupuestada', 14, 1),
('2026-03-15', '2026-05-10', 3, 'Carla', 18, 1, 1, 1, 'aceptada',      15, 1),
('2026-03-20', '2026-05-15', 4, NULL,    30, 0, 0, 0, 'aceptada',      16, 1),
('2026-03-01', '2026-04-15', 2, NULL,     5, 0, 0, 0, 'rechazada',     17, 1),
('2026-03-05', '2026-04-20', 1, NULL,    50, 0, 0, 0, 'rechazada',     18, 1);


INSERT INTO servicio (nombre, descripcion, precio_base, capacidad, id_categoria, id_empresa) VALUES                                                                                                                                            ('Escape Room Piratas', 'Juego temático para grupos', 150.00, 12, 2, 1),
('Cumpleaños Básico', 'Celebración con monitor', 200.00, 15, 3, 1);

INSERT INTO reserva (fecha_reserva, fecha_evento, hora_inicio, hora_fin, num_asistentes, estado, observaciones, id_usuario, id_servicio, id_empresa) VALUES
('2026-05-01', '2026-05-20', '17:00:00', '19:00:00', 12, 'confirmada', 'Cumpleaños de Pablo',   14, 2, 1),
('2026-05-02', '2026-05-28', '16:00:00', '18:00:00',  8, 'confirmada', NULL,                    15, 1, 1),
('2026-05-03', '2026-06-05', '18:00:00', '20:00:00', 10, 'pendiente',  'Confirmar menú',        16, 2, 1),
('2026-05-03', '2026-06-12', '17:30:00', '19:30:00', 15, 'pendiente',  NULL,                    17, 1, 1),
('2026-04-10', '2026-04-25', '16:00:00', '18:00:00',  6, 'confirmada', 'Cumpleaños de Emma',    18, 2, 1),
('2026-04-15', '2026-04-30', '17:00:00', '19:00:00', 20, 'cancelada',  'Cancelado por cliente', 14, 1, 1);




ALTER TABLE solicitud_evento MODIFY estado ENUM('pendiente','presupuestada','aceptada','reservada','rechazada') DEFAULT 'pendiente';