-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: db5018969364.hosting-data.io
-- Creato il: Feb 19, 2026 alle 17:07
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
-- Struttura della tabella `it_provinces`
--

DROP TABLE IF EXISTS `it_provinces`;
CREATE TABLE `it_provinces` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `istat_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `it_provinces`
--

INSERT INTO `it_provinces` (`id`, `name`, `short_code`, `istat_code`) VALUES
(1, 'Torino', 'TO', NULL),
(2, 'Vercelli', 'VC', NULL),
(3, 'Novara', 'NO', NULL),
(4, 'Cuneo', 'CN', NULL),
(5, 'Asti', 'AT', NULL),
(6, 'Alessandria', 'AL', NULL),
(7, 'Biella', 'BI', NULL),
(8, 'Verbano-Cusio-Ossola', 'VB', NULL),
(9, 'Valle d\'Aosta/Vallée d\'Aoste', 'AO', NULL),
(10, 'Varese', 'VA', NULL),
(11, 'Como', 'CO', NULL),
(12, 'Sondrio', 'SO', NULL),
(13, 'Milano', 'MI', NULL),
(14, 'Bergamo', 'BG', NULL),
(15, 'Brescia', 'BS', NULL),
(16, 'Pavia', 'PV', NULL),
(17, 'Cremona', 'CR', NULL),
(18, 'Mantova', 'MN', NULL),
(19, 'Lecco', 'LC', NULL),
(20, 'Lodi', 'LO', NULL),
(21, 'Monza e della Brianza', 'MB', NULL),
(22, 'Bolzano/Bozen', 'BZ', NULL),
(23, 'Trento', 'TN', NULL),
(24, 'Verona', 'VR', NULL),
(25, 'Vicenza', 'VI', NULL),
(26, 'Belluno', 'BL', NULL),
(27, 'Treviso', 'TV', NULL),
(28, 'Venezia', 'VE', NULL),
(29, 'Padova', 'PD', NULL),
(30, 'Rovigo', 'RO', NULL),
(31, 'Udine', 'UD', NULL),
(32, 'Gorizia', 'GO', NULL),
(33, 'Trieste', 'TS', NULL),
(34, 'Pordenone', 'PN', NULL),
(35, 'Imperia', 'IM', NULL),
(36, 'Savona', 'SV', NULL),
(37, 'Genova', 'GE', NULL),
(38, 'La Spezia', 'SP', NULL),
(39, 'Piacenza', 'PC', NULL),
(40, 'Parma', 'PR', NULL),
(41, 'Reggio nell\'Emilia', 'RE', NULL),
(42, 'Modena', 'MO', NULL),
(43, 'Bologna', 'BO', NULL),
(44, 'Ferrara', 'FE', NULL),
(45, 'Ravenna', 'RA', NULL),
(46, 'Forlì-Cesena', 'FC', NULL),
(47, 'Rimini', 'RN', NULL),
(48, 'Massa-Carrara', 'MS', NULL),
(49, 'Lucca', 'LU', NULL),
(50, 'Pistoia', 'PT', NULL),
(51, 'Firenze', 'FI', NULL),
(52, 'Livorno', 'LI', NULL),
(53, 'Pisa', 'PI', NULL),
(54, 'Arezzo', 'AR', NULL),
(55, 'Siena', 'SI', NULL),
(56, 'Grosseto', 'GR', NULL),
(57, 'Prato', 'PO', NULL),
(58, 'Perugia', 'PG', NULL),
(59, 'Terni', 'TR', NULL),
(60, 'Pesaro e Urbino', 'PU', NULL),
(61, 'Ancona', 'AN', NULL),
(62, 'Macerata', 'MC', NULL),
(63, 'Ascoli Piceno', 'AP', NULL),
(64, 'Fermo', 'FM', NULL),
(65, 'Viterbo', 'VT', NULL),
(66, 'Rieti', 'RI', NULL),
(67, 'Roma', 'RM', NULL),
(68, 'Latina', 'LT', NULL),
(69, 'Frosinone', 'FR', NULL),
(70, 'L\'Aquila', 'AQ', NULL),
(71, 'Teramo', 'TE', NULL),
(72, 'Pescara', 'PE', NULL),
(73, 'Chieti', 'CH', NULL),
(74, 'Campobasso', 'CB', NULL),
(75, 'Isernia', 'IS', NULL),
(76, 'Caserta', 'CE', NULL),
(77, 'Benevento', 'BN', NULL),
(78, 'Napoli', 'NA', NULL),
(79, 'Avellino', 'AV', NULL),
(80, 'Salerno', 'SA', NULL),
(81, 'Foggia', 'FG', NULL),
(82, 'Bari', 'BA', NULL),
(83, 'Taranto', 'TA', NULL),
(84, 'Brindisi', 'BR', NULL),
(85, 'Lecce', 'LE', NULL),
(86, 'Barletta-Andria-Trani', 'BT', NULL),
(87, 'Potenza', 'PZ', NULL),
(88, 'Matera', 'MT', NULL),
(89, 'Cosenza', 'CS', NULL),
(90, 'Catanzaro', 'CZ', NULL),
(91, 'Reggio Calabria', 'RC', NULL),
(92, 'Crotone', 'KR', NULL),
(93, 'Vibo Valentia', 'VV', NULL),
(94, 'Trapani', 'TP', NULL),
(95, 'Palermo', 'PA', NULL),
(96, 'Messina', 'ME', NULL),
(97, 'Agrigento', 'AG', NULL),
(98, 'Caltanissetta', 'CL', NULL),
(99, 'Enna', 'EN', NULL),
(100, 'Catania', 'CT', NULL),
(101, 'Ragusa', 'RG', NULL),
(102, 'Siracusa', 'SR', NULL),
(103, 'Sassari', 'SS', NULL),
(104, 'Nuoro', 'NU', NULL),
(105, 'Cagliari', 'CA', NULL),
(106, 'Oristano', 'OR', NULL),
(107, 'Sud Sardegna', 'SU', NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `it_provinces`
--
ALTER TABLE `it_provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_itprov_code` (`istat_code`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `it_provinces`
--
ALTER TABLE `it_provinces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
