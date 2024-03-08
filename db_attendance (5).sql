-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2024 at 10:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_events`
--

CREATE TABLE `tbl_events` (
  `event_id` int(3) NOT NULL,
  `event_name` varchar(50) NOT NULL,
  `event_description` varchar(100) NOT NULL,
  `event_venue` varchar(50) NOT NULL,
  `school_year_id` int(3) NOT NULL,
  `event_date` date NOT NULL,
  `fines` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_events`
--

INSERT INTO `tbl_events` (`event_id`, `event_name`, `event_description`, `event_venue`, `school_year_id`, `event_date`, `fines`) VALUES
(71, 'Present One', 'sdf', 'sdf', 4, '2024-03-05', 10.00),
(72, 'Present One', 'dfg', 'efd', 4, '2024-03-05', 10.00),
(73, 'late and time in only', 'dfg', 'dfg', 4, '2024-03-05', 10.00),
(74, 'duha kabook', 'asd', 'asd', 4, '2024-03-05', 0.00),
(75, 'try', '234', 'ert', 4, '2024-03-08', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_event_attendance`
--

CREATE TABLE `tbl_event_attendance` (
  `event_att_id` int(11) NOT NULL,
  `schedule_id` int(5) NOT NULL,
  `student_id` mediumint(9) NOT NULL,
  `att_status` varchar(10) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `time_late` time DEFAULT '00:00:00',
  `remarks` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_event_attendance`
--

INSERT INTO `tbl_event_attendance` (`event_att_id`, `schedule_id`, `student_id`, `att_status`, `time_in`, `time_out`, `time_late`, `remarks`) VALUES
(65, 71, 2077561, 'Present', '12:33:32', '12:35:20', '00:00:32', ''),
(66, 71, 2077860, '', '00:00:00', '00:00:00', '00:00:00', 'Excuse'),
(67, 72, 2077561, 'Present', '12:37:42', '12:39:58', '00:00:00', ''),
(68, 73, 2077561, 'Present', '12:40:51', '12:45:35', '00:00:00', ''),
(69, 73, 2075309, 'TimeInOnly', '12:41:05', '00:00:00', '00:00:05', ''),
(70, 75, 2077561, 'Present', '16:29:35', '16:31:47', '00:00:35', ''),
(71, 76, 2077561, 'TimeInOnly', '09:04:12', '00:00:00', '00:01:12', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_event_sched`
--

CREATE TABLE `tbl_event_sched` (
  `schedule_id` int(5) NOT NULL,
  `event_id` int(5) NOT NULL,
  `timein` time NOT NULL,
  `timeout` time NOT NULL,
  `phases` enum('morning','afternoon','n/a') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_event_sched`
--

INSERT INTO `tbl_event_sched` (`schedule_id`, `event_id`, `timein`, `timeout`, `phases`) VALUES
(71, 71, '12:33:00', '12:35:00', 'morning'),
(72, 72, '12:38:00', '12:39:00', 'morning'),
(73, 73, '12:41:00', '12:43:00', 'morning'),
(74, 74, '16:28:00', '16:29:00', 'morning'),
(75, 74, '16:29:00', '16:31:00', 'afternoon'),
(76, 75, '09:03:00', '09:05:00', 'morning');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_request_absence`
--

CREATE TABLE `tbl_request_absence` (
  `requestst_id` int(3) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `student_id` mediumint(9) NOT NULL,
  `status` varchar(10) NOT NULL,
  `event_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_request_time`
--

CREATE TABLE `tbl_request_time` (
  `request_id` int(3) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `schedule_id` int(3) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sbo`
--

CREATE TABLE `tbl_sbo` (
  `sbo_id` int(3) NOT NULL,
  `student_id` mediumint(9) NOT NULL,
  `position` varchar(50) NOT NULL,
  `profile_img` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sbo`
--

INSERT INTO `tbl_sbo` (`sbo_id`, `student_id`, `position`, `profile_img`) VALUES
(1, 2077561, 'Position 1', 0x38383739373738325f70305f6d6173746572313230302e6a7067),
(9, 2075309, 'Position 2', 0x3230302e676966),
(10, 2071341, 'Position 3', 0x6261636b6965652d3136393532332e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_school_year`
--

CREATE TABLE `tbl_school_year` (
  `school_year_id` int(2) NOT NULL,
  `semester` enum('1st semester','2nd semester') NOT NULL,
  `school_yearstart` year(4) DEFAULT NULL,
  `school_yearend` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_school_year`
--

INSERT INTO `tbl_school_year` (`school_year_id`, `semester`, `school_yearstart`, `school_yearend`) VALUES
(4, '1st semester', '2024', '2025'),
(5, '2nd semester', '2024', '2025');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_students`
--

CREATE TABLE `tbl_students` (
  `student_id` mediumint(9) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gmail` varchar(50) NOT NULL,
  `year_level` enum('1st','2nd','3rd','4th') DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_students`
--

INSERT INTO `tbl_students` (`student_id`, `first_name`, `last_name`, `gmail`, `year_level`, `status`) VALUES
(1111111, 'q', 'w', '1111111@g.cu.edu.ph', '2nd', 'Inactive'),
(2071341, 'Drahcir', 'Arancana', '2071341@g.cu.edu.ph', '4th', 'Active'),
(2072887, 'Wilsheane Angelo', 'Valmores', '2072887@g.cu.edu.ph', '4th', 'Inactive'),
(2075309, 'Marish', 'Framo', '2075309@g.cu.edu.ph', '4th', 'Active'),
(2077561, 'Kevin', 'Dinogyao', '2077561@g.cu.edu.ph', '4th', 'Active'),
(2077860, 'Resty', 'Obina', '2077860@g.cu.edu.ph', '4th', 'Active'),
(2078405, 'Genesis', 'Benedicto', '2078405@g.cu.edu.ph', '4th', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_user`
--

CREATE TABLE `tbl_system_user` (
  `user_id` bigint(20) NOT NULL DEFAULT current_timestamp(),
  `sbo_id` int(3) NOT NULL,
  `role` enum('Administrator','Attendance Manager','Event Manager') NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_system_user`
--

INSERT INTO `tbl_system_user` (`user_id`, `sbo_id`, `role`, `username`, `password`) VALUES
(20230824184953, 1, 'Administrator', 'antares', '$2y$10$6IPekjWVtyn.EsoKWCgCb.s9q4tBMoUPhbGM.vCClnW9arja.P5yu'),
(20230926200654, 9, 'Attendance Manager', 'admin', '$2y$10$BndmsSXbXnCeqERgZXBeOOP9VDXvkYRc0.xEEkOjHOe0eV2NbW7Si'),
(20240222204723, 10, 'Event Manager', 'admin123', '$2y$10$O7uwVyBCH/3ztCByvAJv.OV.7BEjjpC5BEcthe1c1JwA/Uu7DXtaK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_events`
--
ALTER TABLE `tbl_events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `tbl_event_attendance`
--
ALTER TABLE `tbl_event_attendance`
  ADD PRIMARY KEY (`event_att_id`);

--
-- Indexes for table `tbl_event_sched`
--
ALTER TABLE `tbl_event_sched`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `tbl_request_absence`
--
ALTER TABLE `tbl_request_absence`
  ADD PRIMARY KEY (`requestst_id`);

--
-- Indexes for table `tbl_request_time`
--
ALTER TABLE `tbl_request_time`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `tbl_sbo`
--
ALTER TABLE `tbl_sbo`
  ADD PRIMARY KEY (`sbo_id`);

--
-- Indexes for table `tbl_school_year`
--
ALTER TABLE `tbl_school_year`
  ADD PRIMARY KEY (`school_year_id`);

--
-- Indexes for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `tbl_system_user`
--
ALTER TABLE `tbl_system_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user` (`sbo_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_events`
--
ALTER TABLE `tbl_events`
  MODIFY `event_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tbl_event_attendance`
--
ALTER TABLE `tbl_event_attendance`
  MODIFY `event_att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_event_sched`
--
ALTER TABLE `tbl_event_sched`
  MODIFY `schedule_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `tbl_request_absence`
--
ALTER TABLE `tbl_request_absence`
  MODIFY `requestst_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tbl_request_time`
--
ALTER TABLE `tbl_request_time`
  MODIFY `request_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_sbo`
--
ALTER TABLE `tbl_sbo`
  MODIFY `sbo_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_school_year`
--
ALTER TABLE `tbl_school_year`
  MODIFY `school_year_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
