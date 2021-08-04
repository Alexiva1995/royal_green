-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-07-2021 a las 19:35:04
-- Versión del servidor: 10.3.27-MariaDB-0+deb10u1
-- Versión de PHP: 7.3.27-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bfx`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierre_comisions`
--

CREATE TABLE `cierre_comisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `s_inicial` double NOT NULL COMMENT 'saldo inicial',
  `s_ingreso` double NOT NULL COMMENT 'saldo ingreso',
  `s_final` double NOT NULL COMMENT 'saldo final',
  `cierre` date NOT NULL COMMENT 'fecha del cierre',
  `comentario` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inversions`
--

CREATE TABLE `inversions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `invertido` double NOT NULL,
  `ganacia` double NOT NULL,
  `retiro` double NOT NULL,
  `capital` double NOT NULL,
  `progreso` double NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `porcentaje_fondo` decimal(8,2) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 - activo , 2 - culminada',
  `status_por_pagar` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 - por Pagar , 0 - Pagado',
  `ganancia_acumulada` double NOT NULL DEFAULT 0,
  `porcentaje_utilidad` decimal(8,2) DEFAULT NULL,
  `max_ganancia` double DEFAULT NULL,
  `restante` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidactions`
--

CREATE TABLE `liquidactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `total` double NOT NULL,
  `monto_bruto` double NOT NULL,
  `feed` double NOT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_liquidations`
--

CREATE TABLE `log_liquidations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idliquidation` bigint(20) UNSIGNED NOT NULL,
  `comentario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2020_10_05_165857_create_groups_table', 1),
(4, '2020_10_06_043331_create_packages_table', 1),
(5, '2020_10_19_160343_create_liquidactions_table', 1),
(6, '2020_11_05_181015_create_orden_purchases_table', 1),
(7, '2020_11_12_172029_create_cierre_comisions_table', 1),
(8, '2020_11_13_210917_create_wallets_table', 1),
(9, '2021_03_13_132234_create_tickets_table', 1),
(10, '2021_03_19_211758_create_log_liquidations_table', 1),
(11, '2021_06_10_224007_create_inversions_table', 1),
(12, '2021_07_07_175601_create_ranks_table', 1),
(13, '2021_07_14_140349_create_porcentaje_utilidads_table', 1),
(14, '2021_07_14_151737_create_wallet_binaries_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_purchases`
--

CREATE TABLE `orden_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `idtransacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de la transacion',
  `status` enum('0','1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 - En Espera, 1 - Completada, 2 - Rechazada, 3 - Cancelada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `expired` date DEFAULT NULL COMMENT 'Fecha de vencimiento del paquete',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '0 - desactivado, 1 - activado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `porcentaje_utilidads`
--

CREATE TABLE `porcentaje_utilidads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `porcentaje_utilidad` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ranks`
--

CREATE TABLE `ranks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 - Activo, 1 - Inactivo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Abierto, 1 - Cerrado, 2',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Alto, 1 - Medio, 2 - bajo',
  `issue` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet` double NOT NULL DEFAULT 0,
  `admin` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'permite saber si un usuario es admin o no',
  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 - inactivo, 1 - activo, 2 - eliminado',
  `referred_id` bigint(20) NOT NULL DEFAULT 1 COMMENT 'ID del usuario patrocinador',
  `binary_id` bigint(20) NOT NULL DEFAULT 1 COMMENT 'ID del usuario binario',
  `binary_side` enum('I','D') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Permite saber si esta en la derecha o izquierda en el binario',
  `binary_side_register` enum('I','D') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'I' COMMENT 'Permite saber porque lado va a registrar a un nuevo usuario',
  `dni` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_address` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photoDB` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `referred_id` bigint(20) UNSIGNED DEFAULT NULL,
  `orden_purchases_id` bigint(20) UNSIGNED DEFAULT NULL,
  `liquidation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `monto` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'entrada de cash',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - En espera, 1 - Pagado (liquidado), 2 - Cancelado',
  `tipo_transaction` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - comision, 1 - retiro',
  `liquidado` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - sin liquidar, 1 - liquidado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_binaries`
--

CREATE TABLE `wallet_binaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `referred_id` bigint(20) UNSIGNED DEFAULT NULL,
  `orden_purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `puntos_d` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'puntos del lado derecho',
  `puntos_i` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'puntos del lado izquierdo',
  `side` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - En espera, 1 - Pagado (liquidado), 2 - Cancelado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cierre_comisions`
--
ALTER TABLE `cierre_comisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cierre_comisions_package_id_foreign` (`package_id`);

--
-- Indices de la tabla `inversions`
--
ALTER TABLE `inversions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inversions_iduser_foreign` (`iduser`),
  ADD KEY `inversions_package_id_foreign` (`package_id`),
  ADD KEY `inversions_orden_id_foreign` (`orden_id`);

--
-- Indices de la tabla `liquidactions`
--
ALTER TABLE `liquidactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `liquidactions_iduser_foreign` (`iduser`);

--
-- Indices de la tabla `log_liquidations`
--
ALTER TABLE `log_liquidations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_liquidations_idliquidation_foreign` (`idliquidation`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden_purchases`
--
ALTER TABLE `orden_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_purchases_iduser_foreign` (`iduser`),
  ADD KEY `orden_purchases_package_id_foreign` (`package_id`);

--
-- Indices de la tabla `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `porcentaje_utilidads`
--
ALTER TABLE `porcentaje_utilidads`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_iduser_foreign` (`iduser`),
  ADD KEY `wallets_referred_id_foreign` (`referred_id`),
  ADD KEY `wallets_orden_purchases_id_foreign` (`orden_purchases_id`),
  ADD KEY `wallets_liquidation_id_foreign` (`liquidation_id`);

--
-- Indices de la tabla `wallet_binaries`
--
ALTER TABLE `wallet_binaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_binaries_iduser_foreign` (`iduser`),
  ADD KEY `wallet_binaries_referred_id_foreign` (`referred_id`),
  ADD KEY `wallet_binaries_orden_purchase_id_foreign` (`orden_purchase_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cierre_comisions`
--
ALTER TABLE `cierre_comisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inversions`
--
ALTER TABLE `inversions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidactions`
--
ALTER TABLE `liquidactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log_liquidations`
--
ALTER TABLE `log_liquidations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `orden_purchases`
--
ALTER TABLE `orden_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `porcentaje_utilidads`
--
ALTER TABLE `porcentaje_utilidads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `wallet_binaries`
--
ALTER TABLE `wallet_binaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cierre_comisions`
--
ALTER TABLE `cierre_comisions`
  ADD CONSTRAINT `cierre_comisions_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Filtros para la tabla `inversions`
--
ALTER TABLE `inversions`
  ADD CONSTRAINT `inversions_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `inversions_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `orden_purchases` (`id`),
  ADD CONSTRAINT `inversions_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Filtros para la tabla `liquidactions`
--
ALTER TABLE `liquidactions`
  ADD CONSTRAINT `liquidactions_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `log_liquidations`
--
ALTER TABLE `log_liquidations`
  ADD CONSTRAINT `log_liquidations_idliquidation_foreign` FOREIGN KEY (`idliquidation`) REFERENCES `liquidactions` (`id`);

--
-- Filtros para la tabla `orden_purchases`
--
ALTER TABLE `orden_purchases`
  ADD CONSTRAINT `orden_purchases_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orden_purchases_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Filtros para la tabla `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wallets_liquidation_id_foreign` FOREIGN KEY (`liquidation_id`) REFERENCES `liquidactions` (`id`),
  ADD CONSTRAINT `wallets_orden_purchases_id_foreign` FOREIGN KEY (`orden_purchases_id`) REFERENCES `orden_purchases` (`id`),
  ADD CONSTRAINT `wallets_referred_id_foreign` FOREIGN KEY (`referred_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `wallet_binaries`
--
ALTER TABLE `wallet_binaries`
  ADD CONSTRAINT `wallet_binaries_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wallet_binaries_orden_purchase_id_foreign` FOREIGN KEY (`orden_purchase_id`) REFERENCES `orden_purchases` (`id`),
  ADD CONSTRAINT `wallet_binaries_referred_id_foreign` FOREIGN KEY (`referred_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
