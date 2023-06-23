-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2023 at 08:32 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_records`
--

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `studentNo` varchar(8) NOT NULL,
  `ReferenceNo` varchar(4) NOT NULL,
  `Type` varchar(20) DEFAULT NULL,
  `Score` int(11) DEFAULT NULL,
  `Grade` enum('1.00','1.25','1.50','1.75','2.00','2.25','2.50','2.75','3.00','3.25','3.50','3.75','4.00','4.25','4.50','4.75','5.00','INC','UD') DEFAULT NULL,
  `DateTaken` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`studentNo`, `ReferenceNo`, `Type`, `Score`, `Grade`, `DateTaken`) VALUES
('20202222', '0000', 'exam', 89, '1.50', '2021-05-24'),
('20201234', '0100', 'exam', 25, '1.25', '2021-08-11'),
('20201234', '0200', 'quiz', 28, '1.25', '2022-05-22'),
('20201234', '0300', 'activity', 27, '1.25', '2022-01-08'),
('20201234', '0400', 'activity', 57, '1.00', '2022-03-11'),
('20201234', '0430', 'quiz', 34, '1.00', '2022-07-22'),
('20202222', '0500', 'quiz', 30, '1.25', '2023-05-05'),
('20202222', '0600', 'exam', 22, '1.00', '2023-05-10'),
('20203333', '0700', 'activity', 35, '1.25', '2023-05-06'),
('20205678', '0711', 'quiz', 40, '1.50', '2023-05-22'),
('20203333', '0800', 'quiz', 32, '1.25', '2023-05-15'),
('20205678', '0801', 'performance task', 95, '1.25', '2023-05-24'),
('20205678', '0802', 'reporting', 80, '1.50', '2023-05-24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('professor','student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'prof', '$2y$10$9WwiIf.n..UnJ5uJFpLkPuVVsVv3iBT7TFVMP4D0x8/cIrB0eP13m', 'professor'),
(2, '20201234', '$2y$10$1tllzeTLh8xW4g9b43DO3.DvoYb10AQg5Q.k94r2kk5oAf53XOTh6', 'student'),
(3, '20202222', '$2y$10$xRVnMOH8lFD.R3b.f31gD.TGB9GjQI8ZfGjpMBMTGb9BG1utGnp22', 'student'),
(4, '20203333', '$2y$10$XdDDaw9u8Ba/hJk76mZ09eX2.G4OmxqOB24bR8zdj6avdjkkDebY.', 'student'),
(5, '20205678', '$2y$10$PpjgjyIOEzK9h848IJOdSelADIvdvj71Ze1zrfkd6ADNBdvh3TP3O', 'student'),
(16, '12345678', '$2y$10$Dn/Q7CUdWqCOUShnNtdkfO8kVX/NvJf9bwD068cD.ROqwPAQSnznq', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`ReferenceNo`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
