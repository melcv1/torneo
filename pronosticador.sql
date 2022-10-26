-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2022 a las 17:58:08
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `torneo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pronosticador`
--

CREATE TABLE `pronosticador` (
  `ID_ENCUESTA` int(11) NOT NULL,
  `ID_PARTICIPANTE` int(11) DEFAULT NULL,
  `GRUPO` varchar(1) DEFAULT NULL,
  `EQUIPO` varchar(256) DEFAULT NULL,
  `POSICION` varchar(256) DEFAULT NULL,
  `NUMERACION` varchar(4) DEFAULT NULL,
  `crea_dato` datetime DEFAULT current_timestamp(),
  `modifica_dato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuario_dato` varchar(256) DEFAULT 'admin',
  `ID_EQUIPOTORNEO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pronosticador`
--
ALTER TABLE `pronosticador`
  ADD PRIMARY KEY (`ID_ENCUESTA`),
  ADD KEY `FK_RELATIONSHIP_4` (`ID_PARTICIPANTE`),
  ADD KEY `ID_EQUIPOTORNEO` (`ID_EQUIPOTORNEO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pronosticador`
--
ALTER TABLE `pronosticador`
  MODIFY `ID_ENCUESTA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pronosticador`
--
ALTER TABLE `pronosticador`
  ADD CONSTRAINT `FK_RELATIONSHIP_4` FOREIGN KEY (`ID_PARTICIPANTE`) REFERENCES `participante` (`ID_PARTICIPANTE`),
  ADD CONSTRAINT `pronosticador_ibfk_1` FOREIGN KEY (`ID_EQUIPOTORNEO`) REFERENCES `equipotorneo` (`ID_EQUIPO_TORNEO`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
