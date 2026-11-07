-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2026 at 09:15 AM
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
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `username`, `password_hash`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$vyUMlZh3KohhWfZyiGSsbONvXGfHGTHX7uRXY4EerF9Qia00ENuhC', 'uploads/profile-images/2340221060e2ce057eb9bbacd1d7851a.jpg', '2026-11-04 09:14:32', '2026-11-04 09:15:34');

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
  `child_id` int(11) NOT NULL,
  `vaccination_date` date DEFAULT NULL,
  `vaccine_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `hospital_name`, `vaccination_status`, `created_at`, `parent_id`, `child_id`, `vaccination_date`, `vaccine_id`) VALUES
(1, 'Patel Hospital', 'Pending', '2026-10-29 10:21:43', 2, 1, NULL, NULL),
(2, 'Patel Hospital', 'Pending', '2026-11-03 09:25:44', 3, 2, NULL, NULL),
(3, 'Aga Khan University Hospital', 'Admin Approved', '2026-11-03 09:29:47', 3, 3, '2026-11-03', NULL),
(5, 'Patel Hospital', 'Vaccinated', '2026-11-05 13:54:52', 6, 9, '2026-11-05', NULL);

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
(1, 2, 'adil hussain', '2016-10-29', 'Male', 'peanut', '2026-10-29 03:29:33', '2026-10-15', 'Pentavalent-1'),
(2, 3, 'Muhammad Ahmed', '2025-01-01', 'Male', '', '2026-11-03 09:20:04', NULL, NULL),
(3, 3, 'Muhammad Asif', '2026-11-01', 'Male', '', '2026-11-03 09:29:38', NULL, NULL),
(5, 1, 'fatima', '2024-06-19', 'Female', '', '2026-11-03 12:34:37', NULL, NULL),
(6, 5, 'hadi', '2023-11-02', 'Male', 'OPV', '2026-11-05 13:03:34', '2026-02-02', 'OPV-0'),
(7, 5, 'Fatima', '2020-11-04', 'Female', 'opv', '2026-11-05 13:08:55', NULL, NULL),
(9, 6, 'ailyan', '2022-12-04', 'Male', 'PCV_2', '2026-11-05 13:18:47', '2026-08-11', 'PCV-1'),
(10, 9, 'alishba', '2021-03-12', 'Female', 'MMR', '2026-11-05 13:27:42', '2026-09-11', 'Measles-1'),
(11, 8, 'Amna', '2025-05-07', 'Female', 'Rotavirus_1', '2026-11-05 13:37:54', '2026-02-12', 'Pentavalent-1');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `role` varchar(20) NOT NULL,
  `address` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_feedback`
--

CREATE TABLE `customer_feedback` (
  `id` int(11) NOT NULL,
  `display_name` varchar(60) DEFAULT NULL,
  `role` varchar(20) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review` varchar(700) NOT NULL,
  `public_consent` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
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
  `STATUS` enum('pending','accepted','rejected') DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `hospital_name`, `authorized_name`, `email`, `contact_number`, `city`, `password`, `address`, `created_at`, `STATUS`, `profile_image`) VALUES
(1, 'Ahmed Hospital', 'Dr taimoor', 'drtaimoor@gmail.com', '3183905647', 'Karachi East', '$2y$10$K2MvE2PAM.KagwE100sd/unkLWzSU3RzZ1wunRyoDujZ1g7DwMs8y', 'naya nazimabad', '2026-10-24 13:36:38', 'accepted', NULL),
(2, 'Patel Hospital', 'Dr Abdul-Rehman', 'abdulrehman@gmail.com', '034856789011', 'Karachi Central', '$2y$10$GTzDpmPjh7JAnaObg/pnj.lS3ScLQ0x/BkTkU9f/6G6qR7.yazH3K', 'nazimabad', '2026-10-25 08:39:23', 'accepted', NULL),
(3, 'Bahria Town International Hospital', 'Dr.Jameel', 'jameel@gmail.com', '031190674523', 'Karachi Central', '$2y$10$07S67Sys/O4YYRGGip0beO.8E49iFtBKGA7vXrsLF/SyGdPaa0yY.', 'bahria town karachi', '2026-10-28 11:16:38', 'accepted', NULL),
(4, 'Aga Khan University Hospital', 'Dr Ahmed', 'aga@gmail.com', '032158556555', 'Karachi Central', '$2y$10$fn5DfaSJYjtNIc9XsZn.6eywzQJt4MZHI.ma0xNfoebRXeRmRpn6W', 'Karachi', '2026-11-03 09:28:39', 'accepted', 'uploads/profile-images/16bdf3d52363266c738a0d70859e13e7.jpg');

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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipient_type` enum('parent','hospital','admin') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `message` varchar(500) NOT NULL,
  `target_url` varchar(255) NOT NULL DEFAULT '',
  `event_key` varchar(190) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_type`, `recipient_id`, `title`, `message`, `target_url`, `event_key`, `created_at`, `read_at`) VALUES
(1, 'hospital', 2, 'New appointment request', 'ailyan has an appointment request waiting for review.', 'hospital/hospital_dashboard.php', 'booking-5-created-hospital', '2026-11-05 13:54:52', NULL),
(2, 'admin', 0, 'New appointment booked', 'A new appointment for ailyan at Patel Hospital is waiting for hospital review.', 'admin/admin.php', 'booking-5-created-admin', '2026-11-05 13:54:52', NULL),
(3, 'parent', 6, 'Appointment approved', 'Your appointment for ailyan has been approved by the hospital.', 'parents/vaccination_report.php', 'booking-status-5-hospital-approved-parent', '2026-11-05 13:57:53', NULL),
(4, 'parent', 6, 'Vaccination recorded', 'The hospital recorded a vaccination for ailyan. The record is awaiting administrator review.', 'parents/vaccination_report.php', 'booking-status-5-vaccinated-parent', '2026-11-05 13:59:38', NULL),
(5, 'admin', 0, 'Vaccination record needs review', 'The hospital recorded a vaccination for ailyan. Please review the record.', 'admin/vaccination_report.php', 'booking-status-5-vaccinated-admin', '2026-11-05 13:59:38', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `name`, `Relation_with_child`, `Phone_no`, `email`, `CNIC`, `Hashed_password`, `City`, `address`, `created_at`, `profile_image`) VALUES
(1, 'Abdul Rafay', 'father', '03210985749', 'arafay@gmail.com', '4573987509690', '$argon2id$v=19$m=65536,t=4,p=1$TGpvNGVxUEpJU3J2STBBTQ$VNj8/GbmONm+Lx5OSqcQ7VPugJTx6bNlYjTBFi/HLDs', 'karachi', 'aptech north karachi', '2026-10-28 17:00:57', NULL),
(2, 'samra ali', 'mother', '03298756843', 'saraaa@gmail.com', '4220891728916', '$2y$10$eou/X5zqG5wZxUP7E59Kt.YPW81vW3rbAJ2F/rZZW1c.pKGrZgAyS', 'karachi', 'aptech north karachi', '2026-10-28 17:01:47', NULL),
(3, 'M Ali', 'guardian', '03014785236', 'ali@gmail.com', '4210147852369', '$2y$10$EzMBkPHBB2xxfNUcRYbaleemulSlXF3ht11CniIKomNbuzExgsodi', 'Karachi', 'karachi', '2026-11-03 09:09:09', 'uploads/profile-images/eac5bd2c97b95ede3977482ef0657278.png'),
(5, 'sara', 'mother', '03312345678', 'sara@gmail.com', '4210123456789', '$2y$10$jJzTF6lf8D3ORji5wi/gk.G6oGkubQE7uftGeXKHNDayvWvlsh1cC', 'karachi', 'north karachi sector l_234', '2026-11-05 12:31:17', 'uploads/profile-images/2b838481293cb38eac7758ca9ce8973f.jpg'),
(6, 'mahnoor', 'mother', '03151234568', 'mahnoor@gmail.com', '4210234567898', '$2y$10$rR.Lcioes7XKs1JbNF71N.YkNKXujzTOJ3XhhP4kne7uSIKdNEuSe', 'karachi', 'bafferzone sector 15 R_123', '2026-11-05 12:38:12', 'uploads/profile-images/1ce2fa30fa63704169766035e303f783.jpg'),
(7, 'daniyal', 'father', '03345678925', 'daniyal@gmail.com', '4210987654321', '$2y$10$kJDjGrSatmWLzcviRbCRO.EWwo0aRD3wV1Fir0RoqMEHiVqQPZLOC', 'karachi', 'north nazimabad H_456 sector 4C', '2026-11-05 12:43:54', NULL),
(8, 'Abdul wasay', 'father', '03452456713', 'wasay@gmail.com', '4210345720862', '$2y$10$Pk8lw5cXPkevptzPCz0CGuTPopqkpgCj/tfkFbBm6i2tfYJHjnti2', 'karachi', 'hydri 123', '2026-11-05 12:47:35', 'uploads/profile-images/73d5bd74e3cb80d30902e08d5023ec53.jpg'),
(9, 'haseeb', 'father', '03245680173', 'haseeb@gmail.com', '4210987563212', '$2y$10$3iukmPAgtOgabd0F9ENiJ.Sgt7Rnr.g2EAFr8DWmPgh9rdWMzUNKW', 'karachi', 'new karachi sector 5 l_235', '2026-11-05 12:51:26', 'uploads/profile-images/e2e09daa16269e37d508ba46987286bd.jpg'),
(10, 'hamad', 'father', '03143689203', 'hamad@gmail.com', '4210579246925', '$2y$10$r1xdQwTQQWNMKs8AE9DK..UKoudBsVc2o74yG/v5DnR9M5v0SqjEq', 'karachi', '4k sector 5B R_678', '2026-11-05 12:54:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parent_password_reset_requests`
--

CREATE TABLE `parent_password_reset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `parent_name` varchar(100) NOT NULL,
  `parent_email` varchar(100) NOT NULL,
  `status` enum('Pending','Approved','Link Generated','Sent','Completed','Rejected') NOT NULL DEFAULT 'Pending',
  `status_token_hash` char(64) NOT NULL,
  `reset_token_hash` char(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_password_reset_requests`
--

INSERT INTO `parent_password_reset_requests` (`id`, `parent_id`, `parent_name`, `parent_email`, `status`, `status_token_hash`, `reset_token_hash`, `created_at`, `updated_at`) VALUES
(1, 1, 'Abdul Rafay', 'arafay@gmail.com', 'Link Generated', '1c25db4135b33899d082236586dff06ca6febdddd788ea22ad9f947de4710ae1', '192fc341defb9d66e128b43b9aed7ff55e44ab9ab37d5a517bf410c2fe498d7f', '2026-11-06 11:37:21', '2026-11-06 11:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_type` enum('parent','hospital') NOT NULL,
  `account_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `account_type`, `account_id`, `token_hash`, `created_at`, `expires_at`, `used_at`) VALUES
(1, 'parent', 1, '011f4eadfeef52287a6ee4e325a38a011860cd01e0dd68dd00d8bf7365867857', '2026-11-06 10:32:41', '2026-11-06 16:32:41', '2026-11-06 15:32:54'),
(2, 'parent', 1, '4875997c55856ba0ba0d62810399fbb5b0759168f47654874d7e0a7db6631526', '2026-11-06 10:32:54', '2026-11-06 16:32:54', '2026-11-06 15:33:38'),
(3, 'parent', 1, '8eaa8a516349b3bacdff966ddf2f5012e591f4fc40581ab746d4b3962eee75b7', '2026-11-06 10:53:28', '2026-11-06 16:53:28', '2026-11-06 15:53:38'),
(4, 'parent', 1, '7b93abf2d507b120aaf7b44340e6f0af0e56690cb8afd621be12acedceb59a91', '2026-11-06 10:53:38', '2026-11-06 16:53:38', '2026-11-06 15:57:45'),
(5, 'parent', 1, '965d878bc8cbd24b472c75da11b74b7e44572326262fe98427a85b0919f72dc2', '2026-11-06 10:57:45', '2026-11-06 16:57:45', '2026-11-06 15:59:14'),
(6, 'parent', 1, '192fc341defb9d66e128b43b9aed7ff55e44ab9ab37d5a517bf410c2fe498d7f', '2026-11-06 11:38:10', '2026-11-06 17:38:10', NULL);

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
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_accounts_username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_parent` (`parent_id`),
  ADD KEY `appointments_child` (`child_id`),
  ADD KEY `appointments_vaccine_idx` (`vaccine_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `children_parent` (`parent_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_approval_date` (`is_approved`,`created_at`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notifications_event_key` (`event_key`),
  ADD KEY `notifications_recipient_created` (`recipient_type`,`recipient_id`,`created_at`),
  ADD KEY `notifications_recipient_read` (`recipient_type`,`recipient_id`,`read_at`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parent_password_reset_requests`
--
ALTER TABLE `parent_password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parent_reset_status_token` (`status_token_hash`),
  ADD KEY `parent_reset_status_created` (`status`,`created_at`),
  ADD KEY `parent_reset_parent` (`parent_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_reset_token_hash` (`token_hash`),
  ADD KEY `password_reset_account_created` (`account_type`,`account_id`,`created_at`),
  ADD KEY `password_reset_expiry` (`expires_at`,`used_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hospital_vaccine_stock`
--
ALTER TABLE `hospital_vaccine_stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `parent_password_reset_requests`
--
ALTER TABLE `parent_password_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `appointments_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_vaccine` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`Vaccine_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

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
