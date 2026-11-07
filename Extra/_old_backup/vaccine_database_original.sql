-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2026 at 12:09 PM
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
-- Database: `vaccine_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `vaccination_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `hospital_name`, `vaccination_status`, `created_at`, `parent_id`, `child_id`) VALUES
(1, 'Patel Hospital', 'Pending', '2026-10-29 10:21:43', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `child_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `allergies` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vaccination_date` date DEFAULT NULL,
  `last_vaccine` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `parent_id`, `child_name`, `date_of_birth`, `gender`, `allergies`, `created_at`, `vaccination_date`, `last_vaccine`) VALUES
(1, 2, 'adil hussain', '2016-10-29', 'Male', 'peanut', '2026-10-29 03:29:33', '2026-10-15', 'Pentavalent-1');

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
(1, 'Ahmed Hospital', 'Dr taimoor', 'drtaimoor@gmail.com', '3183905647', 'Karachi East', '$2y$10$K2MvE2PAM.KagwE100sd/unkLWzSU3RzZ1wunRyoDujZ1g7DwMs8y', 'naya nazimabad', '2026-10-24 13:36:38', 'pending'),
(2, 'Patel Hospital', 'Dr Abdul-Rehman', 'abdulrehman@gmail.com', '034856789011', 'Karachi Central', '$2y$10$GTzDpmPjh7JAnaObg/pnj.lS3ScLQ0x/BkTkU9f/6G6qR7.yazH3K', 'nazimabad', '2026-10-25 08:39:23', 'accepted'),
(3, 'Bahria Town International Hospital', 'Dr.Jameel', 'jameel@gmail.com', '031190674523', 'Karachi Central', '$2y$10$07S67Sys/O4YYRGGip0beO.8E49iFtBKGA7vXrsLF/SyGdPaa0yY.', 'bahria town karachi', '2026-10-28 11:16:38', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_vaccine_stock`
--

CREATE TABLE `hospital_vaccine_stock` (
  `stock_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_vaccine_stock`
--

INSERT INTO `hospital_vaccine_stock` (`stock_id`, `hospital_id`, `vaccine_id`, `quantity`, `updated_at`) VALUES
(1, 2, 9, 30, '2026-10-25 21:00:20'),
(2, 2, 8, 50, '2026-10-27 13:06:32'),
(3, 2, 10, 20, '2026-10-27 13:17:28'),
(4, 2, 12, 20, '2026-10-27 13:17:34'),
(5, 2, 2, 10, '2026-10-28 11:13:26'),
(8, 3, 13, 34, '2026-10-28 11:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `Relation_with_child` varchar(50) NOT NULL,
  `Phone_no` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `CNIC` varchar(13) NOT NULL,
  `Hashed_password` varchar(255) NOT NULL,
  `City` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `name`, `Relation_with_child`, `Phone_no`, `email`, `CNIC`, `Hashed_password`, `City`, `address`, `created_at`) VALUES
(1, 'Abdul Rafay', 'father', '03210985749', 'arafay@gmail.com', '4573987509690', '$2y$10$2R.ujvXJIKXEuZ8lRoj4WO300B5hvC.BxHQrsBI4Lgxa05hWO/Aqu', 'karachi', 'aptech north karachi', '2026-10-28 17:00:57'),
(2, 'samra ali', 'mother', '03298756843', 'saraaa@gmail.com', '4220891728916', '$2y$10$eou/X5zqG5wZxUP7E59Kt.YPW81vW3rbAJ2F/rZZW1c.pKGrZgAyS', 'karachi', 'aptech north karachi', '2026-10-28 17:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `Vaccine_ID` int(11) NOT NULL,
  `Vaccine_Name` varchar(150) NOT NULL,
  `Dose` int(11) NOT NULL,
  `Age_Limit` varchar(50) NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`Vaccine_ID`, `Vaccine_Name`, `Dose`, `Age_Limit`, `Status`, `description`) VALUES
(1, 'BCG', 1, 'At birth', 'Active', 'BCG vaccine protects against severe forms of tuberculosis, especially in children.'),
(2, 'OPV-0', 1, 'At birth', 'Active', 'OPV-0 is an oral polio vaccine dose given at birth under the applicable immunization schedule.'),
(3, 'Hepatitis B', 1, 'At birth', 'Inactive', 'Hepatitis B vaccine protects against hepatitis B virus infection, which can damage the liver.'),
(4, 'Pentavalent-1', 1, '6 weeks', 'Active', 'Pentavalent-1 protects against diphtheria, tetanus, pertussis, hepatitis B, and Haemophilus influenzae type b.'),
(5, 'OPV-1', 1, '6 weeks', 'Active', 'OPV-1 is an oral polio vaccine dose that helps protect against poliovirus.'),
(6, 'Rotavirus-1', 1, '6 weeks', 'Active', 'Rotavirus-1 is the first rotavirus vaccine dose, helping protect infants against severe rotavirus diarrhea.'),
(7, 'PCV-1', 1, '6 weeks', 'Active', 'PCV-1 is the first pneumococcal conjugate vaccine dose, helping protect against pneumococcal infections.'),
(8, 'Pentavalent-2', 2, '10 weeks', 'Active', 'Pentavalent-2 is a subsequent dose that continues protection against five targeted infections.'),
(9, 'OPV-2', 2, '10 weeks', 'Active', 'OPV-2 is an oral polio vaccine dose that helps build protection against poliovirus.'),
(10, 'Pentavalent-3', 3, '14 weeks', 'Active', 'Pentavalent-3 is a subsequent dose that completes the primary pentavalent vaccine series under the applicable schedule.'),
(11, 'OPV-3 / IPV', 3, '14 weeks', 'Active', 'OPV-3 / IPV provides polio protection through oral and inactivated polio vaccine doses, according to the schedule.'),
(12, 'Measles-1', 1, '9 months', 'Active', 'Measles-1 is the first dose intended to protect children against measles.'),
(13, 'MMR', 1, '15 months', 'Active', 'MMR vaccine protects against measles, mumps, and rubella.'),
(14, 'Polio', 1, '0-5 years', 'Active', 'Polio vaccine helps protect against poliovirus, which can cause paralysis.');

-- --------------------------------------------------------

--
-- Table structure for table `vaccine_request`
--

CREATE TABLE `vaccine_request` (
  `Dose_ID` int(11) NOT NULL,
  `Vaccine_ID` int(11) NOT NULL,
  `Hospital_ID` int(11) NOT NULL,
  `Dose_Number` int(11) NOT NULL,
  `child_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_parent` (`parent_id`),
  ADD KEY `appointments_child` (`child_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `children_parent` (`parent_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hospital_vaccine_stock`
--
ALTER TABLE `hospital_vaccine_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD UNIQUE KEY `hospital_vaccine` (`hospital_id`,`vaccine_id`),
  ADD KEY `stock_vaccine` (`vaccine_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`Vaccine_ID`),
  ADD UNIQUE KEY `Vaccine_Name` (`Vaccine_Name`);

--
-- Indexes for table `vaccine_request`
--
ALTER TABLE `vaccine_request`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hospital_vaccine_stock`
--
ALTER TABLE `hospital_vaccine_stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `Vaccine_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vaccine_request`
--
ALTER TABLE `vaccine_request`
  MODIFY `Dose_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `appointments_child` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hospital_vaccine_stock`
--
ALTER TABLE `hospital_vaccine_stock`
  ADD CONSTRAINT `stock_hospital` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_vaccine` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`Vaccine_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vaccine_request`
--
ALTER TABLE `vaccine_request`
  ADD CONSTRAINT `vaccine_request_ibfk_1` FOREIGN KEY (`Vaccine_ID`) REFERENCES `vaccines` (`Vaccine_ID`),
  ADD CONSTRAINT `vaccine_request_ibfk_2` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospitals` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
