-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 07:05 PM
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
-- Database: `budget_financier`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `IdAmi` int(11) UNSIGNED NOT NULL,
  `Date` datetime NOT NULL,
  `RelationAccepte` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `user_id`, `IdAmi`, `Date`, `RelationAccepte`) VALUES
(1, 4, 2, '2026-06-19 18:47:03', 1),
(2, 2, 4, '2026-06-19 18:47:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `debts`
--

CREATE TABLE `debts` (
  `debt_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `friend_user_id` int(11) UNSIGNED NOT NULL,
  `transaction_id` int(11) UNSIGNED NOT NULL,
  `debt_type` enum('i_owe','they_owe') NOT NULL,
  `original_amount` decimal(10,2) NOT NULL,
  `remaining_amount` decimal(10,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `debts`
--

INSERT INTO `debts` (`debt_id`, `user_id`, `friend_user_id`, `transaction_id`, `debt_type`, `original_amount`, `remaining_amount`, `due_date`, `status`, `reason`) VALUES
(1, 4, 2, 1, 'they_owe', 15.00, 0.00, NULL, 'paid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `IdMessage` bigint(20) NOT NULL,
  `IdExpediteur` int(11) UNSIGNED NOT NULL,
  `IdDestinataire` int(11) UNSIGNED NOT NULL,
  `DateMessage` datetime NOT NULL DEFAULT current_timestamp(),
  `Message` varchar(280) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`IdMessage`, `IdExpediteur`, `IdDestinataire`, `DateMessage`, `Message`) VALUES
(1, 1, 2, '2026-06-19 18:47:03', 'orked wants to be your friend.'),
(2, 4, 2, '2026-06-19 18:47:58', 'orked: Reminder, please pay back the 15.00 € you still owe me. Thanks!'),
(3, 2, 4, '2026-06-19 18:48:15', 'sophea paid you 5.00 €. Remaining: 10.00 €.'),
(4, 4, 2, '2026-06-19 18:48:42', 'orked: Reminder, please pay back the 10.00 € you still owe me. Thanks!'),
(5, 2, 4, '2026-06-19 18:49:16', 'sophea corrected the payment: 4.00 € refunded (total paid now 1.00 €).'),
(6, 2, 4, '2026-06-19 18:49:48', 'sophea paid you 14.00 €. Remaining: 0.00 €.');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `split_status` varchar(50) DEFAULT NULL,
  `recurring_frequency` enum('daily','weekly','monthly','yearly') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `type`, `category`, `amount`, `transaction_date`, `is_recurring`, `description`, `split_status`, `recurring_frequency`) VALUES
(1, 4, 'expense', 'Groceries', 30.00, '2026-06-19', 0, 'bread', 'others', NULL),
(2, 2, 'expense', 'Debt Payment', 5.00, '2026-06-19', 0, 'Payment to Orked AHMAD', 'none', NULL),
(3, 4, 'income', 'Debt Repayment', 5.00, '2026-06-19', 0, 'Repayment from sophea', 'none', NULL),
(4, 2, 'income', 'Debt Payment', 4.00, '2026-06-19', 0, 'Refund from Orked AHMAD', 'none', NULL),
(5, 4, 'expense', 'Debt Repayment', 4.00, '2026-06-19', 0, 'Repayment refund to sophea', 'none', NULL),
(6, 2, 'expense', 'Debt Payment', 14.00, '2026-06-19', 0, 'Payment to Orked AHMAD', 'none', NULL),
(7, 4, 'income', 'Debt Repayment', 14.00, '2026-06-19', 0, 'Repayment from sophea', 'none', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `username` varchar(10) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone_number`, `username`, `password_hash`) VALUES
(1, 'Admin ADMINISTRATION', 'admin@budget.com', '', 'admin', '$2y$10$Dq7a7CYBLOXleQxoFJIQReUmg8tFD/Fog/IAh.huyudF658Zn0wE6'),
(2, 'Sophea NORIZAN', 'sophea@budget.com', '', 'sophea', '$2y$10$NIBnVRpevtiDuH/Oo8IwwOA5ozy6cuF/iBG5IK9mxMnqxUWlgUwy2'),
(3, 'Hazri MOHD MARLIZAN', 'hazri@budget.com', '', 'hazri', '$2y$10$w9hULEoongHwECwz1MoS4.r2Em78LKsc18eZd3arX7Ql3xQjH63ym'),
(4, 'Orked AHMAD', 'orked@budget.com', '', 'orked', '$2y$10$BPdIhLKpSMxGg5u9PR/AROZSw08v.NMoEGCCYBhShRX6YYOXEKkPm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `IdAmi` (`IdAmi`);

--
-- Indexes for table `debts`
--
ALTER TABLE `debts`
  ADD PRIMARY KEY (`debt_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_user_id` (`friend_user_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`IdMessage`),
  ADD KEY `IdExpediteur` (`IdExpediteur`),
  ADD KEY `IdDestinataire` (`IdDestinataire`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `debts`
--
ALTER TABLE `debts`
  MODIFY `debt_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `IdMessage` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contacts_ibfk_2` FOREIGN KEY (`IdAmi`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `debts`
--
ALTER TABLE `debts`
  ADD CONSTRAINT `debts_fk_friend` FOREIGN KEY (`friend_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `debts_fk_trans` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `debts_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`IdExpediteur`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`IdDestinataire`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
