-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2026 a las 15:00:13
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tone_trainer_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_name` varchar(100) NOT NULL,
  `badge_icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `earned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup_users`
--

CREATE TABLE `backup_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','trainer','admin') DEFAULT 'user',
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birthdate` date DEFAULT NULL,
  `medical_history` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `backup_users`
--

INSERT INTO `backup_users` (`id`, `name`, `email`, `password`, `role`, `active`, `created_at`, `last_update`, `birthdate`, `medical_history`) VALUES
(2, 'Usuario Eliminar', 'eliminar@correo.com', '12345', 'user', 1, '2025-07-03 14:33:47', '2025-07-03 14:33:47', '1995-05-05', 'Sin antecedentes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_preferences`
--

CREATE TABLE `company_preferences` (
  `id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `notification_settings` text DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `exercises`
--

INSERT INTO `exercises` (`id`, `name`, `description`, `type`, `video_url`, `last_update`) VALUES
(1, 'Sentadilla', 'Ejercicio de fuerza para tren inferior', 'Piernas', 'https://www.youtube.com/sentadilla', '2025-07-03 14:59:50'),
(2, 'Press de banca', 'Ejercicio de fuerza para pecho', 'Pecho', 'https://www.youtube.com/pressbanca', '2025-07-03 14:59:50'),
(3, 'Remo con barra', 'Ejercicio de fuerza para espalda', 'Espalda', 'https://www.youtube.com/remobarra', '2025-07-03 14:59:50'),
(4, 'Curl de bíceps', 'Ejercicio para bíceps', 'Brazos', 'https://www.youtube.com/curlbiceps', '2025-07-03 14:59:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `target_value` decimal(10,2) NOT NULL,
  `current_value` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(20) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('active','completed','failed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_routines`
--

CREATE TABLE `historial_routines` (
  `id_historial` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `name_old` varchar(100) DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_routines`
--

INSERT INTO `historial_routines` (`id_historial`, `routine_id`, `name_old`, `modification_date`) VALUES
(1, 1, 'Rutina Prueba', '2025-07-03 09:45:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(2, 22, 33, 'hola jana', 1, '2025-11-25 20:14:33'),
(3, 22, 33, 'hola', 1, '2025-11-25 20:14:37'),
(4, 33, 22, 'hola', 1, '2025-11-25 20:14:58'),
(7, 33, 22, 'hola', 1, '2025-11-25 20:21:13'),
(8, 22, 33, 'como estas', 1, '2025-11-25 20:25:57'),
(9, 33, 22, 'bien y tu?', 1, '2025-11-25 20:26:05'),
(10, 22, 33, 'super bien', 1, '2025-11-25 20:26:10'),
(11, 22, 33, 'holi', 1, '2025-11-25 20:26:28'),
(12, 33, 22, 'holaaaaa', 1, '2025-11-25 20:26:35'),
(13, 22, 33, 'como estas', 1, '2025-11-27 11:19:22'),
(14, 33, 22, 'bien y tu ?', 1, '2025-11-27 11:19:32'),
(15, 22, 34, 'hola', 1, '2025-11-27 11:29:13'),
(16, 34, 22, 'como estas?', 1, '2025-11-27 11:29:23'),
(17, 22, 34, 'bien y tu?', 1, '2025-11-27 11:38:45'),
(18, 34, 22, 'bien', 1, '2025-11-27 11:39:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nutrition_plan`
--

CREATE TABLE `nutrition_plan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `meal_type` varchar(20) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` int(11) DEFAULT NULL,
  `carbs` int(11) DEFAULT NULL,
  `fats` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nutrition_plan`
--

INSERT INTO `nutrition_plan` (`id`, `user_id`, `day_of_week`, `meal_type`, `food_name`, `calories`, `protein`, `carbs`, `fats`, `created_at`) VALUES
(5, 23, 'Lunes', 'Desayuno', 'pene', 12212121, 21212121, 21212121, 0, '2025-11-05 07:44:25'),
(15, 22, 'Lunes', 'Desayuno', 'Pechuga de pollo', 324, 324, 34, 2, '2025-11-25 08:06:42'),
(16, 22, 'Martes', 'Desayuno', 'pollo', 200, 150, 20, 89, '2025-11-27 06:20:25'),
(18, 8, 'Lunes', 'Desayuno', 'pechuga de pollo, con hueuvos fritos', 650, 75, 10, 35, '2025-12-02 22:55:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nutrition_plans`
--

CREATE TABLE `nutrition_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goal` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `daily_calories` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `physical_tracking`
--

CREATE TABLE `physical_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `body_fat_percentage` decimal(4,2) DEFAULT NULL,
  `measurements` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `IMC` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `physical_tracking`
--
DELIMITER $$
CREATE TRIGGER `verificar_usuario_physical_tracking` BEFORE INSERT ON `physical_tracking` FOR EACH ROW BEGIN
    IF NOT EXISTS (SELECT 1 FROM users WHERE id = NEW.user_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no v?lido';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `body_fat` decimal(5,2) DEFAULT NULL,
  `muscle_mass` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `water_intake` decimal(5,2) DEFAULT NULL,
  `protein_intake` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `progress`
--

INSERT INTO `progress` (`id`, `user_id`, `weight`, `body_fat`, `muscle_mass`, `bmi`, `water_intake`, `protein_intake`, `created_at`) VALUES
(1, 0, 43.00, 432.00, 52.00, 253.00, 54.00, 52.00, '2025-11-05 12:10:58'),
(2, 0, 132.00, 12.00, 123.00, 13.00, 12.00, 1223.00, '2025-11-05 12:12:53'),
(3, 0, 75.00, 78.00, 114.00, 10.00, 25.00, 122.00, '2025-11-05 12:13:21'),
(4, 23, 123.00, 122.00, 12.00, 122.00, 12.00, 122.00, '2025-11-05 12:14:34'),
(5, 8, 999.99, 999.99, 999.99, 999.99, 999.99, 9999.99, '2025-11-05 12:15:16'),
(6, 8, 12.00, 12.00, 12.00, 12.00, 12.00, 12.00, '2025-11-05 13:01:52'),
(7, 23, 80.00, 0.00, 15.00, 134.00, 12.00, 144.00, '2025-11-06 11:33:09'),
(8, 21, 60.00, 10.00, 20.00, 1.00, 1.00, 20.00, '2025-11-20 12:28:43'),
(9, 22, 878.00, 76.00, 2.00, 3.00, 34.00, 3.00, '2025-11-25 07:38:23'),
(10, 22, 70.00, 10.00, 100.00, 2.00, 5.00, 200.00, '2025-11-25 11:21:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progress_metrics`
--

CREATE TABLE `progress_metrics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT curdate(),
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `body_fat` decimal(5,2) DEFAULT NULL,
  `muscle_mass` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bmi` decimal(5,2) DEFAULT NULL,
  `water_intake` decimal(5,2) DEFAULT NULL,
  `protein_intake` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `routines`
--

CREATE TABLE `routines` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `objective` varchar(100) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `routines`
--

INSERT INTO `routines` (`id`, `trainer_id`, `name`, `objective`, `level`, `description`, `duration_minutes`, `created_at`, `last_update`) VALUES
(1, NULL, 'Rutina Prueba Editada', 'Fuerza', 'beginner', 'Rutina inicial de prueba', 45, '2025-07-03 14:45:42', '2025-07-03 14:45:55'),
(2, NULL, 'Rutina Fuerza Inicial', 'Fuerza general', 'beginner', 'Rutina para principiantes con ejercicios básicos', 45, '2025-07-03 15:00:04', '2025-07-03 15:00:04'),
(3, NULL, 'Rutina Full Body Intermedia', 'Tonificación', 'intermediate', 'Rutina para intermedios, cuerpo completo', 60, '2025-07-03 15:00:04', '2025-07-03 15:00:04');

--
-- Disparadores `routines`
--
DELIMITER $$
CREATE TRIGGER `guardar_historial_routine` BEFORE UPDATE ON `routines` FOR EACH ROW BEGIN
    INSERT INTO historial_routines (routine_id, name_old, modification_date)
    VALUES (OLD.id, OLD.name, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `routine_exercises`
--

CREATE TABLE `routine_exercises` (
  `id` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `exercise_id` int(11) DEFAULT NULL,
  `sets` tinyint(4) DEFAULT NULL,
  `reps` tinyint(4) DEFAULT NULL,
  `rest_seconds` tinyint(4) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `routine_exercises`
--

INSERT INTO `routine_exercises` (`id`, `routine_id`, `exercise_id`, `sets`, `reps`, `rest_seconds`, `last_update`) VALUES
(1, 1, 1, 4, 12, 60, '2025-07-03 15:04:11'),
(2, 1, 2, 3, 10, 60, '2025-07-03 15:04:11'),
(3, 2, 3, 4, 8, 90, '2025-07-03 15:04:11'),
(4, 2, 4, 3, 12, 60, '2025-07-03 15:04:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traceability`
--

CREATE TABLE `traceability` (
  `id` int(11) NOT NULL,
  `requirement_id` varchar(50) DEFAULT NULL,
  `linked_module` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `training_completions`
--

CREATE TABLE `training_completions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `training_completions`
--

INSERT INTO `training_completions` (`id`, `user_id`, `exercise_id`, `completed_at`) VALUES
(3, 8, 6, '2025-12-03 04:19:15'),
(4, 8, 5, '2025-12-03 04:20:21'),
(9, 8, 3, '2025-12-03 04:32:25'),
(21, 8, 3, '2026-03-18 13:13:02'),
(22, 8, 5, '2026-03-18 13:13:03'),
(23, 8, 6, '2026-03-18 13:13:04'),
(24, 36, 11, '2026-04-21 01:58:53'),
(26, 36, 12, '2026-04-21 01:59:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `training_plan`
--

CREATE TABLE `training_plan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `day_group` varchar(50) NOT NULL,
  `exercise` varchar(100) NOT NULL,
  `series` int(11) NOT NULL,
  `reps` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `training_plan`
--

INSERT INTO `training_plan` (`id`, `user_id`, `assigned_by`, `day_group`, `exercise`, `series`, `reps`, `description`, `status`, `created_at`) VALUES
(1, 22, 34, 'Lunes', 'cardio al fallo', 20, 50, 'cuca al fallo hasta q duela', 0, '2025-11-25 02:32:27'),
(2, 22, 34, 'Martes', 'dormir', 24, 7, 'hola', 0, '2025-11-27 11:21:36'),
(3, 8, 22, 'Lunes', 'press banca', 15, 4, 'ejercicio de levantamiento de peso que consiste en bajar una barra hasta el pecho y luego empujarla hacia arriba mientras se está acostado en un banco, trabajando principalmente los músculos del pectoral, hombros (deltoides) y tríceps. Es fundamental para la fuerza del tren superior y requiere una técnica adecuada para evitar lesiones, manteniendo los glúteos, la espalda y la cabeza apoyados en el banco. ', 0, '2025-12-03 04:01:25'),
(5, 8, 22, 'Lunes', 'Aperturas con mancuernas', 12, 4, ' Ayuda a aislar el pectoral y trabaja el rango de movimiento horizontal, complementando el movimiento de empuje compuesto.', 0, '2025-12-03 04:03:40'),
(6, 8, 22, 'Lunes', 'Dominadas', 15, 4, 'Ejercicios excelentes para desarrollar la espalda y los bíceps, que son músculos antagonistas al press de banca.', 0, '2025-12-03 04:04:26'),
(7, 8, 22, 'Martes', 'press banca', 15, 4, 'ejercicio de levantamiento de peso que consiste en bajar una barra hasta el pecho y luego empujarla hacia arriba mientras se está acostado en un banco, trabajando principalmente los músculos del pectoral, hombros (deltoides) y tríceps. Es fundamental para la fuerza del tren superior y requiere una técnica adecuada para evitar lesiones, manteniendo los glúteos, la espalda y la cabeza apoyados en el banco. ', 0, '2025-12-03 04:22:13'),
(8, 8, 22, 'Martes', 'Aperturas con mancuernas', 12, 4, ' Ayuda a aislar el pectoral y trabaja el rango de movimiento horizontal, complementando el movimiento de empuje compuesto.', 0, '2025-12-03 04:22:51'),
(9, 8, 22, 'Martes', 'Dominadas', 15, 4, 'Ejercicios excelentes para desarrollar la espalda y los bíceps, que son músculos antagonistas al press de banca.', 0, '2025-12-03 04:23:13'),
(10, 13, 22, 'Lunes', 'dormir', 3, 12, 'ksc', 0, '2026-03-18 01:32:29'),
(11, 36, 34, 'Lunes', 'uuijk', 8989, 999, 'bkj', 0, '2026-04-02 18:29:27'),
(12, 36, 22, 'Miércoles', 'dfdwfew', 4, 15, 'ewfew', 0, '2026-04-20 20:44:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 2,
  `nutritionist_id` int(11) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birthdate` date DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `medical_history` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `goal` varchar(255) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `trainer` varchar(100) DEFAULT NULL,
  `membership_start` date DEFAULT NULL,
  `nutrition_plan` text DEFAULT NULL,
  `achievements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `nutritionist_id`, `trainer_id`, `active`, `created_at`, `last_update`, `birthdate`, `profile_photo`, `reset_token`, `reset_expires`, `medical_history`, `phone`, `location`, `goal`, `level`, `weight`, `height`, `imc`, `trainer`, `membership_start`, `nutrition_plan`, `achievements`) VALUES
(8, 'dayam', 'ospinadayam6@gmail.com', '$2y$10$1jMrLWp46tIRyAoT.BaPpudmMdlDj8Y5QTOrbrhqJyWb60BJZSxFS', 1, NULL, NULL, 1, '2025-09-09 12:38:32', '2025-12-03 04:48:23', '2006-09-18', 'uploads/8_1764737303.png', '5f97c7a1540cfc8d196a9e71f2365021', '2025-12-03 05:50:51', '', '3106016301', 'SPORT CLUB san javier', 'ganar masa muscular', 'Intermedio', 76.00, 185.00, NULL, NULL, NULL, NULL, NULL),
(13, 'maximiliano', 'valenciajuanjose325@gmail.com', '$2y$10$..d2MFCi9WfyA1LCqCgWTuvm9ROWhs00Vm0.11Ph8298uCJjJJmm6', 1, NULL, 34, 1, '2025-09-10 23:18:13', '2026-03-18 14:37:08', '2205-10-10', NULL, 'a67738e3a5679d7e95cf26a17b2fc448', '2025-11-25 14:49:18', '', NULL, NULL, '', 'Principiante', 0.00, 0.00, NULL, 'Maira Alexandra Guerra Arias', NULL, NULL, NULL),
(21, 'maxi', 'm.3@gmail.com', '$2y$12$p/Z5xRlREVi2MykhKwabCOgWILwkhx/jbrEfjPxAFoWLJ3wr0hK.2', 2, NULL, NULL, 1, '2025-09-17 21:49:25', '2026-04-20 17:58:10', '2025-09-17', 'uploads/21_1763643570.png', 'ec0913f26c0c97a46796e890fc9a6ddb', '2025-11-25 14:41:16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'maxi', 'm.4@gmail.com', '$2y$12$.L7scDya7HWahgwU/3zmyuHKRKBADxJqkXOLnK5X1dRP8fSwwwNn6', 4, NULL, NULL, 1, '2025-09-17 23:04:56', '2026-04-20 20:05:58', '2007-10-10', 'uploads/22_1776715558.png', NULL, NULL, 'nada', '3014085511', 'enciso', 'xd', 'Intermedio', 60.00, 170.00, NULL, '', NULL, NULL, NULL),
(33, 'Jana Aparicio Munera', 'apariciomunerajana@gmail.com', '$2y$12$XN5uys6BAOdOa.Jf3Iu6tOjH2ZacPcHpyhjIeC83RcdEt3yE9vyl6', 3, NULL, NULL, 1, '2025-11-25 08:16:35', '2026-04-20 17:53:02', '2008-02-20', 'uploads/33_1773796470.png', 'adf357f0c125eb5b59474f60c0c48bcf2ee7f89e108dc47dfc48c7efccad98cf', '2026-04-02 22:07:14', 'sin antecedentes', NULL, NULL, 'mostrarle a mi novio que funciona', 'Avanzado', 58.00, 161.00, NULL, 'mi novio', NULL, NULL, NULL),
(34, 'Maira Alexandra Guerra Arias', 'maira@gmail.com', '$2y$10$Ma20CZLegl8gkS/OGst.OuuEHh1YzpNRq67FuEoyIw8qSpLORxYF.', 4, NULL, NULL, 1, '2025-11-25 08:24:08', '2025-11-25 11:16:59', '2005-09-02', NULL, NULL, NULL, 'fractura mano derecha', NULL, NULL, '', 'Avanzado', 0.00, 0.00, NULL, '', NULL, NULL, NULL),
(35, 'max|', 'm.33@gmail.com', '$2y$10$xX6FFkdFVGCwdVwrwJMUoOS/sapJa0aDJUIMEqfiLPa1vBOGTVjLO', 1, NULL, 34, 1, '2026-03-18 20:38:10', '2026-03-18 20:38:10', '2008-09-10', NULL, NULL, NULL, 'kk', NULL, NULL, 'k', 'Avanzado', 90.00, 180.00, NULL, 'Maira Alexandra Guerra Arias', NULL, NULL, NULL),
(36, 'Sara Sophia Quintero Lotero', 'sarasophia.quintero@udea.edu.co', '$2y$12$xOGXDKxkLQ1c5BdCQgaN6OgvGdGEAPgys0fM6dI8dQ8eofq2ABjMa', 1, NULL, NULL, 1, '2026-04-03 01:26:11', '2026-04-20 20:43:39', '2007-07-10', 'uploads/36_1776717819.jpeg', 'b6d12e0571510650cccdefb649a6a441f10204b713d35f165a3fa2377363ccaf', '2026-04-02 22:03:09', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'Test User', 'maxig@test.com', '$2y$12$6uM/WMfgf21E9obNhOgpA.wcUT2hfhx0mLZuSfaaoUxxXn9mH21LC', 1, NULL, NULL, 1, '2026-04-20 18:43:52', '2026-04-20 18:43:52', '0101-09-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_behavior`
--

CREATE TABLE `user_behavior` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  `context` text DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goal` varchar(100) DEFAULT NULL,
  `training_level` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `weekly_frequency` tinyint(4) DEFAULT NULL,
  `training_type` varchar(100) DEFAULT NULL,
  `physical_restrictions` text DEFAULT NULL,
  `preferred_schedule` varchar(20) DEFAULT NULL,
  `reminders` tinyint(1) DEFAULT NULL,
  `push_notifications` tinyint(1) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workout_calendar`
--

CREATE TABLE `workout_calendar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `workout_date` date NOT NULL,
  `workout_type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed` tinyint(4) DEFAULT 0,
  `duration_minutes` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `workout_calendar`
--

INSERT INTO `workout_calendar` (`id`, `user_id`, `workout_date`, `workout_type`, `title`, `notes`, `completed`, `duration_minutes`, `calories_burned`, `created_at`, `created_by`) VALUES
(1, 8, '2025-12-03', 'pecho', 'pecho', 'nada', 1, 60, 600, '2025-12-03 04:32:05', NULL),
(2, 8, '2025-12-03', 'espalda', 'rumba', 'profe ericson', 1, 60, 30, '2025-12-03 04:33:22', NULL),
(3, 8, '2025-12-04', 'fullbody', 'spinning', 'profe javi', 1, 120, 500, '2025-12-03 04:34:02', NULL),
(4, 35, '2026-03-18', 'fuerza', 'dia de pecho|', 'ghhj\n', 0, 70, 0, '2026-03-18 14:39:51', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `backup_users`
--
ALTER TABLE `backup_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `company_preferences`
--
ALTER TABLE `company_preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `historial_routines`
--
ALTER TABLE `historial_routines`
  ADD PRIMARY KEY (`id_historial`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation` (`sender_id`,`receiver_id`),
  ADD KEY `idx_receiver_unread` (`receiver_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indices de la tabla `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_subscribed_at` (`subscribed_at`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nutrition_plan`
--
ALTER TABLE `nutrition_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `physical_tracking`
--
ALTER TABLE `physical_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `progress_metrics`
--
ALTER TABLE `progress_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `routines`
--
ALTER TABLE `routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indices de la tabla `routine_exercises`
--
ALTER TABLE `routine_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `routine_id` (`routine_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indices de la tabla `traceability`
--
ALTER TABLE `traceability`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `training_completions`
--
ALTER TABLE `training_completions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`completed_at`),
  ADD KEY `idx_exercise` (`exercise_id`);

--
-- Indices de la tabla `training_plan`
--
ALTER TABLE `training_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `nutritionist_id` (`nutritionist_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indices de la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `workout_calendar`
--
ALTER TABLE `workout_calendar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`workout_date`,`title`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `backup_users`
--
ALTER TABLE `backup_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `company_preferences`
--
ALTER TABLE `company_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_routines`
--
ALTER TABLE `historial_routines`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nutrition_plan`
--
ALTER TABLE `nutrition_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `physical_tracking`
--
ALTER TABLE `physical_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `progress_metrics`
--
ALTER TABLE `progress_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `routines`
--
ALTER TABLE `routines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `routine_exercises`
--
ALTER TABLE `routine_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `traceability`
--
ALTER TABLE `traceability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `training_completions`
--
ALTER TABLE `training_completions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `training_plan`
--
ALTER TABLE `training_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `workout_calendar`
--
ALTER TABLE `workout_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  ADD CONSTRAINT `nutrition_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `physical_tracking`
--
ALTER TABLE `physical_tracking`
  ADD CONSTRAINT `physical_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `progress_metrics`
--
ALTER TABLE `progress_metrics`
  ADD CONSTRAINT `progress_metrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `routines`
--
ALTER TABLE `routines`
  ADD CONSTRAINT `routines_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `routine_exercises`
--
ALTER TABLE `routine_exercises`
  ADD CONSTRAINT `routine_exercises_ibfk_1` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `routine_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `training_completions`
--
ALTER TABLE `training_completions`
  ADD CONSTRAINT `training_completions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_completions_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `training_plan` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`nutritionist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD CONSTRAINT `user_behavior_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `workout_calendar`
--
ALTER TABLE `workout_calendar`
  ADD CONSTRAINT `workout_calendar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workout_calendar_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
