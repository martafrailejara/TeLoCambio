-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql308.infinityfree.com
-- Tiempo de generación: 02-06-2024 a las 08:07:39
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_36640038_hospital`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_turno`
--

CREATE TABLE `asignacion_turno` (
  `ID` int(11) NOT NULL,
  `ID_Departamento` int(11) NOT NULL,
  `ID_Turno` int(11) DEFAULT NULL,
  `ID_Trabajador` int(11) DEFAULT NULL,
  `Publicado_libre` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `asignacion_turno`
--

INSERT INTO `asignacion_turno` (`ID`, `ID_Departamento`, `ID_Turno`, `ID_Trabajador`, `Publicado_libre`) VALUES
(1, 1, 1, 1, 0),
(2, 1, 4, 4, 0),
(3, 1, 7, 3, 0),
(4, 1, 10, 5, 0),
(5, 1, 13, 2, 0),
(6, 1, 16, 1, 0),
(7, 1, 19, 4, 0),
(8, 1, 2, 3, 0),
(9, 1, 5, 5, 0),
(10, 1, 8, 2, 0),
(11, 1, 11, 1, 0),
(12, 1, 14, 4, 0),
(13, 1, 17, 9, 0),
(14, 1, 20, 5, 0),
(15, 1, 3, 3, 0),
(16, 1, 6, 1, 0),
(17, 1, 9, 4, 0),
(18, 1, 12, 2, 0),
(19, 1, 15, 5, 0),
(20, 1, 18, 3, 0),
(21, 1, 21, 1, 0),
(22, 2, 1, 6, 0),
(23, 2, 4, 9, 0),
(24, 2, 7, 7, 0),
(25, 2, 10, 10, 0),
(26, 2, 13, 11, 0),
(27, 2, 16, 6, 0),
(28, 2, 19, 9, 0),
(29, 2, 2, 7, 0),
(30, 2, 5, 10, 0),
(31, 2, 8, 11, 0),
(32, 2, 11, 6, 0),
(33, 2, 14, 2, 0),
(34, 2, 17, 7, 0),
(35, 2, 20, 10, 0),
(36, 2, 3, 11, 0),
(37, 2, 6, 6, 0),
(38, 2, 9, 9, 0),
(39, 2, 12, 7, 0),
(40, 2, 15, 10, 0),
(41, 2, 18, 11, 0),
(42, 2, 21, 6, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`ID`, `Nombre`, `Descripcion`) VALUES
(1, 'Medicina General', 'Atencion medica integral para todas las edades.'),
(2, 'Pediatria', 'Atencion medica para niÃ±os y adolescentes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `ID` int(11) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Descripcion` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`ID`, `Fecha`, `Descripcion`) VALUES
(1, '2024-05-31', 'Reunion del equipo de turno de la mañana 78'),
(2, '2025-01-19', 'Dia festivo - Oficina cerrada'),
(3, '2024-12-13', 'Entrenamiento en nuevas politicas de atencion al cliente'),
(15, '2024-06-02', 'PRUEBA UNITARIA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `ID` int(11) NOT NULL,
  `Tipo_notificacion` varchar(100) DEFAULT NULL,
  `ID_Remitente` int(11) DEFAULT NULL,
  `ID_Destinatario` int(11) DEFAULT NULL,
  `ID_Turno_Solicitado` int(11) DEFAULT NULL,
  `ID_Turno_Interesado` int(11) DEFAULT NULL,
  `Estado_notificacion` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

CREATE TABLE `trabajador` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Apellido` varchar(100) DEFAULT NULL,
  `DNI` varchar(20) DEFAULT NULL,
  `Correo_electronico` varchar(100) DEFAULT NULL,
  `Contrasena` varchar(255) DEFAULT NULL,
  `Es_administrador` tinyint(4) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `Nombre_Usuario` varchar(50) DEFAULT NULL,
  `Numero_Departamento` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`ID`, `Nombre`, `Apellido`, `DNI`, `Correo_electronico`, `Contrasena`, `Es_administrador`, `Telefono`, `Direccion`, `Nombre_Usuario`, `Numero_Departamento`) VALUES
(1, 'Alexandra', 'Rodriguez', '36637432L', 'alexandra@gmail.com', '$2y$10$g/uEEuuQQmjLOEQF7AFaRuUO5qxd/SiVLh6xI0vjMSbZVhe192wRW', 0, '636585956', '0', 'AlexandraRod', 1),
(2, 'Carlos', 'Sanchez', '19371620P', 'carlos@gmail.com', '$2y$10$WvTK6CCttsWjUALYSpCaHORvb6Fy1FGfmsSRmMNAozsBWLSVuRqiO', 0, '936534562', 'sdadsadsadsad', 'CarlosSan', 1),
(3, 'Laura', 'Fernandez', '91368470G', 'laura@gmail.com', '$2y$10$A3V2k2z9w6HS./HmrTaeWeCmtp5XmS5vZwPcA1V32Cp7YQcMheqVe', 0, '636585955', 'Plaza de la Ilusión 789', 'LauraFer', 1),
(4, 'Saul', 'Garcia', '69402139F', 'saul@gmail.com', '$2y$10$x.EqxEld.lxitLdjARYnUuxdMJ.cCGpKyU6PwrI4Mw5N7pZxPo0em', 0, '636526955', 'Calle Falsa 321', 'SaulGar', 1),
(5, 'Ines', 'Martin', '83738439Q', 'ines@gmail.com', '$2y$10$QqTPbqi1zLyiB.l9icAxg.3X5ODpooegNJ57tvNiozvEnTQsqM8X6', 0, '635626955', 'Avenida Inexistente 654', 'InesMar', 1),
(6, 'David', 'Perez', '24490217R', 'david@gmail.com', '$2y$10$GfUXGESPE2O6ERaF.udkb.wbba.Zh3pX5uUN9rhUGpGski94YCLDW', 0, '638926955', 'Calle de los Sueños 987', 'DavidPer', 2),
(7, 'Patricia', 'Gonzalez', '59418363W', 'patricia@gmail.com', '$2y$10$5pTygtj8EEYGHlsmjmqi3.YjqHlMBTEf7TZ1RUVt6h23y5tNQRJuG', 0, '638959955', 'Avenida de las Fantasías 159', 'PatriciaGon', 2),
(11, 'Alvaro', 'Lopez', '45378770T', 'alvaro@gmail.com', '$2y$10$OmxJIFq5PzKj/cBKr4nQzuwqn3zCLbXsGK80qAtNSMnkye3ChmMc.', 0, '635479955', 'Avenida del Sol 34', 'AlvaroLop', 2),
(9, 'Raquel', 'Diaz', '54490849P', 'raquel@gmail.com', '$2y$10$bWxmzt9ZFcz37glBC1SsyuIZyj7A0b2s9KF/HJWi.Sxc/Syks1V4O', 0, '635458955', 'Callejón de las Flores 56', 'RaquelDia', 2),
(10, 'Claudia', 'Ruiz', '37159865F', 'claudia@gmail.com', '$2y$10$07J/QQFhit.A41vUOcjLAO5PbvzUfMHksg9E2uKXQAi.LRuVINGfy', 0, '635458678', 'Avenida de la Luna 78', 'ClaudiaRui', 2),
(23, 'Marta', 'Fraile', '56477406D', 'martaAdmin@gmail.com', '$2y$10$haMpzyIX0rQ08ftrm/HUTOoFYifab72GgTm1iZuAHdPqxx14PCdTC', 1, '936585662', 'Calle de los Susurros 90', 'MartaFra', NULL),
(46, 'Marta', 'Fraile', '48766136H', 'martaPrueba@gmail.com', '$2y$10$QL2Q6vVV/kmr4..I2enlGep/hO/dQIbzNJAt8tGjBT.qC2wV1KEZy', 1, '636585962', 'prueba prueba', 'MartaPrueba', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `ID` int(11) NOT NULL,
  `Tipo_turno` varchar(50) DEFAULT NULL,
  `Dia_semana` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`ID`, `Tipo_turno`, `Dia_semana`) VALUES
(1, 'mañana', 'lunes'),
(2, 'tarde', 'lunes'),
(3, 'noche', 'lunes'),
(4, 'mañana', 'martes'),
(5, 'tarde', 'martes'),
(6, 'noche', 'martes'),
(7, 'mañana', 'miercoles'),
(8, 'tarde', 'miercoles'),
(9, 'noche', 'miercoles'),
(10, 'mañana', 'jueves'),
(11, 'tarde', 'jueves'),
(12, 'noche', 'jueves'),
(13, 'mañana', 'viernes'),
(14, 'tarde', 'viernes'),
(15, 'noche', 'viernes'),
(16, 'mañana', 'sabado'),
(17, 'tarde', 'sabado'),
(18, 'noche', 'sabado'),
(19, 'mañana', 'domingo'),
(20, 'tarde', 'domingo'),
(21, 'noche', 'domingo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoraciones`
--

CREATE TABLE `valoraciones` (
  `ID` int(11) NOT NULL,
  `ID_Valorador` int(11) DEFAULT NULL,
  `ID_Trabajador_Valorado` int(11) DEFAULT NULL,
  `Puntuacion` int(11) DEFAULT NULL,
  `Comentario` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignacion_turno`
--
ALTER TABLE `asignacion_turno`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Turno` (`ID_Turno`),
  ADD KEY `ID_Trabajador` (`ID_Trabajador`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `DNI` (`DNI`,`Correo_electronico`,`Telefono`,`Nombre_Usuario`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignacion_turno`
--
ALTER TABLE `asignacion_turno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
