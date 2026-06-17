-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 07:59 PM
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
(1, 3, 2, '2026-06-17 01:18:31', 1),
(2, 2, 3, '2026-06-17 01:20:59', 1),
(3, 4, 2, '2026-06-17 02:52:26', 1),
(4, 2, 4, '2026-06-17 02:52:38', 1),
(5, 5, 4, '2026-06-17 10:30:52', 1),
(6, 4, 5, '2026-06-17 10:31:28', 1),
(7, 6, 2, '2026-06-17 13:24:21', 1),
(8, 6, 3, '2026-06-17 13:24:23', 1),
(9, 6, 4, '2026-06-17 13:24:25', 1),
(10, 6, 5, '2026-06-17 13:24:36', 0),
(11, 2, 6, '2026-06-17 13:25:01', 1),
(12, 4, 6, '2026-06-17 13:25:14', 1),
(13, 3, 6, '2026-06-17 13:25:59', 1),
(14, 2, 5, '2026-06-17 17:47:57', 0);

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
(1, 2, 3, 7, 'they_owe', 7.50, 0.00, NULL, 'paid', NULL),
(2, 2, 3, 8, 'they_owe', 7.50, 0.00, NULL, 'paid', NULL),
(3, 2, 3, 9, 'they_owe', 1.00, 0.00, NULL, 'paid', NULL),
(4, 1, 1, 1, 'they_owe', 1.00, 1.00, NULL, 'pending', NULL),
(5, 3, 2, 10, 'they_owe', 15.00, 0.00, NULL, 'paid', NULL),
(6, 4, 2, 11, 'they_owe', 100.00, 0.00, NULL, 'paid', NULL),
(7, 2, 3, 14, 'they_owe', 150.00, 0.00, NULL, 'paid', NULL),
(8, 4, 5, 15, 'they_owe', 4.00, 4.00, NULL, 'pending', NULL),
(9, 4, 2, 15, 'they_owe', 6.00, 0.00, NULL, 'paid', NULL),
(13, 2, 6, 21, 'they_owe', 45.00, 0.00, NULL, 'paid', NULL),
(14, 2, 6, 36, 'they_owe', 5.00, 0.00, NULL, 'paid', NULL),
(15, 2, 6, 47, 'they_owe', 15.00, 0.00, NULL, 'paid', NULL),
(16, 2, 3, 52, 'they_owe', 20.00, 0.00, NULL, 'paid', NULL),
(17, 2, 6, 52, 'they_owe', 8.00, 8.00, NULL, 'pending', NULL);

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
(1, 1, 2, '2026-06-17 01:18:31', 'nonis wants to be your friend.'),
(2, 1, 2, '2026-06-17 02:52:26', 'sofiss wants to be your friend.'),
(3, 2, 3, '2026-06-17 02:54:38', 'pookers: Reminder, please pay back the 7.50 € you still owe me. Thanks!'),
(4, 2, 3, '2026-06-17 02:54:44', 'pookers: Reminder, please pay back the 1.00 € you still owe me. Thanks!'),
(5, 3, 2, '2026-06-17 03:03:40', 'nonis: Reminder, please pay back the 15.00 € you still owe me. Thanks!'),
(6, 3, 2, '2026-06-17 03:03:44', 'nonis: Reminder, please pay back the 15.00 € you still owe me. Thanks!'),
(7, 3, 2, '2026-06-17 03:03:45', 'nonis: Reminder, please pay back the 15.00 € you still owe me. Thanks!'),
(8, 3, 2, '2026-06-17 10:29:42', 'nonis: Reminder, please pay back the 15.00 € you still owe me. Thanks!'),
(9, 1, 4, '2026-06-17 10:30:52', 'ahmad wants to be your friend.'),
(10, 2, 4, '2026-06-17 11:04:50', 'pookers paid you 30.00 €. Remaining: 70.00 €.'),
(11, 2, 4, '2026-06-17 11:06:17', 'pookers paid you 70.00 €. Remaining: 0.00 €.'),
(12, 1, 2, '2026-06-17 13:24:21', 'anna1 wants to be your friend.'),
(13, 1, 3, '2026-06-17 13:24:23', 'anna1 wants to be your friend.'),
(14, 1, 4, '2026-06-17 13:24:25', 'anna1 wants to be your friend.'),
(15, 1, 5, '2026-06-17 13:24:36', 'anna1 wants to be your friend.'),
(16, 2, 6, '2026-06-17 13:38:19', 'pookers paid you 250.00 €. Remaining: 0.00 €.'),
(17, 2, 3, '2026-06-17 13:40:27', 'pookers paid you 15.00 €. Remaining: 0.00 €.'),
(18, 2, 4, '2026-06-17 13:40:32', 'pookers paid you 6.00 €. Remaining: 0.00 €.'),
(19, 3, 2, '2026-06-17 14:07:18', 'nonis paid you 7.50 €. Remaining: 0.00 €.'),
(20, 3, 2, '2026-06-17 14:07:24', 'nonis paid you 7.50 €. Remaining: 0.00 €.'),
(21, 3, 2, '2026-06-17 14:07:28', 'nonis paid you 1.00 €. Remaining: 0.00 €.'),
(22, 3, 2, '2026-06-17 14:07:34', 'nonis paid you 150.00 €. Remaining: 0.00 €.'),
(23, 6, 2, '2026-06-17 14:10:04', 'anna1 paid you 45.00 €. Remaining: 0.00 €.'),
(24, 3, 6, '2026-06-17 14:10:41', 'nonis paid you 250.00 €. Remaining: 0.00 €.'),
(25, 4, 6, '2026-06-17 14:11:35', 'sofiss paid you 50.00 €. Remaining: 200.00 €.'),
(26, 6, 2, '2026-06-17 17:36:32', 'anna1 paid you 1.00 €. Remaining: 4.00 €.'),
(27, 6, 2, '2026-06-17 17:37:49', 'anna1 paid you 2.00 €. Remaining: 2.00 €.'),
(28, 6, 2, '2026-06-17 17:38:00', 'anna1 paid you 0.50 €. Remaining: 1.50 €.'),
(29, 6, 2, '2026-06-17 17:38:10', 'anna1 paid you 1.00 €. Remaining: 0.50 €.'),
(30, 6, 2, '2026-06-17 17:38:34', 'anna1 paid you 0.50 €. Remaining: 0.00 €.'),
(31, 6, 2, '2026-06-17 17:41:31', 'anna1 paid you 7.00 €. Remaining: 8.00 €.'),
(32, 6, 2, '2026-06-17 17:42:30', 'anna1 paid you 8.00 €. Remaining: 0.00 €.'),
(33, 3, 2, '2026-06-17 17:46:35', 'nonis paid you 20.00 €. Remaining: 0.00 €.'),
(34, 1, 5, '2026-06-17 17:47:57', 'pookers wants to be your friend.'),
(35, 2, 6, '2026-06-17 17:49:57', 'pookers: Reminder, please pay back the 8.00 € you still owe me. Thanks!'),
(36, 2, 6, '2026-06-17 17:58:31', 'pookers: Reminder, please pay back the 8.00 € you still owe me. Thanks!');

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
(1, 1, 'expense', 'Groceries', 10.00, '2026-06-15', 0, 'Chicken Street', 'none', NULL),
(2, 2, 'expense', 'Groceries', 6.01, '2026-06-17', 0, 'sausage', 'others', NULL),
(3, 2, 'expense', 'Rent', 410.00, '2026-06-18', 0, 'rent', 'none', NULL),
(4, 2, 'income', 'Income', 1130.00, '2026-06-18', 0, 'mara', 'none', NULL),
(5, 2, 'expense', 'Entertainment', 11.00, '2026-06-17', 0, 'movie', 'others', NULL),
(6, 2, 'expense', 'Entertainment', 13.00, '2026-06-14', 0, 'flower', 'others', NULL),
(7, 2, 'expense', 'Entertainment', 15.00, '2026-06-17', 0, 'car', 'others', NULL),
(8, 2, 'expense', 'Entertainment', 15.00, '2026-06-01', 0, 'car', 'others', NULL),
(9, 2, 'expense', 'Entertainment', 2.00, '2026-06-20', 0, 'pen', 'others', NULL),
(10, 3, 'expense', 'Entertainment', 30.00, '2026-06-04', 0, 'paris', 'others', NULL),
(11, 4, 'expense', 'Entertainment', 200.00, '2026-06-02', 0, 'weeknd', 'others', NULL),
(13, 3, 'expense', 'Rent', 23.00, '2026-06-01', 1, 'wifi', 'none', 'monthly'),
(14, 2, 'expense', 'Rent', 300.00, '2026-06-03', 0, 'rent', 'others', NULL),
(15, 4, 'expense', 'Entertainment', 13.00, '2026-06-17', 0, 'fr', 'others', NULL),
(18, 2, 'expense', 'Debt Payment', 250.00, '2026-06-17', 0, 'Payment to Anna CAO', 'none', NULL),
(19, 2, 'expense', 'Debt Payment', 15.00, '2026-06-17', 0, 'Payment to Ainun HKASSIM', 'none', NULL),
(20, 2, 'expense', 'Debt Payment', 6.00, '2026-06-17', 0, 'Payment to Sofia ALHADY', 'none', NULL),
(21, 2, 'expense', 'Entertainment', 90.00, '2026-05-05', 0, 'dolomites', 'others', NULL),
(22, 3, 'expense', 'Debt Payment', 7.50, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(23, 2, 'income', 'Debt Payment', 7.50, '2026-06-17', 0, 'Payment from nonis', 'none', NULL),
(24, 3, 'expense', 'Debt Payment', 7.50, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(25, 2, 'income', 'Debt Payment', 7.50, '2026-06-17', 0, 'Payment from nonis', 'none', NULL),
(26, 3, 'expense', 'Debt Payment', 1.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(27, 2, 'income', 'Debt Payment', 1.00, '2026-06-17', 0, 'Payment from nonis', 'none', NULL),
(28, 3, 'expense', 'Debt Payment', 150.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(29, 2, 'income', 'Debt Payment', 150.00, '2026-06-17', 0, 'Payment from nonis', 'none', NULL),
(31, 2, 'income', 'Debt Payment', 45.00, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(32, 3, 'expense', 'Debt Payment', 250.00, '2026-06-17', 0, 'Payment to Anna CAO', 'none', NULL),
(34, 4, 'expense', 'Debt Payment', 50.00, '2026-06-17', 0, 'Payment to Anna CAO', 'none', NULL),
(36, 2, 'expense', 'Groceries', 10.00, '2026-05-01', 0, 'kicap', 'others', NULL),
(37, 6, 'expense', 'Debt Payment', 1.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(38, 2, 'income', 'Debt Payment', 1.00, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(39, 6, 'expense', 'Debt Payment', 2.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(40, 2, 'income', 'Debt Payment', 2.00, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(41, 6, 'expense', 'Debt Payment', 0.50, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(42, 2, 'income', 'Debt Payment', 0.50, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(43, 6, 'expense', 'Debt Payment', 1.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(44, 2, 'income', 'Debt Payment', 1.00, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(45, 6, 'expense', 'Debt Payment', 0.50, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(46, 2, 'income', 'Debt Payment', 0.50, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(47, 2, 'expense', 'Groceries', 30.00, '2026-06-17', 0, 'bawang', 'others', NULL),
(48, 6, 'expense', 'Debt Payment', 7.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(49, 2, 'income', 'Debt Payment', 7.00, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(50, 6, 'expense', 'Debt Payment', 8.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(51, 2, 'income', 'Debt Payment', 8.00, '2026-06-17', 0, 'Payment from anna1', 'none', NULL),
(52, 2, 'expense', 'Groceries', 40.00, '2026-06-17', 0, 'cili', 'others', NULL),
(53, 3, 'expense', 'Debt Payment', 20.00, '2026-06-17', 0, 'Payment to Crumbly Pumps', 'none', NULL),
(54, 2, 'income', 'Debt Payment', 20.00, '2026-06-17', 0, 'Payment from nonis', 'none', NULL),
(55, 2, 'expense', 'Groceries', 6.00, '2026-06-17', 0, 'chocolate', 'none', NULL);

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
(1, 'admin', 'admin@yahoo.com', '0623661233', 'admin', '$2y$10$r.4fvQ/apaKWFFHJrfbDC.8ooMeqEo7F6UuWWeq32zz5bRgRTFuMC'),
(2, 'Crumbly Pumps', 'pooks@budbud.com', '', 'pookers', '$2y$10$zKLQ8h7UfffXm2P22zWiJewWNrm8TXoG9n0DZWdUNpNjEVG8dtcwW'),
(3, 'Ainun HKASSIM', 'ainun@cuicui.com', '', 'nonis', '$2y$10$3ZAK3LeGyz4DSL.RKQxwH.TrRLis0PxE9C2VaviflMdRp68LGRL3C'),
(4, 'Sofia ALHADY', 'sofia@clairo.com', '', 'sofiss', '$2y$10$ChmigTrQnTSD7GawwQI8Aejd6aEVy2TJG6k38y8bBcl5p.UCB6nf6'),
(5, 'ahmad AHMAD', 'ahmad@budbud.com', '', 'ahmad', '$2y$10$Q1ECwjQUlX9h/9RrfszNBOX7Qxwr9t9q99ei3uuLVX0eYFuHAPqNC'),
(6, 'Anna CAO', 'anna@frozen.com', '', 'anna1', '$2y$10$rqa4i1XrcOiKkpvDpgQLF.oZRBxx7ETqhlFs.D8R1cnYHff0rbgd2');

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
  MODIFY `contact_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `debts`
--
ALTER TABLE `debts`
  MODIFY `debt_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `IdMessage` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
