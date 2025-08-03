-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 02:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookingapp_empty`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `PositionTitle` varchar(50) NOT NULL,
  `Institution` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `UserID`, `PositionTitle`, `Institution`) VALUES
(1, 1, 'Director', 'Housing Management'),
(2, 29, '', ''),
(3, 31, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `auth_methods`
--

CREATE TABLE `auth_methods` (
  `AuthID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Provider` enum('google','facebook','apple') NOT NULL,
  `ProviderID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `establishment`
--

CREATE TABLE `establishment` (
  `EstablishmentID` int(11) NOT NULL,
  `OwnerID` int(11) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `NoOfFloors` int(11) DEFAULT NULL,
  `HouseRules` longtext DEFAULT NULL,
  `Status` enum('pending','available','unavailable','removed','disapproved') DEFAULT 'pending',
  `Remark` varchar(100) DEFAULT NULL,
  `Type` enum('Dormitory','Cottage','Apartment','Boarding House','Hotel','Motel') DEFAULT NULL,
  `GenderInclusiveness` enum('Males only','Females only','Coed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `establishment`
--

INSERT INTO `establishment` (`EstablishmentID`, `OwnerID`, `Name`, `Description`, `Address`, `CreatedAt`, `NoOfFloors`, `HouseRules`, `Status`, `Remark`, `Type`, `GenderInclusiveness`) VALUES
(1, 1, 'Rajah Solaiman Hall', '                                                                ', NULL, '2025-01-21 23:53:39', 2, '                1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\n     hall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n            ', 'available', NULL, 'Dormitory', 'Males only'),
(2, 2, 'Rajah Dumduma Hall', '                                                                ', NULL, '2025-01-22 00:10:00', 2, '                                            Avoid Littering\\r\\nBe mindful of noise\\r\\nConserve water usage            \\r\\n', 'removed', NULL, 'Dormitory', 'Males only'),
(3, 4, 'Bolawan Hall', '                                                                                                                                ', NULL, '2025-01-22 00:14:30', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Females only'),
(4, 3, 'Torogan Hall', '', NULL, '2025-01-22 00:18:34', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Coed'),
(5, 5, 'Princess Lawanen Hall (Southwing)', '', NULL, '2025-01-22 00:22:29', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Females only'),
(6, 6, 'Princess Lawanen Hall (Northwing)', '', NULL, '2025-01-22 00:26:04', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Females only'),
(7, 7, 'Girls Dormitory', '', NULL, '2025-01-22 00:29:02', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Females only'),
(8, 8, 'Boys Dormitory', '', NULL, '2025-01-22 00:32:01', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n            ', 'available', NULL, 'Dormitory', 'Males only'),
(9, 9, 'Rajah Indarapatra Hall (Southwing)', '                                                                ', NULL, '2025-01-22 00:35:19', 2, NULL, 'removed', NULL, 'Dormitory', 'Females only'),
(10, 9, 'Rajah Indarapatra Hall (Southwing)', '', NULL, '2025-01-22 00:36:02', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Females only'),
(11, 10, 'Rajah Indarapatra Hall (Northwing)', '', NULL, '2025-01-22 00:47:06', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Females only'),
(12, 11, 'Haranaya Cottage', '', NULL, '2025-01-22 00:50:44', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Cottage', 'Coed'),
(13, 12, 'UnKnown Cottage', '', NULL, '2025-01-22 00:54:33', 3, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Cottage', 'Males only'),
(14, 2, 'Rajah Dumduma Hall', 'Boy\\\'s Dormitory near engineering                     ', NULL, '2025-01-27 21:00:21', 2, NULL, 'removed', NULL, 'Dormitory', 'Males only'),
(15, 2, 'Rajah Dumduma Hall', '                                                    ffgfgftf            ', NULL, '2025-01-27 21:01:10', 2, NULL, 'removed', NULL, 'Dormitory', 'Males only'),
(16, 2, 'Rajah Dumduma Hall', '                                                                                                                                ', 'Near College of Engineering', '2025-01-27 22:40:23', 2, '1. The residents should respect the peace and privacy of his/her co-residents and observe proper decorum.\\r\\n2. The residents should cooperate with the residence hall management in maintaining the cleanliness and \\r\\norderliness of the dormitory/hall.\\r\\n3. The residents should use toilet and bathroom properly.\\r\\n4. The residents shall be held liable for any damage done to the facilities issued to them and the furnishing of the \\r\\nhall.\\r\\n5. Study and visiting hours shall be observed at all times.\\r\\n6. Residents must strictly observe curfew hours which begin at 7:00PM and ends at 5:AM.\\r\\n', 'available', NULL, 'Dormitory', 'Males only'),
(17, 7, 'namar dormitory', '', NULL, '2025-01-30 11:42:34', 5, NULL, 'available', NULL, 'Dormitory', 'Males only'),
(19, 2, 'Innovation Cottage', '', 'Commercial Center, near Fratisco', '2025-02-01 11:29:56', 4, NULL, 'available', NULL, 'Cottage', 'Coed'),
(20, 1, 'Innovation Cottage', 'With Fashion Trend', 'Comcent', '2025-02-15 17:40:44', 5, NULL, 'available', NULL, 'Cottage', 'Coed'),
(21, 2, 'HK', '', '5th Street', '2025-02-24 01:00:48', 2, NULL, 'available', NULL, 'Dormitory', 'Females only'),
(22, 6, 'denr', 'mapiya ago maaliwalas', 'pyagma ingud', '2025-03-13 00:48:48', 15, NULL, 'available', NULL, 'Dormitory', 'Coed'),
(23, 6, 'JK', 'JKJKJK', 'rapasun', '2025-06-04 01:00:37', 3, NULL, 'pending', NULL, 'Dormitory', 'Males only');

-- --------------------------------------------------------

--
-- Table structure for table `establishment_features`
--

CREATE TABLE `establishment_features` (
  `Code` int(11) NOT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `FeatureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `establishment_features`
--

INSERT INTO `establishment_features` (`Code`, `EstablishmentID`, `FeatureID`) VALUES
(12, 12, 26),
(13, 12, 50),
(14, 12, 64),
(15, 12, 36),
(16, 12, 13),
(19, 12, 89),
(20, 12, 41),
(21, 12, 84),
(22, 12, 77),
(23, 12, 37),
(24, 12, 14),
(25, 1, 23),
(27, 1, 14),
(28, 1, 77),
(29, 1, 64),
(30, 1, 50),
(34, 1, 65),
(35, 2, 14),
(38, 2, 37),
(39, 2, 54),
(41, 2, 51),
(43, 2, 64),
(44, 2, 65),
(45, 4, 51),
(46, 4, 64),
(47, 4, 65),
(48, 4, 14),
(50, 3, 23),
(51, 3, 55),
(53, 3, 37),
(55, 3, 64),
(56, 3, 65),
(57, 3, 14),
(58, 5, 14),
(59, 5, 37),
(60, 5, 47),
(64, 5, 64),
(65, 5, 49),
(66, 5, 65),
(69, 6, 64),
(70, 6, 14),
(71, 6, 51),
(72, 6, 37),
(73, 7, 14),
(74, 7, 37),
(75, 7, 47),
(77, 7, 65),
(79, 7, 51),
(80, 7, 64),
(81, 7, 78),
(82, 15, 64),
(84, 15, 55),
(85, 15, 47),
(86, 15, 51),
(87, 15, 50),
(88, 15, 89),
(89, 15, 77),
(90, 15, 65),
(91, 16, 14),
(92, 16, 47),
(94, 16, 89),
(95, 16, 64),
(97, 16, 51),
(98, 16, 50),
(99, 16, 55),
(100, 16, 65),
(101, 4, 55),
(102, 4, 89),
(103, 8, 14),
(104, 8, 37),
(105, 8, 54),
(106, 8, 47),
(107, 8, 48),
(108, 8, 65),
(109, 8, 78),
(110, 8, 89),
(111, 8, 64),
(112, 10, 14),
(113, 10, 55),
(114, 10, 51),
(115, 10, 64),
(116, 11, 14),
(117, 11, 47),
(119, 11, 55),
(120, 11, 51),
(121, 11, 64),
(122, 11, 89),
(123, 11, 65),
(124, 13, 14),
(125, 13, 55),
(126, 13, 64),
(128, 13, 48),
(129, 13, 47),
(131, 21, 41),
(132, 21, 14);

-- --------------------------------------------------------

--
-- Table structure for table `establishment_owner`
--

CREATE TABLE `establishment_owner` (
  `OwnerID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `PositionTitle` varchar(50) DEFAULT NULL,
  `Institution` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `establishment_owner`
--

INSERT INTO `establishment_owner` (`OwnerID`, `UserID`, `PositionTitle`, `Institution`) VALUES
(1, 2, NULL, NULL),
(2, 3, '', ''),
(3, 4, '', ''),
(4, 16, '', ''),
(5, 17, '', ''),
(6, 18, '', ''),
(7, 19, '', ''),
(8, 20, '', ''),
(9, 21, '', ''),
(10, 22, '', ''),
(11, 23, '', ''),
(12, 24, '', ''),
(13, 28, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `establishment_payment_channel`
--

CREATE TABLE `establishment_payment_channel` (
  `EPCID` int(11) NOT NULL,
  `EstablishmentID` int(11) NOT NULL,
  `PaymentChannel` int(11) DEFAULT NULL,
  `AccountNumber` varchar(50) DEFAULT NULL,
  `AccountName` varchar(100) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `IsHidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `establishment_photos`
--

CREATE TABLE `establishment_photos` (
  `PhotoID` int(11) NOT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `Photo1` text NOT NULL,
  `Description1` text DEFAULT NULL,
  `Photo2` text DEFAULT NULL,
  `Description2` text DEFAULT NULL,
  `Photo3` text DEFAULT NULL,
  `Description3` text DEFAULT NULL,
  `Photo4` text DEFAULT NULL,
  `Description4` text DEFAULT NULL,
  `Photo5` text DEFAULT NULL,
  `Description5` text DEFAULT NULL,
  `Photo6` text NOT NULL,
  `Description6` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `establishment_photos`
--

INSERT INTO `establishment_photos` (`PhotoID`, `EstablishmentID`, `Photo1`, `Description1`, `Photo2`, `Description2`, `Photo3`, `Description3`, `Photo4`, `Description4`, `Photo5`, `Description5`, `Photo6`, `Description6`) VALUES
(1, 1, 'est_photos/Photo_1_1_012820252354.jpg', 'Front Door', 'est_photos/Photo_2_1_012820252354.jpg', 'Entrance Gate', 'est_photos/Photo_3_1_012820252354.jpg', '', 'est_photos/Photo_4_1_012820252354.jpg', '', 'est_photos/Photo_5_1_012820252355.jpg', '', 'est_photos/Photo_6_1_013020250354.jpg', 'Backdoor'),
(8, 2, 'est_photos/Photo_1_2_012220250910.jpg', '', 'est_photos/Photo_2_2_012220250910.jpg', '', 'est_photos/Photo_3_2_012220250910.jpg', '', 'est_photos/Photo_4_2_012220250910.jpg', '', 'est_photos/Photo_5_2_012220250911.jpg', '', '', ''),
(13, 3, 'est_photos/Photo_1_3_012920250003.jpg', '', 'est_photos/Photo_2_3_012920250003.jpg', '', 'est_photos/Photo_3_3_012920250003.jpg', '', 'est_photos/Photo_4_3_012920250004.jpg', '', 'est_photos/Photo_5_3_012920250004.jpg', '', 'est_photos/Photo_6_3_013020250447.jpg', ''),
(16, 4, 'est_photos/Photo_1_4_012820252342.jpg', '', 'est_photos/Photo_2_4_012820252343.jpg', '', 'est_photos/Photo_3_4_012820252343.jpg', '', 'est_photos/Photo_4_4_012820252343.jpg', '', 'est_photos/Photo_5_4_012820252343.jpg', '', 'est_photos/Photo_6_4_013020250434.jpg', ''),
(19, 5, 'est_photos/Photo_1_5_013020250448.jpg', '', 'est_photos/Photo_2_5_013020250448.jpg', '', 'est_photos/Photo_3_5_013020250448.jpg', '', NULL, NULL, NULL, NULL, '', ''),
(22, 6, 'est_photos/Photo_1_6_012920250152.jpg', '', 'est_photos/Photo_2_6_012920250152.jpg', '', 'est_photos/Photo_3_6_012920250152.jpg', '', NULL, NULL, NULL, NULL, '', ''),
(25, 7, 'est_photos/Photo_1_7_013020250523.jpg', '', 'est_photos/Photo_2_7_013020250523.jpg', '', 'est_photos/Photo_3_7_013020250523.jpg', '', 'est_photos/Photo_4_7_013020250524.jpg', '', 'est_photos/Photo_5_7_013020250524.jpg', '', 'est_photos/Photo_6_7_013020250524.jpg', ''),
(29, 8, 'est_photos/Photo_1_8_013020250527.jpg', '', 'est_photos/Photo_2_8_013020250527.jpg', '', 'est_photos/Photo_3_8_013020250528.jpg', '', 'est_photos/Photo_4_8_013020250528.jpg', '', NULL, NULL, '', ''),
(33, 10, 'est_photos/Photo_1_10_013020250533.jpg', '', 'est_photos/Photo_2_10_013020250533.jpg', '', 'est_photos/Photo_3_10_013020250533.jpg', '', NULL, NULL, NULL, NULL, '', ''),
(37, 11, 'est_photos/Photo_1_11_013020250547.jpg', '', 'est_photos/Photo_2_11_013020250547.jpg', '', 'est_photos/Photo_3_11_013020250547.jpg', '', NULL, NULL, NULL, NULL, '', ''),
(40, 15, 'est_photos/Photo_1_15_012820250601.jpg', '', 'est_photos/Photo_2_15_012820250601.jpg', '', 'est_photos/Photo_3_15_012820250602.jpg', '', 'est_photos/Photo_4_15_012820250602.jpg', '', NULL, NULL, '', ''),
(44, 16, 'est_photos/Photo_1_16_013020250503.jpg', 'Front Door', NULL, NULL, 'est_photos/Photo_3_16_012820252255.jpg', '', NULL, NULL, 'est_photos/Photo_5_16_012820252255.jpg', '', 'est_photos/Photo_6_16_013020250421.jpg', 'Backdoor'),
(76, 12, 'est_photos/Photo_1_12_013020250604.jpg', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(103, 13, 'est_photos/Photo_1_13_013020250609.jpg', '', 'est_photos/Photo_2_13_013020250609.jpg', '', NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(106, 17, 'est_photos/Photo_1_17_013020252046.jpg', 'front gate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(109, 21, 'est_photos/Photo_1_21_022420251001.jpg', '', 'est_photos/Photo_2_21_022420251011.jpg', '', 'est_photos/Photo_3_21_022420251011.jpg', '', 'est_photos/Photo_4_21_022420251011.webp', '', 'est_photos/Photo_5_21_022420251012.jpg', '', 'est_photos/Photo_6_21_022420251012.jpg', '');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `FeatureID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Icon` varchar(100) NOT NULL,
  `Code` varchar(30) NOT NULL,
  `Category` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`FeatureID`, `Name`, `Icon`, `Code`, `Category`) VALUES
(1, 'Double-deck bed', 'bed', 'bed', 'room'),
(2, 'Single-deck bed', 'bed', 'Single-deck bed', 'building'),
(3, 'Chairs', 'chair', 'Chairs', 'building'),
(4, 'Couches', 'couch', 'Couches', 'building'),
(5, 'Cabinets', 'box-archive', 'Cabinets', 'building'),
(6, 'Storage space', 'door-open', 'Storage space', 'building'),
(7, 'Laundry area', 'jug-detergent', 'Laundry area', 'building'),
(8, 'Kitchen', 'kitchen-set', 'Kitchen', 'building'),
(9, 'Shared bathroom', 'restroom', 'Shared bathroom', 'room'),
(10, 'Private bathroom', 'restroom', 'Private bathroom', 'room'),
(11, 'Shower', 'shower', 'Shower', 'building'),
(12, 'Shared sink', 'sink', 'Shared sink', 'building'),
(13, 'Private sink', 'sink', 'Private sink', 'building'),
(14, 'CCTV cameras', 'video', 'CCTV cameras', 'building'),
(19, 'Gas stoove', 'fire-burner', 'Gas stoove', 'building'),
(20, 'Free Wi-Fi', 'wifi', 'wifi', 'room'),
(21, 'Free water supply', 'droplet', 'Free water supply', 'building'),
(22, 'Free electricity', 'bolt', 'Free electricity', 'building'),
(23, 'Air conditioning', 'wind', 'Air conditioning', 'room'),
(24, 'Ceiling fan', 'fan', 'Ceiling fan', 'building'),
(25, 'Stand fan', 'fan', 'Stand fan', 'building'),
(26, 'Wall fan', 'fan', 'Wall fan', 'building'),
(27, 'Study area', 'book-open-reader', 'Study area', 'building'),
(28, 'Common room', 'people-roof', 'Common room', 'building'),
(29, 'Flat-screen TV', 'tv', 'Flat-screen TV', 'building'),
(30, 'TV', 'tv', 'TV', 'building'),
(31, 'Gaming console', 'gamepad', 'Gaming console', 'building'),
(32, 'Karaoke area', 'microphone', 'Karaoke area', 'building'),
(33, 'Gym area', 'dumbbell', 'Gym area', 'building'),
(34, 'Outdoor recreational space', 'shoe-prints', 'Outdoor recreational space', 'building'),
(35, 'Parking space', 'square-parking', 'Parking space', 'building'),
(36, 'Sari-sari store', 'store', 'Sari-sari store', 'building'),
(37, 'Convenience store', 'store', 'Convenience store', 'building'),
(38, 'Garden area', 'leaf', 'Garden area', 'building'),
(39, 'Rooftop', 'hotel', 'Rooftop', 'building'),
(40, 'Terraces', 'hotel', 'Terraces', 'building'),
(41, 'Cats', 'cat', 'Cats', 'building'),
(42, 'Dogs', 'dog', 'Dogs', 'building'),
(43, 'Disability support', 'wheelchair', 'Disability support', 'building'),
(44, 'Elevator', 'elevator', 'Elevator', 'building'),
(45, 'Clinic', 'kit-medical', 'Clinic', 'building'),
(46, 'Pharmacy', 'kit-medical', 'Pharmacy', 'building'),
(47, 'Emergency responsive', 'fire-extinguisher', 'Emergency responsive', 'building'),
(48, 'Fire extinguisher', 'fire-extinguisher', 'Fire extinguisher', 'building'),
(49, 'Pesonet Wi-Fi', 'wifi', 'Pesonet Wi-Fi', 'building'),
(50, 'Ticket Wi-Fi', 'wifi', 'Ticket Wi-Fi', 'building'),
(51, 'Strong cell signal', 'tower-broadcast', 'Strong cell signal', 'building'),
(52, 'Trash collection', 'trash-can', 'Trash collection', 'building'),
(53, 'Smart home features', 'house-signal', 'Smart home features', 'building'),
(54, 'Cool temperature', 'fan', 'Cool temperature', 'building'),
(55, 'Warm temperature', 'wind', 'Warm temperature', 'building'),
(56, 'Hot temperature', 'temperature-full', 'Hot temperature', 'building'),
(57, 'Printing area', 'print', 'Printing area', 'building'),
(58, 'Photocopying area', 'copy', 'Photocopying area', 'building'),
(59, 'Computer shop', 'computer', 'Computer shop', 'building'),
(60, 'Library', 'book', 'Library', 'building'),
(61, 'Online portal', 'globe', 'Online portal', 'building'),
(62, 'Online payment', 'money-check', 'Online payment', 'building'),
(63, 'Pool', 'person-swimming', 'Pool', 'building'),
(64, 'Security personnel', 'user-shield', 'Security personnel', 'building'),
(65, 'Janitorial personnel', 'broom', 'Janitorial personnel', 'building'),
(66, 'Dining area', 'utensils', 'Dining area', 'building'),
(67, 'Cafeteria', 'utensils', 'Cafeteria', 'building'),
(68, 'Restaurant', 'utensils', 'Restaurant', 'building'),
(69, 'Canteen', 'utensils', 'Canteen', 'building'),
(70, 'Basketball court', 'basketball', 'Basketball court', 'building'),
(71, 'Volleyball court', 'volleyball', 'Volleyball court', 'building'),
(72, 'Table tennis facility', 'table-tennis-paddle-ball', 'Table tennis facility', 'building'),
(73, 'Bike storage', 'person-biking', 'Bike storage', 'building'),
(74, 'Church', 'church', 'Church', 'building'),
(75, 'Christian worship place', 'cross', 'Christian worship place', 'building'),
(76, 'Bible', 'book-bible', 'Bible', 'building'),
(77, 'Mosque', 'mosque', 'Mosque', 'building'),
(78, 'Muslim prayer area', 'star-and-crescent', 'Muslim prayer area', 'building'),
(79, 'Quran', 'book-quran', 'Quran', 'building'),
(80, 'Spa', 'spa', 'Spa', 'building'),
(81, 'Sauna', 'hot-tub-person', 'Sauna', 'building'),
(82, 'Transport convenience', 'car', 'Transport convenience', 'building'),
(83, 'Smoking area', 'Smoking', 'Smoking area', 'building'),
(84, 'No smoking', 'ban-smoking', 'No smoking', 'building'),
(85, 'Quiet', 'volume-off', 'Quiet', 'building'),
(87, 'Theme-based', 'masks-theater', 'Theme-based', 'building'),
(88, 'For honors', 'award', 'For honors', 'building'),
(89, 'Plug outlets', 'plug-circle-bolt', 'Plug outlets', 'building');

-- --------------------------------------------------------

--
-- Table structure for table `geo_tags`
--

CREATE TABLE `geo_tags` (
  `GeoTagID` int(11) NOT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `Latitude` decimal(10,8) NOT NULL,
  `Longitude` decimal(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `geo_tags`
--

INSERT INTO `geo_tags` (`GeoTagID`, `EstablishmentID`, `Latitude`, `Longitude`) VALUES
(1, 1, 7.99500230, 124.26102787),
(2, 2, 7.99449763, 124.26078111),
(3, 4, 7.99491730, 124.26192373),
(4, 3, 7.99475793, 124.26151067),
(5, 5, 7.99771155, 124.25756246),
(6, 6, 7.99763087, 124.25752314),
(7, 7, 7.99475262, 124.26001400),
(8, 8, 7.99525197, 124.26009983),
(9, 10, 8.00004362, 124.25816864),
(10, 11, 8.00031454, 124.25834566),
(11, 12, 7.99441263, 124.25461203),
(12, 13, 7.99575664, 124.25566882),
(13, 16, 7.99454372, 124.26086247),
(14, 21, 7.99724965, 124.25564390),
(16, 22, 11.63780541, 123.72058721);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `OTP_ID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Purpose` enum('email','contact') DEFAULT NULL,
  `OTP_Code` char(6) DEFAULT NULL,
  `ExpiresAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`OTP_ID`, `UserID`, `Purpose`, `OTP_Code`, `ExpiresAt`) VALUES
(2, 6, 'email', '780958', '2025-01-22 16:13:46'),
(3, 7, 'email', '557949', '2025-01-22 16:15:34'),
(4, 8, 'email', '480762', '2025-01-22 16:19:27'),
(5, 9, 'email', '023999', '2025-01-22 16:21:36'),
(6, 10, 'email', '188809', '2025-01-22 16:24:44'),
(7, 11, 'email', '455847', '2025-01-22 16:26:55'),
(8, 12, 'email', '398245', '2025-01-22 16:28:56'),
(9, 13, 'email', '608926', '2025-01-22 16:30:48'),
(10, 14, 'email', '481112', '2025-01-22 16:33:09'),
(11, 15, 'email', '226722', '2025-01-22 16:34:48'),
(12, 25, 'email', '883231', '2025-01-28 07:01:52'),
(13, 26, 'email', '541581', '2025-01-28 07:03:42');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `ResidencyID` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Purpose` varchar(50) DEFAULT NULL,
  `PaymentDate` datetime DEFAULT current_timestamp(),
  `ReferenceNumber` longtext DEFAULT NULL,
  `EstPaymentChannel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_channel`
--

CREATE TABLE `payment_channel` (
  `ChannelID` int(11) NOT NULL,
  `ChannelName` varchar(50) NOT NULL,
  `ChannelLogo` text NOT NULL,
  `Type` enum('e-wallet','bank','credit card','debit card','cash','cheque','remittance') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `PersonID` int(11) NOT NULL,
  `ProfilePicture` text DEFAULT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `ExtName` enum('Jr.','Sr.','I','II','III','IV','V','V','VI','VII') DEFAULT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `ContactNumber` varchar(15) NOT NULL,
  `IsContactVerified` tinyint(1) DEFAULT 0,
  `HomeAddress` varchar(225) NOT NULL,
  `Religion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`PersonID`, `ProfilePicture`, `FirstName`, `MiddleName`, `LastName`, `ExtName`, `Gender`, `DateOfBirth`, `ContactNumber`, `IsContactVerified`, `HomeAddress`, `Religion`) VALUES
(1, '/bookingapp/user/profile-pictures/Nagaranao_013020250348.png', 'Nagaranao', 'sss', 'Sangcopan', '', 'Male', '0000-00-00', '0952 123 4132', 0, 'Iligan city', 'Islam'),
(2, NULL, 'Ryan', '', 'Macarambon', '', 'Male', NULL, '0965 443 2910', 0, 'Bangon Marawi CIty', NULL),
(3, '/bookingapp/user/profile-pictures/Hanif_013020250517.jpg', 'Hanif', '', 'Agakhan', '', 'Male', NULL, '0982 678 9140', 0, 'Dimaluna, Marawi City', NULL),
(4, '/bookingapp/user/profile-pictures/Sarip_013020250518.jpg', 'Norhassan', '', 'Sarip', '', 'Male', NULL, '0965 123 6434', 0, 'Sarimanok, Marawi City', NULL),
(6, '/bookingapp/user/profile-pictures/Jam_013020252052.jpg', 'Jamairah', '', 'Cosain', '', 'Female', '2025-01-20', '0957 123 6455', 0, 'Barrio Salam', 'Islam'),
(7, NULL, 'Edris', '', 'Naimah', '', 'Female', NULL, '0948 234 5234', 0, 'marantao lanao del sur', NULL),
(8, NULL, 'Aisah', '', 'Lacsaman', '', 'Female', NULL, '0977 714 1252', 0, 'Bacong Marawi City', NULL),
(9, NULL, 'Norsia', '', 'Tago', '', 'Female', NULL, '0944 562 5123', 0, 'marantao lanao del sur', NULL),
(10, NULL, 'Inshira', '', 'H.Edris', '', 'Female', NULL, '0912 443 6456', 0, 'Wato Balindong', NULL),
(11, '/bookingapp/user/profile-pictures/Jehan_012220251238.png', 'Jehan', NULL, 'Macararic', '', 'Female', NULL, '0921 234 1234', 0, 'Marinaut, Marawi City', NULL),
(12, NULL, 'Amal', '', 'Sultan', '', 'Female', NULL, '0921 442 1232', 0, 'Dimaluna, Marawi City', NULL),
(13, NULL, 'Rohaima', '', 'Lomala', '', 'Female', NULL, '0909 123 7575', 0, 'Marantao lanao del sur', NULL),
(14, '/bookingapp/user/profile-pictures/Kimkim_013020250614.jpg', 'Abdul Hakim', '', 'Hadji Usman', '', 'Male', NULL, '0911 245 1312', 0, 'Madalum', NULL),
(15, '/bookingapp/user/profile-pictures/Careb_013020250745.jpg', 'Abdul Careb', '', 'Abedin', '', 'Male', NULL, '0951 414 2134', 0, 'Bangon Marawi CIty', NULL),
(16, '/bookingapp/user/profile-pictures/Namzky_013020250519.jpg', 'Mohammad Namar', '', 'Dimalotang', '', 'Male', NULL, '0941 214 2132', 0, 'Sagonsongan, LDS', NULL),
(17, '/bookingapp/user/profile-pictures/Yuri_013020250453.jpg', 'AL-Yoshri', '', 'Abdulfatah', '', 'Male', NULL, '0912 513 1234', 0, 'Iligan city', NULL),
(18, '/bookingapp/user/profile-pictures/Noor_013020250520.jpg', 'Mohammad Noor', '', 'Macalandong', '', 'Male', NULL, '0927 294 0456', 0, 'marantao lanao del sur', NULL),
(19, '/bookingapp/user/profile-pictures/Lano_013020250522.jpg', 'Malano', '', 'Gasanara', '', 'Female', NULL, '0921 412 5244', 0, 'marantao lanao del sur', NULL),
(20, '/bookingapp/user/profile-pictures/Johaiber_013020250527.jpg', 'Johaiber', '', 'Macaronsing', '', 'Male', NULL, '0975 565 2341', 0, 'Basak Malutlut', NULL),
(21, '/bookingapp/user/profile-pictures/Nina_013020250532.jpg', 'Nornina', '', 'Dia', '', 'Female', NULL, '0952 245 2344', 0, 'Naawan', NULL),
(22, '/bookingapp/user/profile-pictures/Aliah_013020250536.jpg', 'Aliah', '', 'Manda', '', 'Female', NULL, '0914 523 3124', 0, 'Barrio Salam', NULL),
(23, '/bookingapp/user/profile-pictures/Amin_013020250607.jpg', 'Nor-amin', '', 'Hadji Ali', '', 'Male', NULL, '0951 423 4212', 0, 'Saguiran LDS', NULL),
(24, '/bookingapp/user/profile-pictures/Unos_013020250608.jpg', 'Ahmad Sodais', '', 'Unos', '', 'Male', NULL, '0912 342 3453', 0, 'Basak Malutlut', NULL),
(25, NULL, 'Ben', 'Gasanara', 'Mac', '', 'Male', NULL, '0923 214 1234', 0, 'marantao lanao del sur', NULL),
(28, NULL, 'Ben', 'Gasanara', 'Mac', '', 'Male', NULL, '0911 523 7777', 0, 'marantao lanao del sur', NULL),
(29, NULL, 'Kim', '', 'Chiu', '', 'Female', NULL, '0999 523 4272', 0, 'Madalum', NULL),
(30, NULL, 'Sanodin', '', 'H.Naim', '', 'Male', NULL, '0966 555 7772', 0, 'Marantao LDS', NULL),
(31, NULL, 'Yasser', '', 'Macausor', '', 'Male', NULL, '0955 222 4123', 0, 'Marantao LDS', NULL),
(32, '/bookingapp/user/profile-pictures/Johana_013120250347.png', 'Johana', '', 'Bayabao', '', 'Female', NULL, '0985 252 4253', 0, 'marantao lanao del sur', NULL),
(33, '/bookingapp/user/profile-pictures/adminUser_021520250633.png', 'Admin', '', 'User', '', 'Male', NULL, '0949 959 9595', 0, 'Bacong, Marantao, Lanao del Sur', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `residency`
--

CREATE TABLE `residency` (
  `ResidencyID` int(11) NOT NULL,
  `TenantID` int(11) DEFAULT NULL,
  `RoomID` int(11) DEFAULT NULL,
  `DateOfEntry` date DEFAULT NULL,
  `DateOfExit` date DEFAULT NULL,
  `Status` enum('pending','confirmed','cancelled','currently residing','reserved','deleted','rejected','residency ended') DEFAULT 'pending',
  `Remark` varchar(100) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdateAt` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residency`
--

INSERT INTO `residency` (`ResidencyID`, `TenantID`, `RoomID`, `DateOfEntry`, `DateOfExit`, `Status`, `Remark`, `CreatedAt`, `UpdateAt`) VALUES
(1, 7, 10, '2025-01-22', '2025-02-15', 'residency ended', 'Residency ended by owner', '2025-01-22 03:41:33', '2025-02-15 16:16:20'),
(2, 2, 23, '2025-01-22', NULL, 'currently residing', NULL, '2025-01-22 04:02:41', '2025-01-28 16:58:02'),
(3, 3, 23, '2025-01-27', '2025-03-07', 'residency ended', 'Residency ended by owner', '2025-01-22 04:04:07', '2025-03-07 15:24:20'),
(4, 2, 14, '2025-01-29', NULL, 'cancelled', NULL, '2025-01-22 16:55:21', '2025-01-22 16:57:18'),
(5, 5, 37, '2025-01-27', NULL, 'cancelled', NULL, '2025-01-23 15:03:52', '2025-01-23 15:07:43'),
(6, 13, 27, '2025-01-30', NULL, 'cancelled', NULL, '2025-01-27 14:07:12', '2025-01-28 17:14:13'),
(7, 2, 48, '2025-01-28', NULL, 'currently residing', NULL, '2025-01-27 20:34:03', '2025-01-28 17:14:13'),
(8, 2, 20, '2025-01-28', NULL, 'cancelled', NULL, '2025-01-27 20:35:37', '2025-01-28 17:14:13'),
(9, 3, 24, '2025-01-28', NULL, 'currently residing', NULL, '2025-01-27 20:45:29', '2025-01-28 17:14:13'),
(10, 11, 40, '2025-01-28', NULL, 'currently residing', NULL, '2025-01-27 20:50:04', '2025-01-28 17:14:13'),
(11, 4, 37, '2025-01-28', NULL, 'cancelled', NULL, '2025-01-27 21:29:23', '2025-01-28 17:14:13'),
(12, 4, 47, '2025-01-28', NULL, 'currently residing', NULL, '2025-01-27 21:30:40', '2025-01-28 17:14:13'),
(13, 10, 39, '2025-01-29', NULL, 'cancelled', NULL, '2025-01-28 17:27:14', '2025-01-28 17:27:15'),
(16, 10, 40, '2025-01-29', NULL, 'cancelled', NULL, '2025-01-28 17:34:17', '2025-01-28 17:34:18'),
(17, 5, 48, '2025-01-29', NULL, 'cancelled', NULL, '2025-01-29 14:10:58', '2025-01-29 21:21:20'),
(18, 10, 1, '2025-01-30', NULL, 'cancelled', NULL, '2025-01-29 21:17:40', '2025-01-29 21:21:27'),
(19, 10, 1, '2025-01-31', NULL, 'cancelled', NULL, '2025-01-29 21:19:12', '2025-01-29 21:19:13'),
(20, 10, 3, '2025-02-04', NULL, 'cancelled', NULL, '2025-01-29 21:19:51', '2025-01-29 21:19:52'),
(21, 10, 2, '2025-01-30', NULL, 'cancelled', NULL, '2025-01-29 21:25:51', '2025-01-29 21:25:52'),
(22, 10, 27, '2025-01-30', NULL, 'cancelled', 'Cancelled by tenant', '2025-01-29 21:33:37', '2025-02-15 14:46:51'),
(23, 10, 44, '2025-02-03', '2025-01-29', 'residency ended', NULL, '2025-01-29 21:36:08', '2025-01-29 21:37:37'),
(24, 11, 15, '2025-01-31', '2025-01-29', 'residency ended', NULL, '2025-01-29 22:47:32', '2025-01-29 23:37:33'),
(25, 6, 57, '2025-02-01', NULL, 'currently residing', NULL, '2025-01-29 22:56:00', '2025-01-29 23:03:25'),
(26, 7, 57, '2025-01-30', NULL, 'currently residing', NULL, '2025-01-29 22:57:41', '2025-01-29 23:42:06'),
(27, 8, 58, '2025-01-30', NULL, 'rejected', NULL, '2025-01-29 22:59:06', '2025-01-29 23:39:31'),
(28, 5, 58, '2025-01-31', NULL, 'currently residing', NULL, '2025-01-29 23:02:11', '2025-01-29 23:03:25'),
(29, 11, 44, '2025-01-30', NULL, 'currently residing', NULL, '2025-01-30 09:54:26', '2025-01-30 10:04:07'),
(30, 4, 18, '2025-01-30', NULL, 'currently residing', NULL, '2025-01-30 10:01:25', '2025-01-30 10:04:07'),
(31, 7, 26, '2025-01-30', NULL, 'currently residing', NULL, '2025-01-30 10:09:46', '2025-01-30 10:16:54'),
(32, 2, 24, '2025-01-30', '2025-01-30', 'pending', NULL, '2025-01-30 11:29:43', '2025-01-30 11:39:41'),
(33, 2, 39, '2025-02-08', NULL, 'cancelled', NULL, '2025-02-01 11:50:21', '2025-02-01 11:50:22'),
(34, 11, 10, '2025-02-20', NULL, 'rejected', 'ghfhtfhgghjhgg', '2025-02-15 15:32:33', '2025-02-15 16:19:14'),
(35, 7, 11, '2025-02-19', NULL, 'confirmed', NULL, '2025-02-15 16:18:36', '2025-02-15 16:21:11'),
(36, 7, 59, '2025-02-24', NULL, 'currently residing', 'Reconsidered by owner', '2025-02-24 01:07:33', '2025-02-24 01:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `residency_status`
--

CREATE TABLE `residency_status` (
  `StatusID` int(11) NOT NULL,
  `ResidencyID` int(11) DEFAULT NULL,
  `Status` enum('pending','confirmed','cancelled','ended') DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `RoomID` int(11) NOT NULL,
  `TenantID` int(11) NOT NULL,
  `StaffScore` int(11) DEFAULT NULL CHECK (`StaffScore` between 1 and 10),
  `FacilitiesScore` int(11) DEFAULT NULL CHECK (`FacilitiesScore` between 1 and 10),
  `CleanlinessScore` int(11) DEFAULT NULL CHECK (`CleanlinessScore` between 1 and 10),
  `ComfortScore` int(11) DEFAULT NULL CHECK (`ComfortScore` between 1 and 10),
  `MoneyValueScore` int(11) DEFAULT NULL CHECK (`MoneyValueScore` between 1 and 10),
  `LocationScore` int(11) DEFAULT NULL CHECK (`LocationScore` between 1 and 10),
  `SignalScore` int(11) DEFAULT NULL CHECK (`SignalScore` between 1 and 10),
  `SecurityScore` int(11) DEFAULT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Comments` varchar(300) DEFAULT NULL,
  `IsDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `RoomID`, `TenantID`, `StaffScore`, `FacilitiesScore`, `CleanlinessScore`, `ComfortScore`, `MoneyValueScore`, `LocationScore`, `SignalScore`, `SecurityScore`, `UpdatedAt`, `CreatedAt`, `Comments`, `IsDeleted`) VALUES
(3, 58, 5, 10, 10, 10, 10, 10, 10, 10, 10, '2025-01-30 07:14:50', '2025-01-30 07:07:17', 'this **** is comfortable', 0),
(4, 58, 5, 6, 8, 6, 8, 7, 6, 8, 6, '2025-01-30 07:12:07', '2025-01-30 07:10:39', 'Balay.com is really helpful especially for students living in distant places', 0),
(5, 58, 8, 7, 7, 6, 4, 4, 7, 6, 6, '2025-01-30 07:24:35', '2025-01-30 07:24:35', 'I experienced some slow loading times when browsing multiple listings, and sometimes the search filters don’t work \\r\\nproperly. ', 0),
(6, 58, 8, 6, 8, 8, 8, 8, 8, 8, 8, '2025-01-30 07:25:32', '2025-01-30 07:25:32', 'The website looks nice, but it was a bit confusing to find contact details for property owners. It would be great if there \\r\\nwas a dedicated section for direct inquiries.', 0),
(7, 15, 11, 10, 10, 10, 10, 10, 10, 10, 10, '2025-01-30 07:26:53', '2025-01-30 07:26:53', 'I love how easy it is to navigate Balay.com! The layout is clean, and I can quickly find the properties that match my \\r\\nbudget and location.', 0),
(8, 15, 11, 10, 10, 10, 10, 10, 10, 10, 10, '2025-01-30 07:27:35', '2025-01-30 07:27:35', 'The website runs smoothly, and I’ve never had issues with loading times. I especially appreciate the detailed property \\r\\ndescriptions and high-quality images.', 0),
(9, 57, 7, 6, 6, 6, 6, 6, 6, 6, 6, '2025-01-30 07:29:26', '2025-01-30 07:29:26', 'Why is there no dark mode? My eyes hurt from looking at this bright screen for too long. ', 0),
(10, 57, 7, 5, 5, 5, 5, 5, 5, 5, 5, '2025-01-30 07:30:57', '2025-01-30 07:30:57', 'What the hell!!!', 0),
(11, 57, 7, 4, 6, 4, 6, 6, 5, 5, 4, '2025-01-30 07:31:40', '2025-01-30 07:31:40', 'SHITTTT!!!', 0),
(12, 58, 5, 10, 10, 10, 10, 10, 10, 10, 10, '2025-01-30 07:33:30', '2025-01-30 07:33:30', 'I love the modern and simple design! The interface is smooth, and the search filters work perfectly. It’s way better than \\r\\nother real estate websites I’ve tried.', 0),
(14, 58, 5, 10, 10, 10, 10, 10, 10, 10, 10, '2025-01-30 07:36:04', '2025-01-30 07:36:04', 'Finally, a real estate website that looks modern and professional! No unnecessary clutter, just the right information \\r\\npresented in a clear way.\\r\\n\\r\\n', 0),
(15, 23, 2, 4, 5, 1, 6, 5, 5, 5, 5, '2025-01-30 19:50:44', '2025-01-30 19:50:44', 'what the hell, maraming daga', 0);

-- --------------------------------------------------------

--
-- Table structure for table `review_responses`
--

CREATE TABLE `review_responses` (
  `ResponseID` int(11) NOT NULL,
  `ReviewID` int(11) NOT NULL,
  `ResponderID` int(11) DEFAULT NULL,
  `ResponseText` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `RoomID` int(11) NOT NULL,
  `RoomName` varchar(50) DEFAULT NULL,
  `RoomType` enum('Single occupancy','Double occupancy','Triple occupancy','Quad occupancy','Suite','Suite double','Suite triple','Studio apartment','One-bedroom apartment','Two-bedroom apartment','Three-bedroom apartment','Luxury suites') NOT NULL,
  `PaymentRate` decimal(10,2) DEFAULT NULL,
  `PaymentOptions` enum('Monthly','Semestral') NOT NULL,
  `PaymentStructure` enum('Per room','Per person','Per bed') NOT NULL,
  `Availability` enum('Available','Occupied','Reserved','Unavailable','Closed','Deleted') DEFAULT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `FloorLocation` int(11) DEFAULT NULL,
  `PaymentRules` varchar(250) DEFAULT NULL,
  `Photo` text DEFAULT NULL,
  `GenderInclusiveness` enum('Males only','Females only','Co-ed') DEFAULT NULL,
  `MaxOccupancy` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`RoomID`, `RoomName`, `RoomType`, `PaymentRate`, `PaymentOptions`, `PaymentStructure`, `Availability`, `EstablishmentID`, `FloorLocation`, `PaymentRules`, `Photo`, `GenderInclusiveness`, `MaxOccupancy`) VALUES
(1, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 1, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Males only', 4),
(2, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 1, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Males only', 4),
(3, 'ROOM 3', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 1, 1, '', 'pictures/ROOM 3__Quad occupancy.jpg', 'Males only', 4),
(4, 'ROOM 4', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 1, 1, '', 'pictures/ROOM 4__Quad occupancy.jpg', 'Males only', 4),
(5, 'ROOM 5', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 1, 1, '', 'pictures/ROOM 5__Quad occupancy.jpg', 'Males only', 4),
(6, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 2, 1, '', 'pictures/ROOM 1_2_Quad occupancy.jpg', 'Males only', 1),
(7, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 2, 1, '', 'pictures/ROOM 2_2_Quad occupancy.jpg', 'Males only', 1),
(8, 'ROOM 3', 'Triple occupancy', 350.00, '', 'Per person', 'Available', 2, 1, '', 'pictures/ROOM 3_2_Triple occupancy.jpg', 'Males only', 3),
(9, 'ROOM 4', 'Double occupancy', 350.00, '', 'Per person', 'Available', 2, 1, '', 'pictures/ROOM 4_2_Double occupancy.jpg', 'Males only', 2),
(10, 'ROOM 1', 'Double occupancy', 350.00, 'Monthly', 'Per person', 'Available', 3, 1, '', 'pictures/ROOM 1__Double occupancy.jpg', 'Males only', 2),
(11, 'ROOM 2', 'Quad occupancy', 350.00, 'Monthly', 'Per person', 'Available', 3, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Co-ed', 4),
(12, 'ROOM 3', '', 350.00, '', 'Per person', 'Available', 3, 1, '', 'pictures/ROOM 3__Five-person occupancy.jpg', 'Females only', 5),
(13, 'ROOM 4', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 3, 1, '', 'pictures/ROOM 4__Quad occupancy.jpg', 'Females only', 4),
(14, 'ROOM 5', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 3, 1, '', 'pictures/ROOM 5__Quad occupancy.jpg', 'Females only', 4),
(15, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 4, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Males only', 4),
(16, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 4, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Males only', 4),
(17, 'ROOM 3', 'Quad occupancy', 350.00, '', 'Per person', 'Deleted', 4, 1, '', 'pictures/ROOM 3__Quad occupancy.jpg', 'Males only', 4),
(18, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 5, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Females only', 4),
(19, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 5, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Females only', 4),
(20, 'ROOM 3', 'Triple occupancy', 350.00, '', 'Per person', 'Available', 5, 1, '', 'pictures/ROOM 3__Triple occupancy.jpg', 'Females only', 3),
(21, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Deleted', 6, 1, '', 'pictures/ROOM 1_6_Quad occupancy.jpg', 'Females only', 4),
(22, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 6, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Females only', 4),
(23, 'ROOM 3', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 6, 1, '', 'pictures/ROOM 3__Quad occupancy.jpg', 'Females only', 4),
(24, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 7, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Females only', 4),
(25, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 7, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Females only', 4),
(26, 'ROOM 3', 'Triple occupancy', 350.00, '', 'Per person', 'Available', 7, 1, '', 'pictures/ROOM 3__Triple occupancy.jpg', 'Females only', 3),
(27, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 8, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Males only', 4),
(28, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 8, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Males only', 4),
(29, 'ROOM 3', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 8, 1, '', 'pictures/ROOM 3__Quad occupancy.jpg', 'Males only', 4),
(30, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 10, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Females only', 4),
(31, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 10, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Females only', 4),
(32, 'ROOM 3', 'Double occupancy', 350.00, '', 'Per person', 'Available', 10, 1, '', 'pictures/ROOM 3__Double occupancy.jpg', 'Females only', 2),
(33, 'ROOM 4', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 10, 1, '', 'pictures/ROOM 4__Quad occupancy.jpg', 'Females only', 4),
(34, 'ROOM 5', 'Quad occupancy', 500.00, '', 'Per person', 'Available', 10, 1, '', 'pictures/ROOM 5__Quad occupancy.jpg', 'Females only', 4),
(35, 'ROOM 6', 'Quad occupancy', 500.00, '', 'Per person', 'Available', 10, 1, '', 'pictures/ROOM 6__Quad occupancy.jpg', 'Females only', 4),
(36, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 11, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Females only', 4),
(37, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 11, 1, '', 'pictures/ROOM 2__Quad occupancy.jpg', 'Females only', 4),
(38, 'ROOM 3', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 11, 1, '', 'pictures/ROOM 3__Quad occupancy.jpg', 'Females only', 4),
(39, 'ROOM 1', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 1__Quad occupancy.jpg', 'Males only', 4),
(40, 'ROOM 2', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 2__Quad occupancy.jpg', 'Males only', 4),
(41, 'ROOM 3', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 3__Quad occupancy.jpg', 'Males only', 4),
(42, 'ROOM 4', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 4__Quad occupancy.jpg', 'Males only', 4),
(43, 'ROOM 5', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 5__Quad occupancy.jpg', 'Males only', 4),
(44, 'ROOM 1', 'Quad occupancy', 650.00, 'Monthly', 'Per person', 'Available', 13, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 1__Quad occupancy.jpg', 'Males only', 4),
(45, 'ROOM 2', 'Quad occupancy', 650.00, 'Monthly', 'Per person', 'Available', 13, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 2__Quad occupancy.jpg', 'Males only', 4),
(46, 'ROOM 3', 'Quad occupancy', 650.00, 'Monthly', 'Per person', 'Available', 13, 1, 'One-month deposit; one-month advance', 'pictures/ROOM 3__Quad occupancy.jpg', 'Males only', 4),
(47, 'ROOM 10', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 2, 'One-month deposit; one-month advance', 'pictures/ROOM 10__Quad occupancy.jpg', 'Females only', 4),
(48, 'ROOM 11', 'Quad occupancy', 700.00, 'Monthly', 'Per person', 'Available', 12, 2, 'One-month deposit; one-month advance', 'pictures/ROOM 11__Quad occupancy.jpg', 'Females only', 4),
(49, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 6, 1, '', 'pictures/ROOM 1__Quad occupancy.jpg', 'Females only', 4),
(50, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 15, 1, '', 'pictures/ROOM 1_15_Quad occupancy.jpg', 'Males only', 4),
(51, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Deleted', 16, 1, '', 'pictures/ROOM 1_16_Quad occupancy.jpg', 'Males only', 4),
(52, 'ROOM 3', 'Triple occupancy', 350.00, '', 'Per person', 'Available', 4, 1, '', 'pictures/ROOM 3_4_Quad occupancy.jpg', 'Males only', 3),
(53, 'ROOM 2', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 16, 1, '', 'pictures/ROOM 2_16_Quad occupancy.jpg', 'Males only', 4),
(54, 'ROOM 3', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 16, 1, '', 'pictures/ROOM 3_16_Quad occupancy.jpg', 'Males only', 4),
(55, 'ROOM 4', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 16, 1, '', 'pictures/ROOM 4_16_Quad occupancy.jpg', 'Males only', 4),
(56, 'ROOM 1', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 16, 1, '', 'pictures/ROOM 1_16_Quad occupancy.jpg', 'Males only', 4),
(57, 'ROOM 4', 'Double occupancy', 500.00, '', 'Per person', 'Available', 4, 2, '', 'pictures/ROOM 4_4_Double occupancy.jpg', 'Females only', 2),
(58, 'ROOM 5', 'Quad occupancy', 350.00, '', 'Per person', 'Available', 4, 2, '', 'pictures/ROOM 5_4_Quad occupancy.jpg', 'Females only', 4),
(59, 'ROOM 1', 'Single occupancy', 350.00, '', 'Per person', 'Available', 21, 1, '', 'pictures/ROOM 1_21_Single occupancy.jpg', 'Females only', 1),
(60, 'asd', 'Single occupancy', 500.00, 'Monthly', '', 'Deleted', 22, 1, '', 'pictures/asd_22_Single occupancy.jpg', 'Co-ed', 1),
(61, 'ROOM 1', 'Quad occupancy', 500.00, '', 'Per bed', 'Available', 23, 1, '', 'pictures/ROOM 1_23_Quad occupancy.jpg', 'Males only', 4);

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `Code` int(11) NOT NULL,
  `RoomID` int(11) DEFAULT NULL,
  `FeatureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_features`
--

INSERT INTO `room_features` (`Code`, `RoomID`, `FeatureID`) VALUES
(1, 39, 5),
(2, 39, 12),
(3, 39, 26),
(4, 39, 51),
(5, 40, 5),
(6, 40, 55),
(7, 40, 10),
(8, 40, 89),
(10, 41, 23),
(12, 41, 5),
(13, 41, 24),
(14, 41, 84),
(15, 41, 49),
(16, 42, 5),
(17, 42, 23),
(18, 42, 10),
(19, 42, 26),
(20, 43, 5),
(22, 43, 89),
(23, 43, 10),
(24, 43, 12),
(25, 47, 5),
(26, 47, 26),
(27, 47, 50),
(28, 47, 10),
(29, 47, 13),
(30, 47, 89),
(31, 47, 12),
(32, 42, 23),
(33, 41, 23),
(38, 40, 23),
(40, 39, 23),
(41, 48, 5),
(42, 48, 26),
(43, 48, 12),
(44, 48, 10),
(45, 48, 89),
(46, 1, 5),
(49, 1, 1),
(53, 1, 55),
(56, 1, 89),
(57, 2, 5),
(58, 2, 26),
(59, 2, 89),
(61, 2, 84),
(62, 3, 5),
(63, 3, 84),
(64, 3, 89),
(65, 3, 1),
(68, 3, 26),
(69, 3, 55),
(71, 4, 5),
(72, 4, 26),
(74, 4, 51),
(75, 4, 1),
(76, 5, 5),
(77, 5, 1),
(78, 5, 26),
(79, 5, 55),
(80, 5, 84),
(82, 6, 23),
(85, 6, 54),
(87, 6, 5),
(88, 6, 26),
(89, 6, 1),
(90, 7, 5),
(91, 7, 26),
(92, 7, 55),
(93, 7, 89),
(94, 7, 2),
(96, 8, 5),
(97, 8, 51),
(98, 8, 2),
(99, 8, 26),
(100, 9, 5),
(101, 9, 51),
(102, 9, 26),
(103, 9, 55),
(104, 9, 2),
(105, 15, 5),
(106, 15, 51),
(107, 15, 55),
(108, 15, 26),
(109, 15, 89),
(110, 16, 5),
(111, 16, 1),
(112, 16, 26),
(114, 16, 55),
(115, 16, 51),
(116, 16, 12),
(117, 16, 2),
(118, 17, 5),
(119, 17, 26),
(120, 17, 55),
(121, 17, 89),
(122, 17, 1),
(123, 10, 5),
(124, 10, 89),
(125, 10, 55),
(127, 10, 51),
(128, 11, 5),
(129, 11, 89),
(130, 11, 26),
(132, 11, 55),
(134, 12, 5),
(135, 12, 26),
(136, 12, 55),
(138, 12, 55),
(139, 13, 5),
(140, 13, 55),
(141, 13, 26),
(142, 13, 51),
(143, 13, 89),
(144, 13, 1),
(145, 14, 5),
(146, 14, 26),
(147, 14, 55),
(148, 14, 51),
(149, 14, 89),
(150, 18, 5),
(151, 18, 26),
(152, 18, 51),
(153, 18, 55),
(155, 19, 5),
(157, 19, 26),
(158, 19, 55),
(159, 19, 51),
(160, 19, 89),
(161, 20, 5),
(162, 20, 26),
(163, 20, 55),
(164, 20, 51),
(165, 20, 89),
(166, 21, 5),
(167, 21, 26),
(168, 21, 55),
(169, 21, 51),
(170, 21, 89),
(171, 22, 5),
(172, 22, 26),
(173, 22, 55),
(174, 22, 51),
(175, 22, 89),
(176, 23, 5),
(177, 23, 26),
(178, 23, 55),
(179, 23, 51),
(180, 23, 89),
(183, 24, 5),
(184, 24, 26),
(185, 24, 55),
(186, 24, 51),
(187, 24, 89),
(188, 25, 5),
(189, 25, 26),
(190, 25, 55),
(191, 25, 89),
(192, 26, 5),
(193, 26, 26),
(194, 26, 55),
(195, 26, 51),
(196, 26, 89),
(197, 51, 23),
(198, 51, 1),
(199, 51, 20),
(200, 51, 10),
(201, 51, 9),
(202, 51, 23),
(203, 51, 1),
(204, 51, 20),
(205, 51, 10),
(206, 51, 9),
(207, 51, 23),
(208, 56, 5),
(209, 56, 51),
(210, 56, 55),
(211, 56, 89),
(212, 53, 5),
(214, 53, 51),
(215, 53, 26),
(216, 54, 5),
(217, 54, 89),
(218, 54, 51),
(219, 55, 5),
(220, 55, 24),
(221, 55, 89),
(222, 52, 5),
(223, 52, 55),
(224, 27, 5),
(225, 27, 51),
(227, 28, 5),
(228, 28, 55),
(229, 29, 5),
(230, 29, 55),
(231, 36, 5),
(232, 36, 55),
(233, 37, 5),
(234, 37, 55),
(235, 37, 26),
(236, 38, 5),
(237, 38, 51),
(238, 44, 5),
(239, 44, 24),
(240, 45, 55),
(241, 45, 51),
(242, 46, 5),
(243, 46, 24),
(244, 46, 51),
(245, 57, 5),
(247, 57, 55);

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `TenantID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UniversityID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`TenantID`, `UserID`, `UniversityID`) VALUES
(2, 6, '202012322'),
(3, 7, '202012346'),
(4, 8, '202029412'),
(5, 9, '202051234'),
(6, 10, '202015125'),
(7, 11, '202042512'),
(8, 12, '202051245'),
(9, 13, '202052151'),
(10, 14, '202000125'),
(11, 15, '202077724'),
(12, 25, '202023545'),
(13, 26, '202202022'),
(14, 27, '203124123'),
(15, 30, '202027743');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `UserID` int(11) NOT NULL,
  `PersonID` int(11) NOT NULL,
  `EmailAddress` varchar(100) NOT NULL,
  `IsEmailVerified` tinyint(1) DEFAULT 0,
  `Username` varchar(50) NOT NULL,
  `Password` longtext NOT NULL,
  `DateCreated` datetime DEFAULT current_timestamp(),
  `Role` enum('tenant','owner','admin') NOT NULL,
  `Status` enum('pending','active','inactive','blocked','deleted','suspended') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`UserID`, `PersonID`, `EmailAddress`, `IsEmailVerified`, `Username`, `Password`, `DateCreated`, `Role`, `Status`) VALUES
(1, 1, 'sangcopan.nagaranao@s.msumain.edu.ph', 0, 'Nagaranao', '22d7fe8c185003c98f97e5d6ced420c7', '2025-01-21 22:50:27', 'admin', 'pending'),
(2, 2, 'Ryan@gmail.com', 0, 'Ryan', '695548af174b3b5b04aad6b35c0e4782', '2025-01-21 22:55:04', 'owner', 'pending'),
(3, 3, 'agakhan44@gmail.com', 0, 'Hanif', 'da40526f219afb8a602e12c727aed58d', '2025-01-21 22:57:45', 'owner', 'pending'),
(4, 4, 'Sarip162@gmail.com', 0, 'Sarip', '5c1bf3f4fbc8bb4997afcc33a096fc2b', '2025-01-21 22:59:11', 'owner', 'pending'),
(6, 6, 'cosain.jamairah@s.msumain.edu.ph', 0, 'Jam', '081d8ab948892e8945c78f1f25d315e1', '2025-01-21 23:10:46', 'tenant', 'pending'),
(7, 7, 'edris.nm15@s.msumain.edu.ph', 0, 'Naimah', '64647ba283cec3d6bf3386c09001f5a0', '2025-01-21 23:12:34', 'tenant', 'pending'),
(8, 8, 'lacsaman.aa06@s.msumain.edu.ph', 0, 'Azzi', 'eb9e6b7fe92a3bbf69d4e5ce364ce8ca', '2025-01-21 23:16:27', 'tenant', 'pending'),
(9, 9, 'tago.na25@s.msumain.edu.ph', 0, 'Unnie', 'd4e9c65d0f5385920e00beeac748bec7', '2025-01-21 23:18:36', 'tenant', 'pending'),
(10, 10, 'h.edris.im95@s.msumain.edu.ph', 0, 'Inshira', '7c9f02cc18da2e34ac82ac326fa71a44', '2025-01-21 23:21:44', 'tenant', 'pending'),
(11, 11, 'macararic.jehan@s.msumain.edu.ph', 0, 'Jehan', '37cf08110057cff2ae9b16424a055759', '2025-01-21 23:23:54', 'tenant', 'pending'),
(12, 12, 'sultan.amal@s.msumain.edu.ph', 0, 'Amal', '02777cba5ef1836f45265346f1e0c4f4', '2025-01-21 23:25:55', 'tenant', 'pending'),
(13, 13, 'lomala.rohaima@s.msumain.edu.ph', 0, 'Rohaima', 'cf0d2183649197f2e8fc8a4d4d271ae6', '2025-01-21 23:27:48', 'tenant', 'pending'),
(14, 14, 'hadjiusman.at47@s.msumain.edu.ph', 0, 'Kimkim', 'e63a57c6a054450180b02a2ad9d60d57', '2025-01-21 23:30:09', 'tenant', 'pending'),
(15, 15, 'abedin.ad38@s.msumain.edu.ph', 0, 'Careb', '17a355c51cb058e7f7b86ae5d3237133', '2025-01-21 23:31:48', 'tenant', 'pending'),
(16, 16, 'namardimalotang@gmail.com', 0, 'Namzky', '2737ca6caee970805ded8497438f2065', '2025-01-21 23:34:44', 'owner', 'pending'),
(17, 17, 'yuri07@gmail.com', 0, 'Yuri', 'f3c391ce8a6cec4c6c557e2eed4a1ceb', '2025-01-21 23:36:33', 'owner', 'pending'),
(18, 18, 'noormacalandong265@gmail.com', 0, 'Noor', '034c6517ce0b3d34651de9fb32eb1983', '2025-01-21 23:37:40', 'owner', 'pending'),
(19, 19, 'gasanara@gmail.com', 0, 'Lano', '5516bc4faa89b1b9e179ca90de7bdf82', '2025-01-21 23:39:16', 'owner', 'pending'),
(20, 20, 'joh40@gmail.com', 0, 'Johaiber', '40423fe3cddcf2d817d67e51d640735b', '2025-01-21 23:41:14', 'owner', 'pending'),
(21, 21, 'nornina06@gmail.com', 0, 'Nina', 'a400e7493a198ec22b54e81478d3ef7f', '2025-01-21 23:43:48', 'owner', 'pending'),
(22, 22, 'Aliah@gmail.com', 0, 'Aliah', '39e9ed5b513d41c6b3ccf79ea2215359', '2025-01-21 23:45:52', 'owner', 'pending'),
(23, 23, 'amin71@gmail.com', 0, 'Amin', '5e064e4ecc1769841dd7d66810a3db46', '2025-01-21 23:47:48', 'owner', 'pending'),
(24, 24, 'Sodais@gmail.com', 0, 'Unos', 'd795ab7aa56aea04868418b4f6ddc54b', '2025-01-21 23:51:55', 'owner', 'pending'),
(25, 25, 'ben14@s.msumain.edu.ph', 0, 'Ben', 'fd035f49548f054943c9f91138bf4c50', '2025-01-27 13:58:51', 'tenant', 'pending'),
(26, 28, 'benla0809@s.msumain.edu.ph', 0, 'Ben08', 'fd035f49548f054943c9f91138bf4c50', '2025-01-27 14:00:42', 'tenant', 'pending'),
(27, 29, 'kimmy412@s.msumain.edu.ph', 0, 'Kimmy', 'a192c7f13258dddb8f9f65b15086ccd9', '2025-01-28 17:18:03', 'tenant', 'pending'),
(28, 30, 'Naim17@gmail.com', 0, 'Sano', '7c5b5055bad6f13660b73056b8a0dc2e', '2025-01-29 15:00:00', 'owner', 'pending'),
(29, 31, 'macausor20@gmail.com', 0, 'Yas', '755a125c7e2328351499a34cdaf44c19', '2025-01-29 15:04:08', 'admin', 'pending'),
(30, 32, 'bayabao.johana@s.msumain.edu.ph', 0, 'Johana', 'db4150853d4ba3421cf257584af118e5', '2025-01-30 18:46:27', 'tenant', 'pending'),
(31, 33, 'admin@msumain.edu.ph', 0, 'adminUser', 'ad173b6d7864f0dbcfcef93fb926cf66', '2025-02-15 13:31:54', 'admin', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `user_socials`
--

CREATE TABLE `user_socials` (
  `SocialID` int(11) NOT NULL,
  `FacebookURL` text DEFAULT NULL,
  `TwitterX` text DEFAULT NULL,
  `Instagram` text DEFAULT NULL,
  `YouTube` text DEFAULT NULL,
  `TikTok` text DEFAULT NULL,
  `LinkedIn` text DEFAULT NULL,
  `Website` text DEFAULT NULL,
  `PersonID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_socials`
--

INSERT INTO `user_socials` (`SocialID`, `FacebookURL`, `TwitterX`, `Instagram`, `YouTube`, `TikTok`, `LinkedIn`, `Website`, `PersonID`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4),
(6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6),
(7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7),
(8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8),
(9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9),
(10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10),
(11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11),
(12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12),
(13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13),
(14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14),
(15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15),
(16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16),
(17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17),
(18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 18),
(19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 19),
(20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20),
(21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 21),
(22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22),
(23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 23),
(24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24),
(25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25),
(26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 28),
(27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 29),
(28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30),
(29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 31),
(30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 32),
(31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 33);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `auth_methods`
--
ALTER TABLE `auth_methods`
  ADD PRIMARY KEY (`AuthID`),
  ADD UNIQUE KEY `ProviderID` (`ProviderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `establishment`
--
ALTER TABLE `establishment`
  ADD PRIMARY KEY (`EstablishmentID`),
  ADD KEY `OwnerID` (`OwnerID`);

--
-- Indexes for table `establishment_features`
--
ALTER TABLE `establishment_features`
  ADD PRIMARY KEY (`Code`),
  ADD KEY `EstablishmentID` (`EstablishmentID`),
  ADD KEY `FeatureID` (`FeatureID`);

--
-- Indexes for table `establishment_owner`
--
ALTER TABLE `establishment_owner`
  ADD PRIMARY KEY (`OwnerID`),
  ADD UNIQUE KEY `UserID_2` (`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `establishment_payment_channel`
--
ALTER TABLE `establishment_payment_channel`
  ADD PRIMARY KEY (`EPCID`),
  ADD KEY `EstablishmentID` (`EstablishmentID`),
  ADD KEY `PaymentChannel` (`PaymentChannel`);

--
-- Indexes for table `establishment_photos`
--
ALTER TABLE `establishment_photos`
  ADD PRIMARY KEY (`PhotoID`),
  ADD UNIQUE KEY `EstablishmentID_2` (`EstablishmentID`),
  ADD KEY `EstablishmentID` (`EstablishmentID`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`FeatureID`);

--
-- Indexes for table `geo_tags`
--
ALTER TABLE `geo_tags`
  ADD PRIMARY KEY (`GeoTagID`),
  ADD UNIQUE KEY `EstablishmentID_2` (`EstablishmentID`),
  ADD KEY `EstablishmentID` (`EstablishmentID`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`OTP_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD UNIQUE KEY `ReferenceNumber` (`ReferenceNumber`) USING HASH,
  ADD KEY `ResidencyID` (`ResidencyID`),
  ADD KEY `EstPaymentChannel` (`EstPaymentChannel`);

--
-- Indexes for table `payment_channel`
--
ALTER TABLE `payment_channel`
  ADD PRIMARY KEY (`ChannelID`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`PersonID`),
  ADD UNIQUE KEY `ContactNumber` (`ContactNumber`);

--
-- Indexes for table `residency`
--
ALTER TABLE `residency`
  ADD PRIMARY KEY (`ResidencyID`),
  ADD KEY `TenantID` (`TenantID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- Indexes for table `residency_status`
--
ALTER TABLE `residency_status`
  ADD PRIMARY KEY (`StatusID`),
  ADD KEY `ResidencyID` (`ResidencyID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `TenantID` (`TenantID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- Indexes for table `review_responses`
--
ALTER TABLE `review_responses`
  ADD PRIMARY KEY (`ResponseID`),
  ADD KEY `ReviewID` (`ReviewID`),
  ADD KEY `ResponderID` (`ResponderID`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`RoomID`),
  ADD KEY `EstablishmentID` (`EstablishmentID`);

--
-- Indexes for table `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`Code`),
  ADD KEY `RoomID` (`RoomID`),
  ADD KEY `FeatureID` (`FeatureID`);

--
-- Indexes for table `tenant`
--
ALTER TABLE `tenant`
  ADD PRIMARY KEY (`TenantID`),
  ADD UNIQUE KEY `UserID_2` (`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `EmailAddress` (`EmailAddress`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `PersonID` (`PersonID`);

--
-- Indexes for table `user_socials`
--
ALTER TABLE `user_socials`
  ADD PRIMARY KEY (`SocialID`),
  ADD KEY `PersonID` (`PersonID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `auth_methods`
--
ALTER TABLE `auth_methods`
  MODIFY `AuthID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment`
--
ALTER TABLE `establishment`
  MODIFY `EstablishmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `establishment_features`
--
ALTER TABLE `establishment_features`
  MODIFY `Code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `establishment_owner`
--
ALTER TABLE `establishment_owner`
  MODIFY `OwnerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `establishment_payment_channel`
--
ALTER TABLE `establishment_payment_channel`
  MODIFY `EPCID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment_photos`
--
ALTER TABLE `establishment_photos`
  MODIFY `PhotoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `FeatureID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `geo_tags`
--
ALTER TABLE `geo_tags`
  MODIFY `GeoTagID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `OTP_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_channel`
--
ALTER TABLE `payment_channel`
  MODIFY `ChannelID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `PersonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `residency`
--
ALTER TABLE `residency`
  MODIFY `ResidencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `residency_status`
--
ALTER TABLE `residency_status`
  MODIFY `StatusID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `review_responses`
--
ALTER TABLE `review_responses`
  MODIFY `ResponseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `Code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `tenant`
--
ALTER TABLE `tenant`
  MODIFY `TenantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_socials`
--
ALTER TABLE `user_socials`
  MODIFY `SocialID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `person` (`PersonID`),
  ADD CONSTRAINT `admin_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `person` (`PersonID`),
  ADD CONSTRAINT `admin_ibfk_4` FOREIGN KEY (`UserID`) REFERENCES `user_account` (`UserID`);

--
-- Constraints for table `auth_methods`
--
ALTER TABLE `auth_methods`
  ADD CONSTRAINT `auth_methods_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user_account` (`UserID`);

--
-- Constraints for table `establishment`
--
ALTER TABLE `establishment`
  ADD CONSTRAINT `establishment_ibfk_1` FOREIGN KEY (`OwnerID`) REFERENCES `establishment_owner` (`OwnerID`);

--
-- Constraints for table `establishment_features`
--
ALTER TABLE `establishment_features`
  ADD CONSTRAINT `establishment_features_ibfk_1` FOREIGN KEY (`EstablishmentID`) REFERENCES `establishment` (`EstablishmentID`),
  ADD CONSTRAINT `establishment_features_ibfk_2` FOREIGN KEY (`FeatureID`) REFERENCES `features` (`FeatureID`);

--
-- Constraints for table `establishment_owner`
--
ALTER TABLE `establishment_owner`
  ADD CONSTRAINT `establishment_owner_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `person` (`PersonID`),
  ADD CONSTRAINT `establishment_owner_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `user_account` (`UserID`);

--
-- Constraints for table `establishment_payment_channel`
--
ALTER TABLE `establishment_payment_channel`
  ADD CONSTRAINT `establishment_payment_channel_ibfk_1` FOREIGN KEY (`EstablishmentID`) REFERENCES `establishment` (`EstablishmentID`),
  ADD CONSTRAINT `establishment_payment_channel_ibfk_2` FOREIGN KEY (`PaymentChannel`) REFERENCES `payment_channel` (`ChannelID`);

--
-- Constraints for table `establishment_photos`
--
ALTER TABLE `establishment_photos`
  ADD CONSTRAINT `establishment_photos_ibfk_1` FOREIGN KEY (`EstablishmentID`) REFERENCES `establishment` (`EstablishmentID`);

--
-- Constraints for table `geo_tags`
--
ALTER TABLE `geo_tags`
  ADD CONSTRAINT `geo_tags_ibfk_1` FOREIGN KEY (`EstablishmentID`) REFERENCES `establishment` (`EstablishmentID`);

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `otp_verifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user_account` (`UserID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`ResidencyID`) REFERENCES `residency` (`ResidencyID`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`EstPaymentChannel`) REFERENCES `establishment_payment_channel` (`EPCID`);

--
-- Constraints for table `residency`
--
ALTER TABLE `residency`
  ADD CONSTRAINT `residency_ibfk_1` FOREIGN KEY (`TenantID`) REFERENCES `tenant` (`TenantID`),
  ADD CONSTRAINT `residency_ibfk_2` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`);

--
-- Constraints for table `residency_status`
--
ALTER TABLE `residency_status`
  ADD CONSTRAINT `residency_status_ibfk_1` FOREIGN KEY (`ResidencyID`) REFERENCES `residency` (`ResidencyID`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`TenantID`) REFERENCES `tenant` (`TenantID`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`);

--
-- Constraints for table `review_responses`
--
ALTER TABLE `review_responses`
  ADD CONSTRAINT `review_responses_ibfk_1` FOREIGN KEY (`ReviewID`) REFERENCES `reviews` (`ReviewID`),
  ADD CONSTRAINT `review_responses_ibfk_2` FOREIGN KEY (`ResponderID`) REFERENCES `user_account` (`UserID`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`EstablishmentID`) REFERENCES `establishment` (`EstablishmentID`);

--
-- Constraints for table `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `room_features_ibfk_1` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`),
  ADD CONSTRAINT `room_features_ibfk_2` FOREIGN KEY (`FeatureID`) REFERENCES `features` (`FeatureID`);

--
-- Constraints for table `tenant`
--
ALTER TABLE `tenant`
  ADD CONSTRAINT `tenant_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user_account` (`UserID`);

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`PersonID`) REFERENCES `person` (`PersonID`);

--
-- Constraints for table `user_socials`
--
ALTER TABLE `user_socials`
  ADD CONSTRAINT `user_socials_ibfk_1` FOREIGN KEY (`PersonID`) REFERENCES `person` (`PersonID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
