-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-05-2024 a las 10:11:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cwubd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `NIF` varchar(9) NOT NULL,
  `nombrecli` varchar(30) NOT NULL,
  `apellidos` varchar(30) NOT NULL,
  `movil1` varchar(9) NOT NULL,
  `movil2` varchar(9) NOT NULL,
  `corre1` varchar(30) NOT NULL,
  `corre2` varchar(30) NOT NULL,
  `contra` varchar(9) NOT NULL,
  `direccion` varchar(30) NOT NULL,
  `como_conoce` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`NIF`, `nombrecli`, `apellidos`, `movil1`, `movil2`, `corre1`, `corre2`, `contra`, `direccion`, `como_conoce`) VALUES
('22692870N', 'Jose Antonio', 'VIDAL VIDAL', '655687307', '655687308', 'majocavi@gmail.com', 'otro@gmail.com', '123456Aa', 'Avda Mtro Rodrigo 35 Esc 1 pta', 'boca a boca'),
('73759398P', 'Magdalena', 'Belinchón Guijarro', '12345654', '64313132', 'jjlkj@gmail.com', 'otro@gmail.com', 'Aa123456', 'Avda Mtro Rodrigo 35 Esc 1 pta', 'boca a boca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `id_evento` int(11) NOT NULL,
  `id_comercial` int(11) NOT NULL DEFAULT 0,
  `fecha_evento` date NOT NULL,
  `hora_evento` time NOT NULL,
  `fecha_ppto` date NOT NULL,
  `estado` enum('solicitado','presupuestado','confirmado','realizado','nulo') NOT NULL,
  `tipo` enum('cumpleaños','sala_escape','fiesta','reunion_familiar','evento_empresa') NOT NULL,
  `NIF_res` varchar(9) NOT NULL COMMENT 'Cliente, NIF del responsable',
  `rotulo` varchar(30) NOT NULL COMMENT 'Nombre cumpleañero y edad para q aparezca en pantalla',
  `tot_participantes` int(11) NOT NULL,
  `esp_participantes` int(11) NOT NULL,
  `sala_escape` enum('clínica','biblioteca','ambas','') NOT NULL,
  `vr` tinyint(1) NOT NULL COMMENT 'Realidad Virtual',
  `tarta` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Se incluye gratis',
  `fecha_reserva` date NOT NULL,
  `formapago_reserva` enum('transferencia','efectivo','biizum','ingreso_cta') NOT NULL,
  `fecha_pago_resto` date NOT NULL,
  `formapago_resto` enum('transferencia','efectivo','bizum','ingreso_cta') NOT NULL,
  `id_monitor1` int(11) NOT NULL,
  `id_monitor2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`NIF`);

--
-- Indices de la tabla `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id_evento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
