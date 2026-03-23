SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Creación de base de datos
--CREATE DATABASE IF NOT EXISTS cwubd;
--USE cwubd;
CREATE DATABASE IF NOT EXISTS gestion_eventos;
USE gestion_eventos;

--- CREACION DE TABLAS ---
--- 1. Creacion de tabla de Empresa, Rol, Categoria
-- Despues de crear estas 3 tablas se inserta datos porque se necesita para las demás.
CREATE TABLE Empresa (
	id int AUTO_INCREMENT PRIMARY KEY,
	nombre_empresa varchar(150) NOT NULL,
	cif varchar(20) NOT NULL UNIQUE,
	telefono varchar(100),
    email VARCHAR(100),
	direccion varchar(255)
);

CREATE TABLE Rol (
	id int AUTO_INCREMENT PRIMARY KEY,
	nombre_rol varchar(20) NOT NULL UNIQUE
);

CREATE TABLE Categoria (
	id int AUTO_INCREMENT PRIMARY KEY,
	nombre varchar(50) NOT NULL UNIQUE
);

--- 2. TABLAS PRINCIPALES
CREATE TABLE Usuario (
    id int AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150),
    telefono VARCHAR(20),
    otro_telefono VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
    como_conoce VARCHAR(100),
    id_rol INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Rol(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

CREATE TABLE Proveedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    cif_nif VARCHAR(20),
    telefono VARCHAR(20),
    tipo_suministro VARCHAR(100),
    id_empresa INT NOT NULL,
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
    seguridad_social VARCHAR(30),
    cuenta_bancaria VARCHAR(34),
    precio_por_hora DECIMAL(10,2) NOT NULL,
    especialidad VARCHAR(100),
    id_usuario INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);

CREATE TABLE Producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    cantidad_stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 0,
    precio_compra DECIMAL(10,2) NOT NULL,
    id_proveedor INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_proveedor) REFERENCES Proveedor(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);


--- 3. TABLAS DEL NÚCLEO
CREATE TABLE Reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_reserva DATE NOT NULL,
    fecha_evento DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    num_asistentes INT NOT NULL,
    estado ENUM('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
    observaciones TEXT,
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

CREATE TABLE Asignacion_Monitor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    rol_evento VARCHAR(100),
    observaciones TEXT,
    id_reserva INT NOT NULL,
    id_empleado INT NOT NULL,
    FOREIGN KEY (id_reserva) REFERENCES Reserva(id),
    FOREIGN KEY (id_empleado) REFERENCES Empleado(id)
);

CREATE TABLE Compra_Suministro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cantidad_comprada INT NOT NULL,
    fecha DATE NOT NULL,
    importe_total DECIMAL(10,2) NOT NULL,
    estado_pago_proveedor VARCHAR(50),
    id_producto INT NOT NULL,
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES Producto(id),
    FOREIGN KEY (id_empresa) REFERENCES Empresa(id)
);