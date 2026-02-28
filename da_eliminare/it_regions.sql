-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: db5018969364.hosting-data.io
-- Creato il: Feb 19, 2026 alle 17:08
-- Versione del server: 8.0.36
-- Versione PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbs14943233`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `it_regions`
--

DROP TABLE IF EXISTS `it_regions`;
CREATE TABLE `it_regions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `istat_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `it_regions`
--

INSERT INTO `it_regions` (`id`, `name`, `istat_code`, `created_at`) VALUES
(1, 'Piemonte', NULL, '2025-11-09 17:41:20'),
(2, 'Valle d\'Aosta/Vallée d\'Aoste', NULL, '2025-11-09 17:41:22'),
(3, 'Lombardia', NULL, '2025-11-09 17:41:22'),
(4, 'Trentino-Alto Adige/Südtirol', NULL, '2025-11-09 17:41:25'),
(5, 'Veneto', NULL, '2025-11-09 17:41:26'),
(6, 'Friuli-Venezia Giulia', NULL, '2025-11-09 17:41:27'),
(7, 'Liguria', NULL, '2025-11-09 17:41:27'),
(8, 'Emilia-Romagna', NULL, '2025-11-09 17:41:28'),
(9, 'Toscana', NULL, '2025-11-09 17:41:29'),
(10, 'Umbria', NULL, '2025-11-09 17:41:29'),
(11, 'Marche', NULL, '2025-11-09 17:41:29'),
(12, 'Lazio', NULL, '2025-11-09 17:41:30'),
(13, 'Abruzzo', NULL, '2025-11-09 17:41:31'),
(14, 'Molise', NULL, '2025-11-09 17:41:31'),
(15, 'Campania', NULL, '2025-11-09 17:41:32'),
(16, 'Puglia', NULL, '2025-11-09 17:41:33'),
(17, 'Basilicata', NULL, '2025-11-09 17:41:33'),
(18, 'Calabria', NULL, '2025-11-09 17:41:33'),
(19, 'Sicilia', NULL, '2025-11-09 17:41:34'),
(20, 'Sardegna', NULL, '2025-11-09 17:41:35');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `it_regions`
--
ALTER TABLE `it_regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_it_regions_name` (`name`),
  ADD KEY `idx_it_regions_istat` (`istat_code`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `it_regions`
--
ALTER TABLE `it_regions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
