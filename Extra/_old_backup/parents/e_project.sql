-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 12:35 PM
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
-- Database: `e_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `PID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `Relation_with_child` enum('Mother','Father','Guardian') DEFAULT 'Guardian',
  `Phone_no` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `CNIC` char(13) NOT NULL,
  `Hashed_password` varchar(255) NOT NULL,
  `City` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`PID`, `name`, `Relation_with_child`, `Phone_no`, `email`, `CNIC`, `Hashed_password`, `City`, `address`) VALUES
(2, 'adil hussain', '', '3111234567', 'adil@gmail.com', '1234567890123', '$2y$10$UmgC/xQ6sfBhAfyuFHBRUe4kQ7c3TJkEg7g0c9fWwoQkyGjFrvFNa', 'karachi', 'aptech north karachi'),
(8, 'ali', 'Guardian', '75984373798', 'ali@gmail.com', '5793427599867', '$2y$10$5pLvMbXpB1TozHmGFspf8.HGIJPM3CeX3tEJq5GPopSP91Rz39MT.', 'karachi', 'aptech north karachi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`PID`),
  ADD UNIQUE KEY `Phone_no` (`Phone_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `CNIC` (`CNIC`),
  ADD UNIQUE KEY `Phone_no_2` (`Phone_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `PID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
