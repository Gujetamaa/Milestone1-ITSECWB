-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 12, 2024 at 04:37 PM
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
-- Database: `kapekada`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(330) NOT NULL,
  `phoneNumber` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `wallet` decimal(10,2) DEFAULT 0.00,
  `address` varchar(255) DEFAULT NULL,
  `picture` varchar(255) NOT NULL,
  `login_attempts` int(11) DEFAULT NULL,
  `ban_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phoneNumber`, `password`, `role`, `wallet`, `address`, `picture`, `login_attempts`, `ban_time`) VALUES
(2, 'John Doe', 'john.doe@example.com', '9876543210', 'password123', 'User', 100.00, '456 Oak Street', '', NULL, NULL),
(54, 'Jane Smith', 'jane.smith@example.com', '5551234567', 'password456', 'User', 50.00, '789 Maple Avenue', '', NULL, NULL),
(101, 'Michael Johnson', 'michael.johnson@example.com', '1112223333', 'password789', 'User', 200.00, '101 Pine Road', '', NULL, NULL),
(115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL);

--
-- Indexes for dumped tables
--

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
