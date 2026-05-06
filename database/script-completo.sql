SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================
-- 1. CREAR BASE DE DATOS
-- =====================================
CREATE DATABASE IF NOT EXISTS gestion_eventos;
USE gestion_eventos;

-- =====================================
-- 2. TABLAS MAESTRAS
-- =====================================

CREATE TABLE Empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    cif VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(100),
    email VARCHAR(100),
    direccion VARCHAR(255)
);

CREATE TABLE Rol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE Categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- 3. DATOS BASE
-- =====================================

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

-- =====================================
-- 4. TABLAS PRINCIPALES
-- =====================================

CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nif VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150),
    telefono VARCHAR(20),
    otro_telefono VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
    como_conoce VARCHAR(100),
    activo TINYINT(1) NOT NULL DEFAULT 1,
    id_rol INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Rol(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

CREATE TABLE Servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2) NOT NULL,
    capacidad INT NOT NULL,
    id_categoria INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

CREATE TABLE Empleado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    precio_por_hora DECIMAL(10,2) NOT NULL,
    especialidad VARCHAR(100),
    id_usuario INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

-- =====================================
-- 5. TABLAS DEL NÚCLEO
-- =====================================

CREATE TABLE Solicitud_Evento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_solicitud DATE NOT NULL,
    fecha_evento DATE NOT NULL,
    tipo_evento INT NOT NULL,
    nombre_protagonista VARCHAR(150),
    num_participantes INT NOT NULL,
    sala TINYINT,
    realidad_virtual TINYINT,
    tarta TINYINT,
    observaciones TEXT,
    estado ENUM('pendiente','presupuestada','aceptada','reservada','rechazada') DEFAULT 'pendiente',
    importe_presupuesto DECIMAL(10,2),
    notas_presupuesto TEXT,
    fecha_limite_presupuesto DATE,
    motivo_revision TEXT,
    id_usuario INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (tipo_evento) REFERENCES Categoria(id),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

CREATE TABLE Reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_reserva DATE NOT NULL,
    fecha_evento DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    num_asistentes INT NOT NULL,
    estado ENUM('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
    observaciones TEXT,
    motivo_cancelacion TEXT,
    cambio_solicitado TINYINT(1) NOT NULL DEFAULT 0,
    motivo_cambio TEXT,
    id_usuario INT NOT NULL,
    id_servicio INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
    FOREIGN KEY (id_servicio) REFERENCES Servicio(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

CREATE TABLE Pago_Cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    metodo ENUM('tarjeta','efectivo','transferencia') NOT NULL,
    estado VARCHAR(50) NOT NULL,
    id_reserva INT NOT NULL,
    FOREIGN KEY (id_reserva) REFERENCES Reserva(id)
);

CREATE TABLE Asignacion_Empleado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_evento VARCHAR(100),
    estado ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
    id_reserva INT NOT NULL,
    id_empleado INT NOT NULL,
    FOREIGN KEY (id_reserva) REFERENCES Reserva(id),
    FOREIGN KEY (id_empleado) REFERENCES Empleado(id)
);

CREATE TABLE Notificacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    mensaje VARCHAR(500) NOT NULL,
    leida TINYINT(1) NOT NULL DEFAULT 0,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) ON DELETE CASCADE
);

-- =====================================
-- 6. DATOS DE PRUEBA
-- =====================================

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

COMMIT;
