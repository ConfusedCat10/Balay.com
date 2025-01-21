-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2025 at 11:06 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookingapp`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `UserID`, `PositionTitle`, `Institution`) VALUES
(1, 1, 'President of the Philippines', 'Malacanang Palace'),
(2, 9, 'Dean', 'College of Information and Computing Sciences'),
(3, 12, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `auth_methods`
--

CREATE TABLE `auth_methods` (
  `AuthID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Provider` enum('google','facebook','apple') NOT NULL,
  `ProviderID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `establishment`
--

CREATE TABLE `establishment` (
  `EstablishmentID` int(11) NOT NULL,
  `OwnerID` int(11) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `NoOfFloors` int(11) DEFAULT NULL,
  `HouseRules` longtext DEFAULT NULL,
  `Status` enum('available','unavailable','removed') DEFAULT 'available',
  `Type` enum('Dormitory','Cottage','Apartment','Boarding House','Hotel','Motel') DEFAULT NULL,
  `GenderInclusiveness` enum('Males only','Females only','Coed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `establishment`
--

INSERT INTO `establishment` (`EstablishmentID`, `OwnerID`, `Name`, `Description`, `CreatedAt`, `NoOfFloors`, `HouseRules`, `Status`, `Type`, `GenderInclusiveness`) VALUES
(19, 2, 'The Trump Cottage', '                                                                dsdas                                                                                                                                                                                       ', '2024-12-10 12:14:10', 3, 'Hello\\r\\nWorld!', 'available', 'Cottage', 'Coed'),
(20, 2, 'Princess Lawanen Hall', 'All-girls dormitory', '2024-12-17 12:34:31', 2, NULL, 'available', 'Dormitory', 'Females only'),
(21, 1, 'Rajah Indapatra Hall', 'djojkldasfdas', '2024-12-17 12:50:48', 2, NULL, 'available', 'Dormitory', 'Females only'),
(22, 2, 'Faisalin Dormitory', 'Gender equality', '2025-01-15 15:55:31', 5, NULL, 'available', 'Cottage', 'Coed'),
(23, 2, 'dsadsadas', '', '2025-01-17 14:34:07', 1, NULL, 'available', 'Dormitory', 'Males only');

-- --------------------------------------------------------

--
-- Table structure for table `establishment_features`
--

CREATE TABLE `establishment_features` (
  `Code` int(11) NOT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `FeatureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `establishment_features`
--

INSERT INTO `establishment_features` (`Code`, `EstablishmentID`, `FeatureID`) VALUES
(1, 19, 70),
(4, 19, 4),
(5, 19, 3),
(7, 20, 54),
(8, 20, 78),
(9, 20, 84),
(10, 21, 70),
(11, 21, 24),
(12, 21, 43),
(13, 21, 88),
(14, 21, 56),
(15, 21, 20),
(16, 19, 41),
(17, 22, 23),
(18, 22, 70),
(19, 22, 20),
(20, 22, 77),
(21, 22, 46),
(23, 22, 9),
(24, 22, 64);

-- --------------------------------------------------------

--
-- Table structure for table `establishment_owner`
--

CREATE TABLE `establishment_owner` (
  `OwnerID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `PositionTitle` varchar(50) DEFAULT NULL,
  `Institution` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `establishment_owner`
--

INSERT INTO `establishment_owner` (`OwnerID`, `UserID`, `PositionTitle`, `Institution`) VALUES
(1, 2, 'Co-developer', 'Balay.com'),
(2, 6, 'President-elect', 'United States of America'),
(3, 10, NULL, NULL),
(4, 11, NULL, NULL),
(5, 13, 'Chairperson', 'Department of Computing Sciences');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `establishment_payment_channel`
--

INSERT INTO `establishment_payment_channel` (`EPCID`, `EstablishmentID`, `PaymentChannel`, `AccountNumber`, `AccountName`, `Notes`, `IsHidden`) VALUES
(1, 19, 1, '', '', 'Proceed to the Cashier\\\'s office.', 0),
(2, 19, 2, '0952 569 6601', 'Ahmad Kiram A. Grar', 'Free of charge', 0),
(3, 19, 3, '0952 569 6601', 'Ahmad Kiram A. Grar', '', 0),
(4, 19, 5, 'hhhkhj', '45465456', '', 0);

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
  `Photo6` text DEFAULT NULL,
  `Description6` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `establishment_photos`
--

INSERT INTO `establishment_photos` (`PhotoID`, `EstablishmentID`, `Photo1`, `Description1`, `Photo2`, `Description2`, `Photo3`, `Description3`, `Photo4`, `Description4`, `Photo5`, `Description5`, `Photo6`, `Description6`) VALUES
(11, 19, 'est_photos/Photo_1_19_010520251457.jpg', NULL, 'est_photos/Photo_2_19_011520251557.jpg', 'dsasdas', 'est_photos/Photo_3_19_011720250736.png', '', 'est_photos/Photo_4_19_011720250736.jpeg', '', 'est_photos/Photo_5_19_011720250735.jpg', '', 'est_photos/Photo_6_19_011720250737.jpg', ''),
(16, 20, '', NULL, 'est_photos/Photo_2_20_011520251724.PNG', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `FeatureID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`FeatureID`, `Name`, `Icon`) VALUES
(1, 'Double-deck bed', 'bed'),
(2, 'Single-deck bed', 'bed'),
(3, 'Chairs', 'chair'),
(4, 'Couches', 'couch'),
(5, 'Cabinets', 'box-archive'),
(6, 'Storage space', 'door-open'),
(7, 'Laundry area', 'jug-detergent'),
(8, 'Kitchen', 'kitchen-set'),
(9, 'Shared bathroom', 'restroom'),
(10, 'Private bathroom', 'restroom'),
(11, 'Shower', 'shower'),
(12, 'Shared sink', 'sink'),
(13, 'Private sink', 'sink'),
(14, 'CCTV cameras', 'video'),
(19, 'Gas stoove', 'fire-burner'),
(20, 'Free Wi-Fi', 'wifi'),
(21, 'Free water supply', 'droplet'),
(22, 'Free electricity', 'bolt'),
(23, 'Air conditioning', 'wind'),
(24, 'Ceiling fan', 'fan'),
(25, 'Stand fan', 'fan'),
(26, 'Wall fan', 'fan'),
(27, 'Study area', 'book-open-reader'),
(28, 'Common room', 'people-roof'),
(29, 'Flat-screen TV', 'tv'),
(30, 'TV', 'tv'),
(31, 'Gaming console', 'gamepad'),
(32, 'Karaoke area', 'microphone'),
(33, 'Gym area', 'dumbbell'),
(34, 'Outdoor recreational space', 'shoe-prints'),
(35, 'Parking space', 'square-parking'),
(36, 'Sari-sari store', 'store'),
(37, 'Convenience store', 'store'),
(38, 'Garden area', 'leaf'),
(39, 'Rooftop', 'hotel'),
(40, 'Terraces', 'hotel'),
(41, 'Cats', 'cat'),
(42, 'Dogs', 'dog'),
(43, 'Disability support', 'wheelchair'),
(44, 'Elevator', 'elevator'),
(45, 'Clinic', 'kit-medical'),
(46, 'Pharmacy', 'kit-medical'),
(47, 'Emergency responsive', 'fire-extinguisher'),
(48, 'Fire extinguisher', 'fire-extinguisher'),
(49, 'Pesonet Wi-Fi', 'wifi'),
(50, 'Ticket Wi-Fi', 'wifi'),
(51, 'Strong cell signal', 'tower-broadcast'),
(52, 'Trash collection', 'trash-can'),
(53, 'Smart home features', 'house-signal'),
(54, 'Cool temperature', 'fan'),
(55, 'Warm temperature', 'wind'),
(56, 'Hot temperature', 'temperature-full'),
(57, 'Printing area', 'print'),
(58, 'Photocopying area', 'copy'),
(59, 'Computer shop', 'computer'),
(60, 'Library', 'book'),
(61, 'Online portal', 'globe'),
(62, 'Online payment', 'money-check'),
(63, 'Pool', 'person-swimming'),
(64, 'Security personnel', 'user-shield'),
(65, 'Janitorial personnel', 'broom'),
(66, 'Dining area', 'utensils'),
(67, 'Cafeteria', 'utensils'),
(68, 'Restaurant', 'utensils'),
(69, 'Canteen', 'utensils'),
(70, 'Basketball court', 'basketball'),
(71, 'Volleyball court', 'volleyball'),
(72, 'Table tennis facility', 'table-tennis-paddle-ball'),
(73, 'Bike storage', 'person-biking'),
(74, 'Church', 'church'),
(75, 'Christian worship place', 'cross'),
(76, 'Bible', 'book-bible'),
(77, 'Mosque', 'mosque'),
(78, 'Muslim prayer area', 'star-and-crescent'),
(79, 'Quran', 'book-quran'),
(80, 'Spa', 'spa'),
(81, 'Sauna', 'hot-tub-person'),
(82, 'Transport convenience', 'car'),
(83, 'Smoking area', 'Smoking'),
(84, 'No smoking', 'ban-smoking'),
(85, 'Quiet', 'volume-off'),
(87, 'Theme-based', 'masks-theater'),
(88, 'For honors', 'award'),
(89, 'Plug outlets', 'plug-circle-bolt');

-- --------------------------------------------------------

--
-- Table structure for table `geo_tags`
--

CREATE TABLE `geo_tags` (
  `GeoTagID` int(11) NOT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `Latitude` decimal(10,8) NOT NULL,
  `Longitude` decimal(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `geo_tags`
--

INSERT INTO `geo_tags` (`GeoTagID`, `EstablishmentID`, `Latitude`, `Longitude`) VALUES
(1, 19, '7.99516864', '124.26071524'),
(21, 20, '7.99787789', '124.25773911'),
(29, 22, '7.99419118', '124.25736461');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`OTP_ID`, `UserID`, `Purpose`, `OTP_Code`, `ExpiresAt`) VALUES
(190, 2, '', '291203', '2025-01-15 20:12:44'),
(191, 2, '', '502946', '2025-01-15 20:14:16'),
(192, 2, '', '482955', '2025-01-15 20:14:43'),
(193, 2, '', '459660', '2025-01-15 20:16:57'),
(194, 2, '', '416285', '2025-01-15 20:17:31'),
(195, 2, '', '428677', '2025-01-15 20:26:47'),
(196, 2, '', '770033', '2025-01-15 20:27:28'),
(197, 2, '', '097129', '2025-01-15 20:32:09'),
(198, 2, '', '465456', '2025-01-15 20:32:38'),
(199, 2, '', '364239', '2025-01-15 20:35:15'),
(200, 2, '', '914086', '2025-01-15 20:35:31'),
(201, 2, '', '950471', '2025-01-15 20:41:05'),
(202, 2, '', '678509', '2025-01-15 20:41:20');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payment_channel`
--

CREATE TABLE `payment_channel` (
  `ChannelID` int(11) NOT NULL,
  `ChannelName` varchar(50) NOT NULL,
  `ChannelLogo` text NOT NULL,
  `Type` enum('e-wallet','bank','credit card','debit card','cash','cheque','remittance') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_channel`
--

INSERT INTO `payment_channel` (`ChannelID`, `ChannelName`, `ChannelLogo`, `Type`) VALUES
(1, 'Cash', '/bookingapp/assets/paymentChannels/Cash.jpg', 'cash'),
(2, 'GCash', '/bookingapp/assets/paymentChannels/GCash.png', 'e-wallet'),
(3, 'Maya', '/bookingapp/assets/paymentChannels/Maya.png', 'e-wallet'),
(4, 'ApplePay', '/bookingapp/assets/paymentChannels/ApplePay.png', 'e-wallet'),
(5, 'ShopeePay', '/bookingapp/assets/paymentChannels/ShopeePay.png', 'e-wallet'),
(6, 'Google Pay', '/bookingapp/assets/paymentChannels/GooglePay.png', 'e-wallet'),
(7, 'BancNet', '/bookingapp/assets/paymentChannels/BancNet.png', 'bank'),
(8, 'BDO (Banco De Oro)', '/bookingapp/assets/paymentChannels/BDO.png', 'bank'),
(9, 'ChinaBank', '/bookingapp/assets/paymentChannels/ChinaBank.png', 'bank'),
(10, 'Landbank', '/bookingapp/assets/paymentChannels/LandBank.png', 'bank'),
(12, 'GCash', '/bookingapp/assets/paymentChannels/GCash.png', 'e-wallet'),
(13, 'Maya', '/bookingapp/assets/paymentChannels/Maya.png', 'e-wallet'),
(14, 'ApplePay', '/bookingapp/assets/paymentChannels/ApplePay.png', 'e-wallet'),
(15, 'ShopeePay', '/bookingapp/assets/paymentChannels/ShopeePay.png', 'e-wallet'),
(16, 'Google Pay', '/bookingapp/assets/paymentChannels/GooglePay.png', 'e-wallet'),
(17, 'BancNet', '/bookingapp/assets/paymentChannels/BancNet.png', 'bank'),
(18, 'BDO (Banco De Oro)', '/bookingapp/assets/paymentChannels/BDO.png', 'bank'),
(19, 'ChinaBank', '/bookingapp/assets/paymentChannels/ChinaBank.png', 'bank'),
(20, 'Landbank', '/bookingapp/assets/paymentChannels/LandBank.png', 'bank'),
(21, 'MasterCard', '/bookingapp/assets/paymentChannels/MasterCard.png', 'credit card'),
(22, 'PalawanPay', '/bookingapp/assets/paymentChannels/PalawanPay.png', 'e-wallet'),
(23, 'PNB (Philippine National Bank)', '/bookingapp/assets/paymentChannels/PNB.png', 'bank'),
(24, 'SeaBank', '/bookingapp/assets/paymentChannels/SeaBank.png', 'bank'),
(25, 'UnionBank', '/bookingapp/assets/paymentChannels/UnionBank.jpg', 'bank'),
(26, 'VISA', '/bookingapp/assets/paymentChannels/Visa.png', 'credit card');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`PersonID`, `ProfilePicture`, `FirstName`, `MiddleName`, `LastName`, `ExtName`, `Gender`, `DateOfBirth`, `ContactNumber`, `IsContactVerified`, `HomeAddress`, `Religion`) VALUES
(1, NULL, 'Mohammad Noor', 'G', 'Macalandong', NULL, 'Male', NULL, '0975 866 8146', 1, 'Marawi City', 'Islam'),
(2, '/bookingapp/user/profile-pictures/pbbm.jpeg', 'Ferdinand', 'Romualdez', 'Marcos', 'Jr.', 'Male', '1958-12-04', '0985 145 5508', 1, 'Malacanang Palace, Manila', ''),
(3, '/bookingapp/user/profile-pictures/sara.jpg', 'Sara', 'Zimmerman', 'Duterte-Carpio', NULL, 'Female', '1978-05-31', '0925 145 6789', 0, 'Davao City, Philippines', 'Christianity'),
(5, NULL, 'Jason', 'Alonto', 'Grar', '', 'Male', NULL, '0952 569 6601', 1, 'Marawi City', NULL),
(6, '/bookingapp/user/profile-pictures/donaldjtrump.jpg', 'Donald', 'John', 'Trump', 'Sr.', 'Male', '1946-06-14', '0912 238 4845', 1, 'Queens, New York City, United States', 'Christianity'),
(7, NULL, 'Kamala', 'Devi', 'Harris', '', 'Female', NULL, '0959 939 3919', 1, 'Oakland, California, United States', NULL),
(8, '/bookingapp/user/profile-pictures/IShowSpeed.jpg', 'Darren', 'Jason', 'Watkins', 'Jr.', 'Male', '2005-01-21', '0958 839 9929', 0, 'Cincinnati, Ohio, United States', 'Other religion'),
(9, NULL, 'Mudzna', 'Muin', 'Asakil', '', 'Female', NULL, '0995 995 9992', 0, 'Mindanao State University', NULL),
(10, NULL, 'Mark', 'Longhas', 'Delina', '', 'Female', NULL, '0957 773 7838', 1, 'Maraw City', NULL),
(11, NULL, 'Rodrigo', 'Roa', 'Duterte', '', 'Male', NULL, '0995 884 8488', 0, 'Davao City', NULL),
(12, NULL, 'Joseph', 'Ejercito', 'Estrada', '', 'Male', NULL, '0958 884 3848', 0, 'San Juan', NULL),
(13, '/bookingapp/user/profile-pictures/cicswade.jpg', 'Janice', 'Fortuna', 'Wade', '', 'Female', NULL, '0958 858 5858', 0, 'Luinab, Iligan City', NULL),
(14, NULL, 'Billie Joe', '', 'Armstrong', '', 'Male', NULL, '0995 993 9949', 0, 'Oakland, California', NULL),
(26, NULL, 'Dk-boy', 'Alonto', 'Grar', '', 'Male', NULL, '0954 995 9599', 0, 'Bacong, Marantao, Lanao del Sur', NULL);

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
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdateAt` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `residency`
--

INSERT INTO `residency` (`ResidencyID`, `TenantID`, `RoomID`, `DateOfEntry`, `DateOfExit`, `Status`, `CreatedAt`, `UpdateAt`) VALUES
(28, 4, 7, '2025-02-04', NULL, 'pending', '2025-01-10 11:05:04', '2025-01-15 09:59:01'),
(33, 6, 7, '2025-01-30', NULL, 'currently residing', '2025-01-13 05:13:36', '2025-01-15 10:16:25'),
(34, 4, 13, '2025-01-24', NULL, 'confirmed', '2025-01-16 00:37:48', '2025-01-16 00:43:01'),
(35, 6, 14, '2025-01-15', NULL, 'pending', '2025-01-19 17:25:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `residency_status`
--

CREATE TABLE `residency_status` (
  `StatusID` int(11) NOT NULL,
  `ResidencyID` int(11) DEFAULT NULL,
  `Status` enum('pending','confirmed','cancelled','ended') DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `RoomID`, `TenantID`, `StaffScore`, `FacilitiesScore`, `CleanlinessScore`, `ComfortScore`, `MoneyValueScore`, `LocationScore`, `SignalScore`, `SecurityScore`, `UpdatedAt`, `CreatedAt`, `Comments`, `IsDeleted`) VALUES
(4, 12, 6, 1, 10, 5, 3, 10, 1, 4, 4, '2025-01-12 22:09:38', '2025-01-12 22:00:54', 'Sucks', 0),
(6, 12, 6, 1, 6, 10, 7, 9, 3, 10, 9, '2025-01-12 22:16:46', '2025-01-12 22:16:46', 'Fucking amazing!', 0),
(7, 12, 6, 1, 6, 10, 7, 10, 3, 10, 9, '2025-01-12 22:17:28', '2025-01-12 22:17:28', 'Fucking amazing!', 0),
(9, 12, 6, 1, 1, 1, 1, 1, 1, 1, 1, '2025-01-12 22:23:39', '2025-01-12 22:23:39', '', 0),
(13, 12, 4, 1, 1, 1, 1, 3, 1, 1, 1, '2025-01-13 00:13:47', '2025-01-12 23:35:14', 'If I could find you now, things would get better\\r\\nWe could leave this town and run forever\\r\\nI know somewhere, somehow, we\\\'ll be together\\r\\nLet your waves crash down on me and take me away', 0),
(14, 7, 4, 1, 10, 1, 4, 6, 1, 1, 1, '2025-01-14 16:55:03', '2025-01-13 00:02:17', 'Dickhead, ****face, cock-smoking, mother****er,\\r\\nasshole, dirty-thwart, waste of semen, I hope you die. Shit. Bullshit', 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`RoomID`, `RoomName`, `RoomType`, `PaymentRate`, `PaymentOptions`, `PaymentStructure`, `Availability`, `EstablishmentID`, `FloorLocation`, `PaymentRules`, `Photo`, `GenderInclusiveness`, `MaxOccupancy`) VALUES
(7, 'Gogo CPA', 'Single occupancy', '1500.00', 'Monthly', 'Per room', 'Deleted', 19, 3, '', 'pictures/Gogo CPA_19_Single occupancy.jpg', 'Males only', 0),
(8, 'Saguiaran Bridge', 'Double occupancy', '1500.00', 'Monthly', 'Per person', 'Available', 19, 3, 'One-month deposit; one-month advance', 'pictures/Saguiaran Bridge_19_Single occupancy.jpg', 'Males only', 1),
(9, 'dds', 'Three-bedroom apartment', '500.00', 'Monthly', 'Per bed', 'Available', 19, 1, '', 'pictures/dds_19_Single occupancy.jpg', 'Males only', 3),
(10, 'sas', 'Single occupancy', '1900.00', 'Monthly', '', 'Deleted', 19, 1, 'dad', 'pictures/sas_19_Single occupancy.jpg', 'Co-ed', 1),
(11, 'Music Room', 'Quad occupancy', '500.00', 'Monthly', 'Per bed', 'Available', 20, 1, '', 'pictures/Music Room_20_Single occupancy.jpg', 'Co-ed', 4),
(12, 'CICS Lobby', 'Studio apartment', '4000.00', 'Monthly', '', 'Available', 19, 2, 'dsdsadsa', 'pictures/CICS Lobby_19_Studio apartment.jpg', 'Co-ed', 4),
(13, 'Musem', 'Single occupancy', '500.00', 'Monthly', '', 'Available', 20, 2, '', 'pictures/Musem_20_Single occupancy.png', 'Co-ed', 1),
(14, 'VIP room', '', '2500.00', '', 'Per room', 'Available', 22, 5, 'One-year deposit', 'pictures/VIP room_22_Luxury suite.jpg', 'Co-ed', 4);

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `Code` int(11) NOT NULL,
  `RoomID` int(11) DEFAULT NULL,
  `FeatureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room_features`
--

INSERT INTO `room_features` (`Code`, `RoomID`, `FeatureID`) VALUES
(10, 7, 23),
(11, 7, 70),
(22, 7, 78),
(23, 7, 20),
(24, 7, 10),
(25, 7, 13),
(26, 7, 25),
(27, 8, 54),
(28, 9, 1),
(29, 9, 37);

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `TenantID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UniversityID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`TenantID`, `UserID`, `UniversityID`) VALUES
(1, 3, '201122007'),
(2, 5, '10740923'),
(3, 7, '201994992'),
(4, 8, '199393922'),
(5, 14, '200049921'),
(6, 15, '1074092');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`UserID`, `PersonID`, `EmailAddress`, `IsEmailVerified`, `Username`, `Password`, `DateCreated`, `Role`, `Status`) VALUES
(1, 2, 'pbbm@gov.ph', 1, 'pbbm', '3cf25e63e1920876dff972c69009f49a', '2024-11-11 12:00:51', 'admin', 'pending'),
(2, 1, 'noor.balay@gmail.com', 0, 'noor', 'cicsmarter', '2024-12-03 15:12:58', 'owner', 'pending'),
(3, 3, 'vp@gov.ph', 0, 'vpsaraduterte', 'd89690743a07e71485ab5d5abedc0253', '2024-12-03 15:24:46', 'tenant', 'pending'),
(5, 5, '3232@gmail.com', 1, '3232', '4fc27d7f1968c691d7ff3f4a2d2f36cf', '2024-12-08 17:48:47', 'tenant', 'pending'),
(6, 6, 'donaldjtrump@trump.com', 1, 'donaldjtrump', 'aebe8914c006165d540a019d4b6a2415', '2024-12-09 20:49:02', 'owner', 'pending'),
(7, 7, 'kamalaharris@s.msumain.edu.ph', 1, 'vp_usa', '5514c8959e1a7faa9a36a8ed818250fc', '2024-12-09 21:12:06', 'tenant', 'pending'),
(8, 8, 'ishowspeed@gmail.com', 1, 'IShowSpeed', '78b18b51641a3d8ea260e91d7d05295a', '2024-12-09 21:25:33', 'tenant', 'pending'),
(9, 9, 'cics@msumain.edu.ph', 1, 'cicsdean', 'e83db6176673f63f29aaad69979e760f', '2024-12-09 21:49:47', 'admin', 'pending'),
(10, 10, '123@gmail.com', 1, 'Helloffo', '25d55ad283aa400af464c76d713c07ad', '2024-12-17 12:46:54', 'owner', 'pending'),
(11, 11, 'prrd@gmail.com', 0, 'prrd', 'b5e94ca302d6263cb0ec8df4493b1869', '2025-01-10 09:28:36', 'owner', 'pending'),
(12, 12, 'erap@gmail.com', 0, 'pres.erap', '008ebbc8704645265d67c244d6853fb3', '2025-01-10 16:46:49', 'admin', 'pending'),
(13, 13, 'janicewade@msumain.edu.ph', 0, 'cics.wade', '22d7fe8c185003c98f97e5d6ced420c7', '2025-01-10 16:49:55', 'owner', 'pending'),
(14, 14, 'comingclean@dookie.net', 1, 'billiejoearmstrong', 'e90521f137961dcf5520d50cde66e7a3', '2025-01-12 13:25:37', 'tenant', 'pending'),
(15, 26, 'grar.aa07@s.msumain.edu.ph', 1, '1074092', '4fc27d7f1968c691d7ff3f4a2d2f36cf', '2025-01-13 03:44:01', 'tenant', 'pending');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_socials`
--

INSERT INTO `user_socials` (`SocialID`, `FacebookURL`, `TwitterX`, `Instagram`, `YouTube`, `TikTok`, `LinkedIn`, `Website`, `PersonID`) VALUES
(1, 'https://www.facebook.com/BongbongMarcos', 'https://twitter.com/bongbongmarcos', 'https://www.tiktok.com/@bongbong.marcos', '', 'https://www.tiktok.com/@bongbong.marcos', '', 'https://www.pbbm.com', 2),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5),
(6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6),
(7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7),
(8, '', '', '', 'https://www.youtube.com/IShowSpeed', '', '', 'https://www.twitch.tv/IShowSpeed', 8),
(9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9),
(10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10),
(11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11),
(12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12),
(13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13),
(14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14),
(15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 26);

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
  MODIFY `Code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `establishment_owner`
--
ALTER TABLE `establishment_owner`
  MODIFY `OwnerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `establishment_payment_channel`
--
ALTER TABLE `establishment_payment_channel`
  MODIFY `EPCID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `establishment_photos`
--
ALTER TABLE `establishment_photos`
  MODIFY `PhotoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `FeatureID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `geo_tags`
--
ALTER TABLE `geo_tags`
  MODIFY `GeoTagID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `OTP_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payment_channel`
--
ALTER TABLE `payment_channel`
  MODIFY `ChannelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `PersonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `residency`
--
ALTER TABLE `residency`
  MODIFY `ResidencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `Code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tenant`
--
ALTER TABLE `tenant`
  MODIFY `TenantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_socials`
--
ALTER TABLE `user_socials`
  MODIFY `SocialID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `delete_expired_otps` ON SCHEDULE EVERY 3 MINUTE STARTS '2024-12-08 23:15:37' ON COMPLETION NOT PRESERVE ENABLE DO delete from otp_verifications where expiry_time < now()$$

CREATE DEFINER=`root`@`localhost` EVENT `update_residency_status` ON SCHEDULE EVERY 1 DAY STARTS '2025-01-06 11:26:07' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE residency 
    SET Status = "currently residing" 
    WHERE DateOfEntry >= CURDATE() AND Status = 'confirmed';
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
