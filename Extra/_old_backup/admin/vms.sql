-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2026 at 03:54 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vaccine_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `child_name` varchar(100) NOT NULL,
  `parent_email` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `vaccination_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `authorized_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `city` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `STATUS` enum('pending','accepted','rejected') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `hospital_name`, `authorized_name`, `email`, `contact_number`, `city`, `password`, `address`, `created_at`, `STATUS`) VALUES
(1, 'Ahmed Hospital', 'Dr taimoor', 'drtaimoor@gmail.com', '3183905647', 'Karachi East', '$2y$10$K2MvE2PAM.KagwE100sd/unkLWzSU3RzZ1wunRyoDujZ1g7DwMs8y', 'naya nazimabad', '2026-10-24 13:36:38', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `PID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `Relation_with_child` enum('Mother','Father','Guardian') DEFAULT 'Guardian',
  `Phone_no` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `CNIC` char(13) NOT NULL,
  `City` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `Hashed_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `Vaccine_ID` int(11) NOT NULL,
  `Vaccine_Name` varchar(100) NOT NULL,
  `Dose` int(11) NOT NULL,
  `Age_Limit` varchar(50) NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaccine_doses`
--

CREATE TABLE `vaccine_doses` (
  `Dose_ID` int(11) NOT NULL,
  `Vaccine_ID` int(11) NOT NULL,
  `Hospital_ID` int(11) NOT NULL,
  `Dose_Number` int(11) NOT NULL,
  `Age_Limit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`PID`),
  ADD UNIQUE KEY `Phone_no` (`Phone_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `CNIC` (`CNIC`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`Vaccine_ID`);

--
-- Indexes for table `vaccine_doses`
--
ALTER TABLE `vaccine_doses`
  ADD PRIMARY KEY (`Dose_ID`),
  ADD UNIQUE KEY `Vaccine_ID` (`Vaccine_ID`),
  ADD UNIQUE KEY `Hospital_ID` (`Hospital_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `PID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `Vaccine_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vaccine_doses`
--
ALTER TABLE `vaccine_doses`
  MODIFY `Dose_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `vaccine_doses`
--
ALTER TABLE `vaccine_doses`
  ADD CONSTRAINT `vaccine_doses_ibfk_1` FOREIGN KEY (`Vaccine_ID`) REFERENCES `vaccines` (`Vaccine_ID`),
  ADD CONSTRAINT `vaccine_doses_ibfk_2` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospitals` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
