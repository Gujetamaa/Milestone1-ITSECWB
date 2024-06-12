-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 12, 2024 at 04:20 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

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
  `picture` varchar(255) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT NULL,
  `ban_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phoneNumber`, `password`, `role`, `wallet`, `address`, `picture`, `login_attempts`, `ban_time`) VALUES
(1, 'Administrator', 'admin@example.com', '1234567890', 'admin', 'Administrator', 500.00, '123 Admin Street', NULL, NULL, NULL),
(2, 'John Doe', 'john.doe@example.com', '9876543210', 'password123', 'User', 100.00, '456 Oak Street', NULL, NULL, NULL),
(54, 'Jane Smith', 'jane.smith@example.com', '5551234567', 'password456', 'User', 50.00, '789 Maple Avenue', NULL, NULL, NULL),
(101, 'Michael Johnson', 'michael.johnson@example.com', '1112223333', 'password789', 'User', 200.00, '101 Pine Road', NULL, NULL, NULL),
(148, 'Liza Gaela', 'liza16@gmail.com', '09176284624', '$2y$10$l.woC80hRzT5KGyV8hbCXeCHRRL8ylvPD3Xnhwhin/vxdzcMH1a/m', 'User', 100.00, 'Muntinlupa', '', 4, 1718114824),
(149, 'John Derick Endozo', 'user@cloudscapeblissfulelementalharmonyeverlastingserenitymysticalvoyagewhimsicaladventuretranquilityjourneydivineessenceeternalbeautyetherealauraenchantedwonderlandserendipitouswonderlovelyrhapsodyevergreenoasisenchantedgardenwhisperingbreezeenlightesexse.com', '09176284624', '$2y$10$e2DL1bHaYzXza6cHQ6iSUueDx58UhpaSWK3IeQ7aIlI13DmAxzlj6', 'User', 100.00, 'B1 L31 Phase 3C Citihomes Subdivision molino 4 bacoor cavite', '', NULL, NULL),
(150, 'John Derick Endozo', 'jane.doe.abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123@cloudscapeblissfulelementalharmonyeverlastingserenitymysticalvoyagewhimsicaladventuretranquilityjourneydivineessenceeternalbeautyetherealauraenchantedwonderlandserendipitouswonderlovelyrhapsodyevergreenoasisenchantedgardenwhisperingbreezeenlightesexse.com', '09176284624', '$2y$10$1Ji41aKaCBjcB3Yi6JmiOOLzSXxQLdZFVnvnyBkz2oOb7qT/y9Kpu', 'User', 100.00, 'B1 L31 Phase 3C Citihomes Subdivision molino 4 bacoor cavite', '', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
