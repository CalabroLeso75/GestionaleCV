-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: db5018969364.hosting-data.io
-- Creato il: Feb 19, 2026 alle 17:06
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
-- Struttura della tabella `foreign_states`
--

DROP TABLE IF EXISTS `foreign_states`;
CREATE TABLE `foreign_states` (
  `id` bigint UNSIGNED NOT NULL,
  `name_it` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso2` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cadastral_code` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `foreign_states`
--

INSERT INTO `foreign_states` (`id`, `name_it`, `iso2`, `cadastral_code`, `valid_from`, `valid_to`, `slug`) VALUES
(1, 'Albania', 'AL', 'Z100', NULL, NULL, NULL),
(2, 'Andorra', 'AD', 'Z101', NULL, NULL, NULL),
(3, 'Austria', 'AT', 'Z102', NULL, NULL, NULL),
(4, 'Belgio', 'BE', 'Z103', NULL, NULL, NULL),
(5, 'Bulgaria', 'BG', 'Z104', NULL, NULL, NULL),
(6, 'Stato della Città del Vaticano', 'VA', 'Z106', NULL, NULL, NULL),
(7, 'Danimarca', 'DK', 'Z107', NULL, NULL, NULL),
(8, 'Isole Fær Øer', 'FO', 'Z108', NULL, NULL, NULL),
(9, 'Finlandia', 'FI', 'Z109', NULL, NULL, NULL),
(10, 'Francia', 'FR', 'Z110', NULL, NULL, NULL),
(11, 'Germania', 'DE', 'Z112', NULL, NULL, NULL),
(12, 'Gibilterra', 'GI', 'Z113', NULL, NULL, NULL),
(13, 'Regno Unito', 'UK', 'Z114', NULL, NULL, NULL),
(14, 'Grecia', 'GR', 'Z115', NULL, NULL, NULL),
(15, 'Irlanda', 'IE', 'Z116', NULL, NULL, NULL),
(16, 'Islanda', 'IS', 'Z117', NULL, NULL, NULL),
(17, 'Liechtenstein', 'LI', 'Z119', NULL, NULL, NULL),
(18, 'Lussemburgo', 'LU', 'Z120', NULL, NULL, NULL),
(19, 'Malta', 'MT', 'Z121', NULL, NULL, NULL),
(20, 'Isola di Man', 'IM', 'Z122', NULL, NULL, NULL),
(21, 'Monaco', 'MC', 'Z123', NULL, NULL, NULL),
(22, 'Norvegia', 'NO', 'Z125', NULL, NULL, NULL),
(23, 'Paesi Bassi', 'NL', 'Z126', NULL, NULL, NULL),
(24, 'Polonia', 'PL', 'Z127', NULL, NULL, NULL),
(25, 'Portogallo', 'PT', 'Z128', NULL, NULL, NULL),
(26, 'Romania', 'RO', 'Z129', NULL, NULL, NULL),
(27, 'San Marino', 'SM', 'Z130', NULL, NULL, NULL),
(28, 'Spagna', 'ES', 'Z131', NULL, NULL, NULL),
(29, 'Svezia', 'SE', 'Z132', NULL, NULL, NULL),
(30, 'Svizzera', 'CH', 'Z133', NULL, NULL, NULL),
(31, 'Ungheria', 'HU', 'Z134', NULL, NULL, NULL),
(32, 'Ucraina', 'UA', 'Z138', NULL, NULL, NULL),
(33, 'Bielorussia', 'BY', 'Z139', NULL, NULL, NULL),
(34, 'Moldova', 'MD', 'Z140', NULL, NULL, NULL),
(35, 'Estonia', 'EE', 'Z144', NULL, NULL, NULL),
(36, 'Lettonia', 'LV', 'Z145', NULL, NULL, NULL),
(37, 'Lituania', 'LT', 'Z146', NULL, NULL, NULL),
(38, 'Macedonia del Nord', 'MK', 'Z148', NULL, NULL, NULL),
(39, 'Croazia', 'HR', 'Z149', NULL, NULL, NULL),
(40, 'Slovenia', 'SI', 'Z150', NULL, NULL, NULL),
(41, 'Bosnia-Erzegovina', 'BA', 'Z153', NULL, NULL, NULL),
(42, 'Federazione russa', 'RU', 'Z154', NULL, NULL, NULL),
(43, 'Slovacchia', 'SK', 'Z155', NULL, NULL, NULL),
(44, 'Repubblica ceca', 'CZ', 'Z156', NULL, NULL, NULL),
(45, 'Serbia', 'RS', 'Z158', NULL, NULL, NULL),
(46, 'Montenegro', 'ME', 'Z159', NULL, NULL, NULL),
(47, 'Kosovo', 'n.d', 'Z160', NULL, NULL, NULL),
(48, 'Palestina', 'PS', 'Z161', NULL, NULL, NULL),
(49, 'Afghanistan', 'AF', 'Z200', NULL, NULL, NULL),
(50, 'Arabia Saudita', 'SA', 'Z203', NULL, NULL, NULL),
(51, 'Bahrein', 'BH', 'Z204', NULL, NULL, NULL),
(52, 'Bhutan', 'BT', 'Z205', NULL, NULL, NULL),
(53, 'Myanmar/Birmania', 'MM', 'Z206', NULL, NULL, NULL),
(54, 'Brunei Darussalam', 'BN', 'Z207', NULL, NULL, NULL),
(55, 'Cambogia', 'KH', 'Z208', NULL, NULL, NULL),
(56, 'Sri Lanka', 'LK', 'Z209', NULL, NULL, NULL),
(57, 'Cina', 'CN', 'Z210', NULL, NULL, NULL),
(58, 'Cipro', 'CY', 'Z211', NULL, NULL, NULL),
(59, 'Corea del Sud', 'KR', 'Z213', NULL, NULL, NULL),
(60, 'Corea del Nord', 'KP', 'Z214', NULL, NULL, NULL),
(61, 'Emirati Arabi Uniti', 'AE', 'Z215', NULL, NULL, NULL),
(62, 'Filippine', 'PH', 'Z216', NULL, NULL, NULL),
(63, 'Taiwan', 'TW', 'Z217', NULL, NULL, NULL),
(64, 'Giappone', 'JP', 'Z219', NULL, NULL, NULL),
(65, 'Giordania', 'JO', 'Z220', NULL, NULL, NULL),
(66, 'India', 'IN', 'Z222', NULL, NULL, NULL),
(67, 'Indonesia', 'ID', 'Z223', NULL, NULL, NULL),
(68, 'Iran', 'IR', 'Z224', NULL, NULL, NULL),
(69, 'Iraq', 'IQ', 'Z225', NULL, NULL, NULL),
(70, 'Israele', 'IL', 'Z226', NULL, NULL, NULL),
(71, 'Kuwait', 'KW', 'Z227', NULL, NULL, NULL),
(72, 'Laos', 'LA', 'Z228', NULL, NULL, NULL),
(73, 'Libano', 'LB', 'Z229', NULL, NULL, NULL),
(74, 'Maldive', 'MV', 'Z232', NULL, NULL, NULL),
(75, 'Mongolia', 'MN', 'Z233', NULL, NULL, NULL),
(76, 'Nepal', 'NP', 'Z234', NULL, NULL, NULL),
(77, 'Oman', 'OM', 'Z235', NULL, NULL, NULL),
(78, 'Pakistan', 'PK', 'Z236', NULL, NULL, NULL),
(79, 'Qatar', 'QA', 'Z237', NULL, NULL, NULL),
(80, 'Siria', 'SY', 'Z240', NULL, NULL, NULL),
(81, 'Thailandia', 'TH', 'Z241', NULL, NULL, NULL),
(82, 'Timor Leste', 'TL', 'Z242', NULL, NULL, NULL),
(83, 'Turchia', 'TR', 'Z243', NULL, NULL, NULL),
(84, 'Yemen', 'YE', 'Z246', NULL, NULL, NULL),
(85, 'Malaysia', 'MY', 'Z247', NULL, NULL, NULL),
(86, 'Singapore', 'SG', 'Z248', NULL, NULL, NULL),
(87, 'Bangladesh', 'BD', 'Z249', NULL, NULL, NULL),
(88, 'Vietnam', 'VN', 'Z251', NULL, NULL, NULL),
(89, 'Armenia', 'AM', 'Z252', NULL, NULL, NULL),
(90, 'Azerbaigian', 'AZ', 'Z253', NULL, NULL, NULL),
(91, 'Georgia', 'GE', 'Z254', NULL, NULL, NULL),
(92, 'Kazakhstan', 'KZ', 'Z255', NULL, NULL, NULL),
(93, 'Kirghizistan', 'KG', 'Z256', NULL, NULL, NULL),
(94, 'Tagikistan', 'TJ', 'Z257', NULL, NULL, NULL),
(95, 'Turkmenistan', 'TM', 'Z258', NULL, NULL, NULL),
(96, 'Uzbekistan', 'UZ', 'Z259', NULL, NULL, NULL),
(97, 'Namibia', 'NA', 'Z300', NULL, NULL, NULL),
(98, 'Algeria', 'DZ', 'Z301', NULL, NULL, NULL),
(99, 'Angola', 'AO', 'Z302', NULL, NULL, NULL),
(100, 'Burundi', 'BI', 'Z305', NULL, NULL, NULL),
(101, 'Camerun', 'CM', 'Z306', NULL, NULL, NULL),
(102, 'Capo Verde', 'CV', 'Z307', NULL, NULL, NULL),
(103, 'Repubblica Centrafricana', 'CF', 'Z308', NULL, NULL, NULL),
(104, 'Ciad', 'TD', 'Z309', NULL, NULL, NULL),
(105, 'Comore', 'KM', 'Z310', NULL, NULL, NULL),
(106, 'Congo', 'CG', 'Z311', NULL, NULL, NULL),
(107, 'Repubblica Democratica del Congo', 'CD', 'Z312', NULL, NULL, NULL),
(108, 'Costa d\'Avorio', 'CI', 'Z313', NULL, NULL, NULL),
(109, 'Benin', 'BJ', 'Z314', NULL, NULL, NULL),
(110, 'Etiopia', 'ET', 'Z315', NULL, NULL, NULL),
(111, 'Gabon', 'GA', 'Z316', NULL, NULL, NULL),
(112, 'Gambia', 'GM', 'Z317', NULL, NULL, NULL),
(113, 'Ghana', 'GH', 'Z318', NULL, NULL, NULL),
(114, 'Guinea', 'GN', 'Z319', NULL, NULL, NULL),
(115, 'Guinea-Bissau', 'GW', 'Z320', NULL, NULL, NULL),
(116, 'Guinea equatoriale', 'GQ', 'Z321', NULL, NULL, NULL),
(117, 'Kenya', 'KE', 'Z322', NULL, NULL, NULL),
(118, 'Liberia', 'LR', 'Z325', NULL, NULL, NULL),
(119, 'Libia', 'LY', 'Z326', NULL, NULL, NULL),
(120, 'Madagascar', 'MG', 'Z327', NULL, NULL, NULL),
(121, 'Malawi', 'MW', 'Z328', NULL, NULL, NULL),
(122, 'Mali', 'ML', 'Z329', NULL, NULL, NULL),
(123, 'Marocco', 'MA', 'Z330', NULL, NULL, NULL),
(124, 'Mauritania', 'MR', 'Z331', NULL, NULL, NULL),
(125, 'Maurizio', 'MU', 'Z332', NULL, NULL, NULL),
(126, 'Mozambico', 'MZ', 'Z333', NULL, NULL, NULL),
(127, 'Niger', 'NE', 'Z334', NULL, NULL, NULL),
(128, 'Nigeria', 'NG', 'Z335', NULL, NULL, NULL),
(129, 'Egitto', 'EG', 'Z336', NULL, NULL, NULL),
(130, 'Zimbabwe', 'ZW', 'Z337', NULL, NULL, NULL),
(131, 'Ruanda', 'RW', 'Z338', NULL, NULL, NULL),
(132, 'Sahara occidentale', 'EH', 'Z339', NULL, NULL, NULL),
(133, 'Sant\'Elena', 'SH', 'Z340', NULL, NULL, NULL),
(134, 'Sao Tomé e Principe', 'ST', 'Z341', NULL, NULL, NULL),
(135, 'Seychelles', 'SC', 'Z342', NULL, NULL, NULL),
(136, 'Senegal', 'SN', 'Z343', NULL, NULL, NULL),
(137, 'Sierra Leone', 'SL', 'Z344', NULL, NULL, NULL),
(138, 'Somalia', 'SO', 'Z345', NULL, NULL, NULL),
(139, 'Sudafrica', 'ZA', 'Z347', NULL, NULL, NULL),
(140, 'Sudan', 'SD', 'Z348', NULL, NULL, NULL),
(141, 'Eswatini', 'SZ', 'Z349', NULL, NULL, NULL),
(142, 'Togo', 'TG', 'Z351', NULL, NULL, NULL),
(143, 'Tunisia', 'TN', 'Z352', NULL, NULL, NULL),
(144, 'Uganda', 'UG', 'Z353', NULL, NULL, NULL),
(145, 'Burkina Faso', 'BF', 'Z354', NULL, NULL, NULL),
(146, 'Zambia', 'ZM', 'Z355', NULL, NULL, NULL),
(147, 'Tanzania', 'TZ', 'Z357', NULL, NULL, NULL),
(148, 'Botswana', 'BW', 'Z358', NULL, NULL, NULL),
(149, 'Lesotho', 'LS', 'Z359', NULL, NULL, NULL),
(150, 'Gibuti', 'DJ', 'Z361', NULL, NULL, NULL),
(151, 'Eritrea', 'ER', 'Z368', NULL, NULL, NULL),
(152, 'Bermuda', 'BM', 'Z400', NULL, NULL, NULL),
(153, 'Canada', 'CA', 'Z401', NULL, NULL, NULL),
(154, 'Groenlandia', 'GL', 'Z402', NULL, NULL, NULL),
(155, 'Saint Pierre e Miquelon', 'PM', 'Z403', NULL, NULL, NULL),
(156, 'Stati Uniti d\'America', 'US', 'Z404', NULL, NULL, NULL),
(157, 'Aruba', 'AW', 'Z501', NULL, NULL, NULL),
(158, 'Bahamas', 'BS', 'Z502', NULL, NULL, NULL),
(159, 'Costa Rica', 'CR', 'Z503', NULL, NULL, NULL),
(160, 'Cuba', 'CU', 'Z504', NULL, NULL, NULL),
(161, 'Repubblica Dominicana', 'DO', 'Z505', NULL, NULL, NULL),
(162, 'El Salvador', 'SV', 'Z506', NULL, NULL, NULL),
(163, 'Giamaica', 'JM', 'Z507', NULL, NULL, NULL),
(164, 'Guatemala', 'GT', 'Z509', NULL, NULL, NULL),
(165, 'Haiti', 'HT', 'Z510', NULL, NULL, NULL),
(166, 'Honduras', 'HN', 'Z511', NULL, NULL, NULL),
(167, 'Belize', 'BZ', 'Z512', NULL, NULL, NULL),
(168, 'Messico', 'MX', 'Z514', NULL, NULL, NULL),
(169, 'Nicaragua', 'NI', 'Z515', NULL, NULL, NULL),
(170, 'Panama', 'PA', 'Z516', NULL, NULL, NULL),
(171, 'Isole Turks e Caicos', 'TC', 'Z519', NULL, NULL, NULL),
(172, 'Barbados', 'BB', 'Z522', NULL, NULL, NULL),
(173, 'Grenada', 'GD', 'Z524', NULL, NULL, NULL),
(174, 'Isole Vergini britanniche', 'VG', 'Z525', NULL, NULL, NULL),
(175, 'Dominica', 'DM', 'Z526', NULL, NULL, NULL),
(176, 'Santa Lucia', 'LC', 'Z527', NULL, NULL, NULL),
(177, 'Saint Vincent e Grenadine', 'VC', 'Z528', NULL, NULL, NULL),
(178, 'Anguilla', 'AI', 'Z529', NULL, NULL, NULL),
(179, 'Isole Cayman', 'KY', 'Z530', NULL, NULL, NULL),
(180, 'Montserrat', 'MS', 'Z531', NULL, NULL, NULL),
(181, 'Antigua e Barbuda', 'AG', 'Z532', NULL, NULL, NULL),
(182, 'Saint Kitts e Nevis', 'KN', 'Z533', NULL, NULL, NULL),
(183, 'Argentina', 'AR', 'Z600', NULL, NULL, NULL),
(184, 'Bolivia', 'BO', 'Z601', NULL, NULL, NULL),
(185, 'Brasile', 'BR', 'Z602', NULL, NULL, NULL),
(186, 'Cile', 'CL', 'Z603', NULL, NULL, NULL),
(187, 'Colombia', 'CO', 'Z604', NULL, NULL, NULL),
(188, 'Ecuador', 'EC', 'Z605', NULL, NULL, NULL),
(189, 'Guyana', 'GY', 'Z606', NULL, NULL, NULL),
(190, 'Suriname', 'SR', 'Z608', NULL, NULL, NULL),
(191, 'Isole Falkland (Malvine)', 'FK', 'Z609', NULL, NULL, NULL),
(192, 'Paraguay', 'PY', 'Z610', NULL, NULL, NULL),
(193, 'Perù', 'PE', 'Z611', NULL, NULL, NULL),
(194, 'Trinidad e Tobago', 'TT', 'Z612', NULL, NULL, NULL),
(195, 'Uruguay', 'UY', 'Z613', NULL, NULL, NULL),
(196, 'Venezuela', 'VE', 'Z614', NULL, NULL, NULL),
(197, 'Australia', 'AU', 'Z700', NULL, NULL, NULL),
(198, 'Isole Cook (NZ)', 'CK', 'Z703', NULL, NULL, NULL),
(199, 'Figi', 'FJ', 'Z704', NULL, NULL, NULL),
(200, 'Isole Marshall', 'MH', 'Z711', NULL, NULL, NULL),
(201, 'Nauru', 'NR', 'Z713', NULL, NULL, NULL),
(202, 'Nuova Caledonia', 'NC', 'Z716', NULL, NULL, NULL),
(203, 'Nuova Zelanda', 'NZ', 'Z719', NULL, NULL, NULL),
(204, 'Isole Pitcairn', 'PN', 'Z722', NULL, NULL, NULL),
(205, 'Polinesia francese', 'PF', 'Z723', NULL, NULL, NULL),
(206, 'Isole Salomone', 'SB', 'Z724', NULL, NULL, NULL),
(207, 'Samoa', 'WS', 'Z726', NULL, NULL, NULL),
(208, 'Tonga', 'TO', 'Z728', NULL, NULL, NULL),
(209, 'Wallis e Futuna', 'WF', 'Z729', NULL, NULL, NULL),
(210, 'Papua Nuova Guinea', 'PG', 'Z730', NULL, NULL, NULL),
(211, 'Kiribati', 'KI', 'Z731', NULL, NULL, NULL),
(212, 'Tuvalu', 'TV', 'Z732', NULL, NULL, NULL),
(213, 'Vanuatu', 'VU', 'Z733', NULL, NULL, NULL),
(214, 'Palau', 'PW', 'Z734', NULL, NULL, NULL),
(215, 'Stati Federati di Micronesia', 'FM', 'Z735', NULL, NULL, NULL),
(216, 'Sud Sudan', 'SS', 'Z907', NULL, NULL, NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `foreign_states`
--
ALTER TABLE `foreign_states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fs_cadastral` (`cadastral_code`),
  ADD UNIQUE KEY `uq_fs_name` (`name_it`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `foreign_states`
--
ALTER TABLE `foreign_states`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
