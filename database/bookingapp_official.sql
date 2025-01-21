-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2025 at 08:16 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `establishment_features`
--

CREATE TABLE `establishment_features` (
  `Code` int(11) NOT NULL,
  `EstablishmentID` int(11) DEFAULT NULL,
  `FeatureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `FeatureID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `Code` int(11) NOT NULL,
  `RoomID` int(11) DEFAULT NULL,
  `FeatureID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `TenantID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UniversityID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_methods`
--
ALTER TABLE `auth_methods`
  MODIFY `AuthID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment`
--
ALTER TABLE `establishment`
  MODIFY `EstablishmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment_features`
--
ALTER TABLE `establishment_features`
  MODIFY `Code` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment_owner`
--
ALTER TABLE `establishment_owner`
  MODIFY `OwnerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment_payment_channel`
--
ALTER TABLE `establishment_payment_channel`
  MODIFY `EPCID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `establishment_photos`
--
ALTER TABLE `establishment_photos`
  MODIFY `PhotoID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `FeatureID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geo_tags`
--
ALTER TABLE `geo_tags`
  MODIFY `GeoTagID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `OTP_ID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `PersonID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `residency`
--
ALTER TABLE `residency`
  MODIFY `ResidencyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `residency_status`
--
ALTER TABLE `residency_status`
  MODIFY `StatusID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_responses`
--
ALTER TABLE `review_responses`
  MODIFY `ResponseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `Code` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant`
--
ALTER TABLE `tenant`
  MODIFY `TenantID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_socials`
--
ALTER TABLE `user_socials`
  MODIFY `SocialID` int(11) NOT NULL AUTO_INCREMENT;

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
